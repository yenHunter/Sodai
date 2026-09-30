<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Services\Admin\SettingService;
use App\Services\Shared\PricingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class PricingServiceTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    private function service(MockInterface $settings): PricingService
    {
        return new PricingService($settings);
    }

    private function settings(): MockInterface
    {
        return Mockery::mock(SettingService::class);
    }

    private function makeCoupon(array $attributes = []): Coupon
    {
        // Unsaved model is enough for discount math — only casts are involved.
        return new Coupon(array_merge([
            'code' => 'TESTCODE',
            'type' => 'percentage',
            'value' => 20,
        ], $attributes));
    }

    // ─────────────────────────────────────────────
    // TAX MODES
    // ─────────────────────────────────────────────

    public function test_tax_returns_zero_when_disabled(): void
    {
        $settings = $this->settings();
        $settings->shouldReceive('getGroup')->with('tax')->andReturn(['tax_enabled' => '0']);

        $this->assertSame(0.0, $this->service($settings)->calculateTax(250));
    }

    public function test_tax_returns_zero_when_rate_is_zero(): void
    {
        $settings = $this->settings();
        $settings->shouldReceive('getGroup')->with('tax')->andReturn([
            'tax_enabled' => '1',
            'tax_rate' => '0',
        ]);

        $this->assertSame(0.0, $this->service($settings)->calculateTax(250));
    }

    public function test_exclusive_tax_is_percentage_of_subtotal(): void
    {
        $settings = $this->settings();
        $settings->shouldReceive('getGroup')->with('tax')->andReturn([
            'tax_enabled' => '1',
            'tax_rate' => '10',
            'prices_include_tax' => '0',
        ]);

        $this->assertSame(20.0, $this->service($settings)->calculateTax(200));
    }

    public function test_inclusive_tax_extracts_portion_from_subtotal(): void
    {
        $settings = $this->settings();
        $settings->shouldReceive('getGroup')->with('tax')->andReturn([
            'tax_enabled' => '1',
            'tax_rate' => '10',
            'prices_include_tax' => '1',
        ]);

        // 100 - (100 / 1.1) = 9.09 — informational only, not additive.
        $this->assertSame(9.09, $this->service($settings)->calculateTax(100));
    }

    // ─────────────────────────────────────────────
    // SHIPPING (pass-through + rounding)
    // ─────────────────────────────────────────────

    public function test_shipping_charge_delegates_to_settings_and_rounds(): void
    {
        $settings = $this->settings();
        $settings->shouldReceive('resolveShippingCharge')
            ->with('Dhaka', 500.0)
            ->andReturn(25.456);

        $this->assertSame(25.46, $this->service($settings)->resolveShippingCharge('Dhaka', 500.0));
    }

    // ─────────────────────────────────────────────
    // TOTAL CLAMPING
    // ─────────────────────────────────────────────

    public function test_total_combines_all_components(): void
    {
        $this->assertSame(97.0, $this->service($this->settings())->calculateTotal(100, 10, 5, 2));
    }

    public function test_total_clamps_at_zero_when_discount_exceeds_subtotal(): void
    {
        $this->assertSame(0.0, $this->service($this->settings())->calculateTotal(100, 200, 0, 0));
    }

    public function test_total_rounds_to_two_decimals(): void
    {
        $this->assertSame(100.46, $this->service($this->settings())->calculateTotal(100.456, 0, 0, 0));
    }

    // ─────────────────────────────────────────────
    // COUPON DISCOUNT CAPS
    // ─────────────────────────────────────────────

    public function test_fixed_discount_capped_at_subtotal(): void
    {
        $coupon = $this->makeCoupon(['type' => 'fixed', 'value' => 50]);

        $this->assertSame(30.0, $this->service($this->settings())->calculateCouponDiscount($coupon, 30));
    }

    public function test_fixed_discount_under_subtotal_applies_in_full(): void
    {
        $coupon = $this->makeCoupon(['type' => 'fixed', 'value' => 20]);

        $this->assertSame(20.0, $this->service($this->settings())->calculateCouponDiscount($coupon, 100));
    }

    public function test_percentage_discount_applies_to_subtotal(): void
    {
        $coupon = $this->makeCoupon(['type' => 'percentage', 'value' => 20]);

        $this->assertSame(80.0, $this->service($this->settings())->calculateCouponDiscount($coupon, 400));
    }

    public function test_percentage_discount_capped_by_maximum_discount(): void
    {
        $coupon = $this->makeCoupon(['type' => 'percentage', 'value' => 50, 'maximum_discount' => 100]);

        $this->assertSame(100.0, $this->service($this->settings())->calculateCouponDiscount($coupon, 1000));
    }

    public function test_percentage_discount_never_exceeds_subtotal(): void
    {
        $coupon = $this->makeCoupon(['type' => 'percentage', 'value' => 100]);

        $this->assertSame(50.0, $this->service($this->settings())->calculateCouponDiscount($coupon, 50));
    }

    // ─────────────────────────────────────────────
    // COUPON VALIDATION (DB-backed)
    // ─────────────────────────────────────────────

    public function test_coupon_code_lookup_is_trimmed_and_uppercased(): void
    {
        $coupon = Coupon::factory()->create(['code' => 'SAVE20']);

        $result = $this->service($this->settings())->validateAndLockCoupon('  save20 ', null, 100);

        $this->assertSame($coupon->id, $result->id);
    }

    public function test_unknown_coupon_is_rejected(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('does not exist');

        $this->service($this->settings())->validateAndLockCoupon('NOPE', null, 100);
    }

    public function test_expired_coupon_is_rejected(): void
    {
        Coupon::factory()->create(['code' => 'OLDCODE', 'expires_at' => now()->subDay()]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not currently valid');

        $this->service($this->settings())->validateAndLockCoupon('OLDCODE', null, 100);
    }

    public function test_coupon_below_minimum_order_amount_is_rejected(): void
    {
        Coupon::factory()->create(['code' => 'MIN500', 'minimum_order_amount' => 500]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('requires a minimum order amount of 500.00');

        $this->service($this->settings())->validateAndLockCoupon('MIN500', null, 100);
    }

    public function test_per_user_limit_blocks_customer_who_already_used_coupon(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'ONCEONLY', 'usage_per_user' => 1]);

        Order::factory()->for($user)->create([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'status' => 'confirmed',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('maximum number of times');

        $this->service($this->settings())->validateAndLockCoupon('ONCEONLY', $user->id, 100);
    }

    public function test_cancelled_orders_do_not_count_toward_per_user_limit(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'ONCEONLY', 'usage_per_user' => 1]);

        Order::factory()->for($user)->create([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'status' => 'cancelled',
        ]);

        $result = $this->service($this->settings())->validateAndLockCoupon('ONCEONLY', $user->id, 100);

        $this->assertSame($coupon->id, $result->id);
    }

    public function test_ignore_order_id_excludes_the_order_being_edited(): void
    {
        $user = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'ONCEONLY', 'usage_per_user' => 1]);

        $order = Order::factory()->for($user)->create([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'status' => 'confirmed',
        ]);

        $result = $this->service($this->settings())
            ->validateAndLockCoupon('ONCEONLY', $user->id, 100, ignoreOrderId: $order->id);

        $this->assertSame($coupon->id, $result->id);
    }

    public function test_guest_user_skips_per_user_check_entirely(): void
    {
        $coupon = Coupon::factory()->create(['code' => 'ANYONE', 'usage_per_user' => 1]);

        // An existing order on this coupon must not block a guest (no user to attribute).
        Order::factory()->create([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'status' => 'confirmed',
        ]);

        $result = $this->service($this->settings())->validateAndLockCoupon('ANYONE', null, 100);

        $this->assertSame($coupon->id, $result->id);
    }

    // ─────────────────────────────────────────────
    // ORDER NUMBER SEQUENCING (DB-backed)
    // ─────────────────────────────────────────────

    public function test_first_order_number_uses_default_prefix(): void
    {
        $settings = $this->settings();
        $settings->shouldReceive('getGroup')->with('order')->andReturn([]);

        $this->assertSame('ORD-000001', $this->service($settings)->generateUniqueOrderNumber());
    }

    public function test_first_order_number_uses_configured_prefix(): void
    {
        $settings = $this->settings();
        $settings->shouldReceive('getGroup')->with('order')->andReturn(['order_number_prefix' => 'REF-']);

        $this->assertSame('REF-000001', $this->service($settings)->generateUniqueOrderNumber());
    }

    public function test_order_number_increments_from_highest_existing(): void
    {
        Order::factory()->create(['order_number' => 'ORD-000009']);
        Order::factory()->create(['order_number' => 'ORD-000041']);

        $settings = $this->settings();
        $settings->shouldReceive('getGroup')->with('order')->andReturn([]);

        $this->assertSame('ORD-000042', $this->service($settings)->generateUniqueOrderNumber());
    }

    public function test_order_number_sequences_are_isolated_per_prefix(): void
    {
        Order::factory()->create(['order_number' => 'ORD-000041']);

        $settings = $this->settings();
        $settings->shouldReceive('getGroup')->with('order')->andReturn(['order_number_prefix' => 'REF-']);

        $this->assertSame('REF-000001', $this->service($settings)->generateUniqueOrderNumber());
    }
}
