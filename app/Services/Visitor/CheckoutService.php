<?php

namespace App\Services\Visitor;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Admin\CustomerService;
use App\Services\Admin\SettingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        private SettingService $settingService,
        private CustomerService $customerService
    ) {}

    /**
     * @param  array{
     *   address_id: ?int, shipping_name: ?string, shipping_email: ?string,
     *   shipping_phone: ?string, shipping_address: ?string, shipping_city: ?string,
     *   shipping_state: ?string, shipping_zip: ?string, shipping_country: ?string,
     *   payment_method: string, coupon_code: ?string, notes: ?string
     * }  $data
     */
    public function placeOrder(Cart $cart, ?User $customer, array $data): Order
    {
        if ($cart->items->isEmpty()) {
            throw new \Exception('Your cart is empty.');
        }

        return DB::transaction(function () use ($cart, $customer, $data) {

            $shipping = $this->resolveShippingDetails($customer, $data);

            // Guests get a real account so they can track their order later.
            // Existing customers are left untouched.
            if (! $customer) {
                $customer = $this->resolveOrCreateCustomer($shipping);
            }

            [$items, $subtotal] = $this->buildItemsWithStockLock($cart);

            $shippingCharge = round(
                $this->settingService->resolveShippingCharge($shipping['shipping_city'], $subtotal),
                2
            );
            $taxAmount = $this->resolveTaxAmount($subtotal);

            $coupon = null;
            $discountAmount = 0.0;

            if (! empty($data['coupon_code'])) {
                $coupon = $this->validateAndLockCoupon($data['coupon_code'], $customer, $subtotal);
                $discountAmount = $this->calculateCouponDiscount($coupon, $subtotal);
            }

            $totalAmount = max(0, round($subtotal - $discountAmount + $shippingCharge + $taxAmount, 2));

            $order = Order::create([
                'order_number' => $this->generateUniqueOrderNumber(),
                'user_id' => $customer->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_charge' => $shippingCharge,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'coupon_code' => $coupon?->code,
                'coupon_id' => $coupon?->id,
                'payment_method' => $data['payment_method'],
                ...$shipping,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'product_image' => $item['product_image'],
                    'variant_options' => $item['variant_options'],
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'total_price' => $item['total_price'],
                ]);

                $item['variant']->decrementStock($item['quantity']);
                $item['product']->increment('total_sales', $item['quantity']);
                $item['product']->refreshPriceAndStockCache();
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'from_status' => null,
                'to_status' => 'pending',
                'changed_by' => null,
                'note' => 'Order placed by customer.',
            ]);

            $cart->items()->delete();

            Log::info('Storefront order placed.', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $customer->id,
            ]);

            return $order->fresh(['items', 'user', 'coupon']);
        });
    }

    // ─────────────────────────────────────────────
    // GUEST → ACCOUNT RESOLUTION
    // Finds an existing account by the shipping email (so a returning guest
    // doesn't get duplicate accounts), or creates a new one with an unusable
    // random password + sends the same "set password" email flow admin-created
    // customers already use, so the guest can log in and track this order later.
    // ─────────────────────────────────────────────

    private function resolveOrCreateCustomer(array $shipping): User
    {
        $email = $shipping['shipping_email'];

        $existing = User::where('email', $email)->first();
        if ($existing) {
            return $existing;
        }

        $customer = User::create([
            'name' => $shipping['shipping_name'],
            'email' => $email,
            'phone' => $shipping['shipping_phone'] ?? null,
            'password' => Hash::make(Str::random(40)),
            'status' => 'active',
            'email_verified_at' => now(), // trusted: they'll verify identity by setting a password via emailed link
        ]);

        Log::info('Guest checkout auto-created customer account.', ['user_id' => $customer->id]);

        try {
            $this->customerService->sendSetPasswordEmail($customer);
        } catch (\Exception $e) {
            // Don't let a mail failure block order placement — the account
            // still exists and the customer can use "Forgot Password" later.
            Log::error('Failed to send set-password email after guest checkout.', [
                'user_id' => $customer->id,
                'exception' => $e,
            ]);
        }

        return $customer;
    }

    // ─────────────────────────────────────────────
    // SHIPPING DETAILS RESOLUTION
    // ─────────────────────────────────────────────

    private function resolveShippingDetails(?User $customer, array $data): array
    {
        if (! empty($data['address_id'])) {
            $address = Address::where('id', $data['address_id'])
                ->when($customer, fn ($q) => $q->where('user_id', $customer->id))
                ->first();

            if (! $address) {
                throw new \Exception('Selected address is invalid.');
            }

            return [
                'shipping_name' => $address->recipient_name,
                'shipping_email' => $customer?->email ?? $data['shipping_email'] ?? '',
                'shipping_phone' => $address->recipient_phone,
                'shipping_address' => trim($address->address_line_1.' '.$address->address_line_2),
                'shipping_city' => $address->city,
                'shipping_state' => $address->state,
                'shipping_zip' => $address->zip_code,
                'shipping_country' => $address->country,
            ];
        }

        return [
            'shipping_name' => $data['shipping_name'],
            'shipping_email' => $data['shipping_email'],
            'shipping_phone' => $data['shipping_phone'],
            'shipping_address' => $data['shipping_address'],
            'shipping_city' => $data['shipping_city'],
            'shipping_state' => $data['shipping_state'],
            'shipping_zip' => $data['shipping_zip'],
            'shipping_country' => $data['shipping_country'],
        ];
    }

    // ─────────────────────────────────────────────
    // STOCK LOCKING (mirrors OrderService::buildItemsWithStockLock)
    // ─────────────────────────────────────────────

    private function buildItemsWithStockLock(Cart $cart): array
    {
        $items = [];
        $subtotal = 0.0;

        foreach ($cart->items as $cartItem) {
            $variant = ProductVariant::where('id', $cartItem->product_variant_id)->lockForUpdate()->first();

            if (! $variant || ! $variant->is_active) {
                throw new \Exception('One of the items in your cart is no longer available.');
            }

            if ($variant->stock_quantity < $cartItem->quantity) {
                throw new \Exception("Only {$variant->stock_quantity} unit(s) left for \"{$variant->product->name}\".");
            }

            $unitPrice = $variant->final_price;
            $totalPrice = round($unitPrice * $cartItem->quantity, 2);
            $subtotal += $totalPrice;

            $items[] = [
                'product' => $variant->product,
                'variant' => $variant,
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'product_sku' => $variant->sku,
                'product_image' => $variant->thumbnail ?? $variant->product->thumbnail,
                'variant_options' => $variant->options_label,
                'unit_price' => $unitPrice,
                'quantity' => $cartItem->quantity,
                'total_price' => $totalPrice,
            ];
        }

        return [$items, round($subtotal, 2)];
    }

    // ─────────────────────────────────────────────
    // TAX (mirrors OrderService::resolveTaxAmount, non-override branch only)
    // ─────────────────────────────────────────────

    private function resolveTaxAmount(float $subtotal): float
    {
        $taxSettings = $this->settingService->getGroup('tax');

        if (($taxSettings['tax_enabled'] ?? '0') !== '1') {
            return 0.0;
        }

        $rate = (float) ($taxSettings['tax_rate'] ?? 0);
        if ($rate <= 0) {
            return 0.0;
        }

        $pricesIncludeTax = ($taxSettings['prices_include_tax'] ?? '0') === '1';

        if ($pricesIncludeTax) {
            return round($subtotal - ($subtotal / (1 + ($rate / 100))), 2);
        }

        return round($subtotal * ($rate / 100), 2);
    }

    // ─────────────────────────────────────────────
    // COUPON (mirrors OrderService validation, scoped to storefront customer)
    // ─────────────────────────────────────────────

    private function validateAndLockCoupon(string $code, ?User $customer, float $subtotal): Coupon
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->lockForUpdate()->first();

        if (! $coupon) {
            throw new \Exception("Coupon \"{$code}\" does not exist.");
        }

        if (! $coupon->isCurrentlyValid()) {
            throw new \Exception("Coupon \"{$coupon->code}\" is not currently valid.");
        }

        if ($subtotal < (float) $coupon->minimum_order_amount) {
            throw new \Exception("This coupon requires a minimum order of {$coupon->minimum_order_amount}.");
        }

        if ($customer) {
            $usedByCustomer = Order::where('user_id', $customer->id)
                ->where('coupon_id', $coupon->id)
                ->whereNotIn('status', ['cancelled'])
                ->count();

            if ($usedByCustomer >= $coupon->usage_per_user) {
                throw new \Exception('You have already used this coupon the maximum number of times.');
            }
        }

        return $coupon;
    }

    public function previewCoupon(string $code, ?User $customer, float $subtotal): array
    {
        $coupon = $this->validateAndLockCoupon($code, $customer, $subtotal);
        $discount = $this->calculateCouponDiscount($coupon, $subtotal);

        return [
            'code' => $coupon->code,
            'discount_amount' => $discount,
        ];
    }

    private function calculateCouponDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === 'fixed') {
            return round(min((float) $coupon->value, $subtotal), 2);
        }

        $discount = $subtotal * ((float) $coupon->value / 100);

        if ($coupon->maximum_discount) {
            $discount = min($discount, (float) $coupon->maximum_discount);
        }

        return round(min($discount, $subtotal), 2);
    }

    // ─────────────────────────────────────────────
    // ORDER NUMBER (mirrors OrderService::generateUniqueOrderNumber)
    // ─────────────────────────────────────────────

    private function generateUniqueOrderNumber(): string
    {
        $prefix = $this->settingService->getGroup('order')['order_number_prefix'] ?? 'ORD-';

        $lastNumber = Order::where('order_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderBy('order_number', 'desc')
            ->value('order_number');

        $next = 1;
        if ($lastNumber) {
            $next = (int) substr($lastNumber, strrpos($lastNumber, '-') !== false ? strrpos($lastNumber, '-') + 1 : strlen($prefix)) + 1;
        }

        return $prefix.str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    // ─────────────────────────────────────────────
    // SUMMARY FOR THE CHECKOUT VIEW
    // ─────────────────────────────────────────────

    public function getCheckoutSummary(Cart $cart): array
    {
        $subtotal = round($cart->items->sum('subtotal'), 2);

        return [
            'subtotal' => $subtotal,
            'shipping_charge_estimate' => $this->settingService->resolveShippingCharge(null, $subtotal),
            'tax_amount_estimate' => $this->resolveTaxAmount($subtotal),
        ];
    }
}
