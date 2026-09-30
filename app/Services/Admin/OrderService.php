<?php

namespace App\Services\Admin;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Shared\PricingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private PricingService $pricingService
    ) {}
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Order
    {
        try {
            return DB::transaction(function () use ($data) {

                $items = $this->buildItemsWithStockLock($data['items']);
                $subtotal = collect($items)->sum('total_price');

                $shippingCharge = $this->resolveShippingCharge($data, $subtotal);
                $taxAmount = $this->resolveTaxAmount($data, $subtotal);

                $coupon = null;
                $discountAmount = (float) ($data['discount_amount'] ?? 0);

                if (! empty($data['coupon_code'])) {
                    $coupon = $this->pricingService->validateAndLockCoupon($data['coupon_code'], $data['user_id'], $subtotal);
                    $discountAmount = $this->pricingService->calculateCouponDiscount($coupon, $subtotal);
                }

                $order = Order::create([
                    'order_number' => $this->pricingService->generateUniqueOrderNumber(),
                    'user_id' => $data['user_id'],
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'shipping_charge' => $shippingCharge,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $this->pricingService->calculateTotal($subtotal, $discountAmount, $shippingCharge, $taxAmount),
                    'coupon_code' => $coupon?->code,
                    'coupon_id' => $coupon?->id,
                    'shipping_name' => $data['shipping_name'],
                    'shipping_email' => $data['shipping_email'],
                    'shipping_phone' => $data['shipping_phone'],
                    'shipping_address' => $data['shipping_address'],
                    'shipping_city' => $data['shipping_city'],
                    'shipping_state' => $data['shipping_state'],
                    'shipping_zip' => $data['shipping_zip'],
                    'shipping_country' => $data['shipping_country'],
                    'notes' => $data['notes'] ?? null,
                ]);

                $this->persistItemsAndAdjustStock($order, $items, decrement: true);

                if ($coupon) {
                    $coupon->increment('used_count');
                }

                $this->recordStatusHistory($order, null, 'pending', 'Order placed.');

                Log::info('Order created successfully.', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);

                return $order->fresh(['items', 'user', 'coupon']);
            });
        } catch (\Exception $e) {
            Log::error('Order creation failed.', ['exception' => $e]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Order $order, array $data): Order
    {
        if (! $order->isEditable()) {
            throw new \Exception('This order can no longer be edited because of its current status.');
        }

        try {
            return DB::transaction(function () use ($order, $data) {

                $this->restoreStockForItems($order->items);
                $order->items()->delete();

                $items = $this->buildItemsWithStockLock($data['items']);
                $subtotal = collect($items)->sum('total_price');

                $shippingCharge = $this->resolveShippingCharge($data, $subtotal);
                $taxAmount = $this->resolveTaxAmount($data, $subtotal);

                $previousCouponId = $order->coupon_id;
                $coupon = null;
                $discountAmount = (float) ($data['discount_amount'] ?? 0);

                if (! empty($data['coupon_code'])) {
                    $coupon = $this->pricingService->validateAndLockCoupon($data['coupon_code'], $data['user_id'], $subtotal, ignoreOrderId: $order->id);
                    $discountAmount = $this->pricingService->calculateCouponDiscount($coupon, $subtotal);
                }

                // Coupon changed (removed or swapped) — release the old one, consume the new one.
                if ($previousCouponId && $previousCouponId !== $coupon?->id) {
                    Coupon::where('id', $previousCouponId)->where('used_count', '>', 0)->decrement('used_count');
                }
                if ($coupon && $coupon->id !== $previousCouponId) {
                    $coupon->increment('used_count');
                }

                $order->update([
                    'user_id' => $data['user_id'],
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'shipping_charge' => $shippingCharge,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $this->pricingService->calculateTotal($subtotal, $discountAmount, $shippingCharge, $taxAmount),
                    'coupon_code' => $coupon?->code,
                    'coupon_id' => $coupon?->id,
                    'shipping_name' => $data['shipping_name'],
                    'shipping_email' => $data['shipping_email'],
                    'shipping_phone' => $data['shipping_phone'],
                    'shipping_address' => $data['shipping_address'],
                    'shipping_city' => $data['shipping_city'],
                    'shipping_state' => $data['shipping_state'],
                    'shipping_zip' => $data['shipping_zip'],
                    'shipping_country' => $data['shipping_country'],
                    'notes' => $data['notes'] ?? null,
                ]);

                $this->persistItemsAndAdjustStock($order, $items, decrement: true);

                Log::info('Order updated successfully.', ['order_id' => $order->id]);

                return $order->fresh(['items', 'user', 'coupon']);
            });
        } catch (\Exception $e) {
            Log::error('Order update failed.', ['exception' => $e, 'order_id' => $order->id]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // STATUS TRANSITIONS
    // ─────────────────────────────────────────────

    public function updateStatus(Order $order, string $status, ?string $note = null): Order
    {
        if (! in_array($status, Order::STATUSES)) {
            throw new \Exception('Invalid order status.');
        }

        if ($status === $order->status) {
            throw new \Exception('Order is already in this status.');
        }

        if ($status === 'cancelled') {
            return $this->cancel($order, $note);
        }

        return DB::transaction(function () use ($order, $status, $note) {
            $previousStatus = $order->status;
            $update = ['status' => $status];

            if ($status === 'shipped') {
                $update['shipped_at'] = now();
            }
            if ($status === 'delivered') {
                $update['delivered_at'] = now();
            }

            if ($status === 'refunded' && $order->isCancellable()) {
                $this->restoreStockForItems($order->items);
            }

            $order->update($update);

            $this->recordStatusHistory($order, $previousStatus, $status, $note);

            Log::info('Order status updated.', ['order_id' => $order->id, 'from' => $previousStatus, 'to' => $status]);

            return $order->fresh();
        });
    }

    public function cancel(Order $order, ?string $reason): Order
    {
        if (! $order->isCancellable()) {
            throw new \Exception('This order cannot be cancelled from its current status.');
        }

        return DB::transaction(function () use ($order, $reason) {
            $previousStatus = $order->status;

            $this->restoreStockForItems($order->items);

            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => $reason,
            ]);

            $this->recordStatusHistory($order, $previousStatus, 'cancelled', $reason);

            Log::info('Order cancelled.', ['order_id' => $order->id, 'reason' => $reason]);

            return $order->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Order $order): bool
    {
        if (! $order->isDeletable()) {
            throw new \Exception('Only cancelled or refunded orders can be deleted.');
        }

        return $order->delete();
    }

    // ─────────────────────────────────────────────
    // QUICK CUSTOMER CREATE (POS convenience —
    // stopgap until customer self-registration ships)
    // ─────────────────────────────────────────────

    public function quickCreateCustomer(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make(Str::random(24)),
            'status' => 'active',
        ]);
    }

    // ─────────────────────────────────────────────
    // STOCK / ITEM HELPERS
    // ─────────────────────────────────────────────

    /**
     * Locks each product row, validates stock, and builds item
     * payloads with server-trusted pricing. Does NOT persist or
     * mutate stock yet — call persistItemsAndAdjustStock() after.
     */
    private function buildItemsWithStockLock(array $submittedItems): array
    {
        $items = [];

        $merged = [];
        foreach ($submittedItems as $row) {
            $pid = (int) $row['product_id'];
            $merged[$pid] = ($merged[$pid] ?? 0) + (int) $row['quantity'];
        }

        foreach ($merged as $productId => $quantity) {
            $product = Product::where('id', $productId)->lockForUpdate()->first();

            if (! $product) {
                throw new \Exception("Product #{$productId} no longer exists.");
            }

            $variant = $product->variants()->where('is_default', true)->lockForUpdate()->first()
                ?? $product->variants()->lockForUpdate()->first();

            if (! $variant) {
                throw new \Exception("\"{$product->name}\" has no purchasable variant.");
            }

            if ($variant->stock_quantity < $quantity) {
                throw new \Exception("Insufficient stock for \"{$product->name}\" (available: {$variant->stock_quantity}, requested: {$quantity}).");
            }

            $items[] = $this->pricingService->buildOrderItemPayload($product, $variant, $quantity);
        }

        return $items;
    }

    // ─────────────────────────────────────────────
    // SETTINGS-DRIVEN RESOLUTION (shipping / tax)
    // Admin form fields are treated as an override: if the admin
    // typed a value, it's respected. If left blank, it's computed
    // from the store's configured Shipping/Tax settings.
    // ─────────────────────────────────────────────

    private function resolveShippingCharge(array $data, float $subtotal): float
    {
        if (array_key_exists('shipping_charge', $data) && $data['shipping_charge'] !== null && $data['shipping_charge'] !== '') {
            return round((float) $data['shipping_charge'], 2);
        }

        return $this->pricingService->resolveShippingCharge($data['shipping_city'] ?? null, $subtotal);
    }

    private function resolveTaxAmount(array $data, float $subtotal): float
    {
        if (array_key_exists('tax_amount', $data) && $data['tax_amount'] !== null && $data['tax_amount'] !== '') {
            return round((float) $data['tax_amount'], 2);
        }

        return $this->pricingService->calculateTax($subtotal);
    }

    private function persistItemsAndAdjustStock(Order $order, array $items, bool $decrement): void
    {
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

            if ($decrement) {
                /** @var ProductVariant $variant */
                $variant = $item['variant'];
                $variant->decrementStock($item['quantity']);

                /** @var Product $product */
                $product = $item['product'];
                $product->increment('total_sales', $item['quantity']);
                $product->refreshPriceAndStockCache();
            }
        }
    }

    private function restoreStockForItems($items): void
    {
        foreach ($items as $item) {
            $variant = ProductVariant::where('id', $item->product_variant_id)->lockForUpdate()->first();

            if ($variant) {
                $variant->incrementStock($item->quantity);
                $variant->product?->refreshPriceAndStockCache();
            }

            $product = Product::where('id', $item->product_id)->first();
            $product?->decrement('total_sales', min($item->quantity, $product->total_sales));
        }
    }

    private function recordStatusHistory(Order $order, ?string $fromStatus, string $toStatus, ?string $note = null): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by' => Auth::guard('admin')->id(),
            'note' => $note,
        ]);
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getOrdersList(array $filters = [])
    {
        $query = Order::with('user')->withCount('items');

        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (! empty($filters['status'])) {
            $query->ofStatus($filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    public function getOrderStats(): array
    {
        $counts = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'total' => (int) $counts->sum(),
            'pending' => (int) ($counts->get('pending') ?? 0),
            'completed' => (int) ($counts->get('delivered') ?? 0),
            'cancelled' => (int) ($counts->get('cancelled') ?? 0),
            'returned' => (int) ($counts->get('refunded') ?? 0),
        ];
    }

    public function getOrderForEdit(Order $order): Order
    {
        return $order->load(['items.product', 'user']);
    }

    public function getOrderForDetails(Order $order): Order
    {
        return $order->load(['items.product', 'user', 'statusHistories.admin']);
    }
}
