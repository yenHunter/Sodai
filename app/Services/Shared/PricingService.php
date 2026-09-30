<?php

namespace App\Services\Shared;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Admin\SettingService;

/**
 * Single source of truth for order money math and pricing-adjacent
 * rules shared by the admin POS (OrderService) and the storefront
 * checkout (CheckoutService): totals, settings-driven tax/shipping,
 * coupon validation + discount calculation, order item snapshots,
 * and order-number generation.
 */
class PricingService
{
    public function __construct(
        private SettingService $settingService
    ) {}

    // ─────────────────────────────────────────────
    // TOTALS
    // ─────────────────────────────────────────────

    public function calculateTotal(float $subtotal, float $discountAmount, float $shippingCharge, float $taxAmount): float
    {
        return max(0, round($subtotal - $discountAmount + $shippingCharge + $taxAmount, 2));
    }

    /**
     * Settings-driven tax for a subtotal. Callers that allow a manual
     * override (admin POS form) check their override field first and
     * only fall back to this.
     */
    public function calculateTax(float $subtotal): float
    {
        $taxSettings = $this->settingService->getGroup('tax');

        if (($taxSettings['tax_enabled'] ?? '0') !== '1') {
            return 0.0;
        }

        $rate = (float) ($taxSettings['tax_rate'] ?? 0);
        $pricesIncludeTax = ($taxSettings['prices_include_tax'] ?? '0') === '1';

        if ($rate <= 0) {
            return 0.0;
        }

        // If prices already include tax, the tax line is informational only
        // (already baked into subtotal) — don't add it again on top.
        if ($pricesIncludeTax) {
            return round($subtotal - ($subtotal / (1 + ($rate / 100))), 2);
        }

        return round($subtotal * ($rate / 100), 2);
    }

    /**
     * Settings-driven shipping charge (free-threshold → in-area → out-of-area).
     */
    public function resolveShippingCharge(?string $city, float $subtotal = 0.0): float
    {
        return round($this->settingService->resolveShippingCharge($city, $subtotal), 2);
    }

    // ─────────────────────────────────────────────
    // COUPONS
    // ─────────────────────────────────────────────

    /**
     * Locks the coupon row and validates it end-to-end.
     *
     * $userId may be null (guest checkout) — the per-customer usage
     * check is skipped. $ignoreOrderId excludes the order currently
     * being edited from the usage count (admin order updates).
     */
    public function validateAndLockCoupon(string $code, ?int $userId, float $subtotal, ?int $ignoreOrderId = null): Coupon
    {
        $coupon = Coupon::where('code', strtoupper(trim($code)))->lockForUpdate()->first();

        if (! $coupon) {
            throw new \Exception("Coupon \"{$code}\" does not exist.");
        }

        if (! $coupon->isCurrentlyValid()) {
            throw new \Exception("Coupon \"{$coupon->code}\" is not currently valid (inactive, expired, not yet started, or usage limit reached).");
        }

        if ($subtotal < (float) $coupon->minimum_order_amount) {
            throw new \Exception("Coupon \"{$coupon->code}\" requires a minimum order amount of {$coupon->minimum_order_amount}.");
        }

        if ($userId !== null) {
            $usedByCustomer = Order::where('user_id', $userId)
                ->where('coupon_id', $coupon->id)
                ->when($ignoreOrderId, fn ($q) => $q->where('id', '!=', $ignoreOrderId))
                ->whereNotIn('status', ['cancelled'])
                ->count();

            if ($usedByCustomer >= $coupon->usage_per_user) {
                throw new \Exception("This customer has already used coupon \"{$coupon->code}\" the maximum number of times.");
            }
        }

        return $coupon;
    }

    public function calculateCouponDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === 'fixed') {
            return round(min((float) $coupon->value, $subtotal), 2);
        }

        // percentage
        $discount = $subtotal * ((float) $coupon->value / 100);

        if ($coupon->maximum_discount) {
            $discount = min($discount, (float) $coupon->maximum_discount);
        }

        return round(min($discount, $subtotal), 2);
    }

    // ─────────────────────────────────────────────
    // ORDER NUMBER
    // ─────────────────────────────────────────────

    /**
     * Sequential order number from the configured prefix. Must be
     * called inside the caller's transaction — it locks on the
     * latest matching row to stay race-free.
     */
    public function generateUniqueOrderNumber(): string
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
    // ORDER ITEM SNAPSHOT
    // ─────────────────────────────────────────────

    /**
     * Server-trusted order item snapshot: immutable name/sku/image/
     * price fields captured at order time, plus the live model
     * references callers need for stock adjustments. This is the one
     * field list — admin POS and storefront checkout can never drift.
     */
    public function buildOrderItemPayload(Product $product, ProductVariant $variant, int $quantity): array
    {
        $unitPrice = $variant->final_price;

        return [
            'product' => $product,
            'variant' => $variant,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'product_sku' => $variant->sku,
            'product_image' => $variant->thumbnail ?? $product->thumbnail,
            'variant_options' => $variant->options_label,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'total_price' => round($unitPrice * $quantity, 2),
        ];
    }
}
