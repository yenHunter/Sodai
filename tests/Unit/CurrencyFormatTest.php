<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyFormatTest extends TestCase
{
    use RefreshDatabase;

    public function test_defaults_to_usd_symbol_before_the_amount(): void
    {
        $this->assertSame('$1,299.50', money(1299.5));
        $this->assertSame('$0.00', format_price(0));
    }

    public function test_uses_the_configured_currency_symbol(): void
    {
        Setting::setMany('company', ['currency' => 'BDT']);

        $this->assertSame('৳1,299.50', money(1299.5));
    }

    public function test_renders_the_symbol_after_the_amount_when_configured(): void
    {
        Setting::setMany('company', ['currency' => 'EUR', 'currency_symbol_position' => 'after']);

        $this->assertSame('1,299.50€', money(1299.5));
    }

    public function test_respects_a_custom_decimal_count(): void
    {
        $this->assertSame('$1,300', money(1299.5, 0));
        $this->assertSame('$1,299.500', money(1299.5, 3));
    }

    public function test_uses_the_currency_decimals_setting(): void
    {
        Setting::setMany('company', ['currency' => 'BDT', 'currency_decimals' => '0']);

        $this->assertSame('৳1,300', money(1299.5));
    }

    public function test_unknown_currency_falls_back_to_the_iso_code(): void
    {
        Setting::setMany('company', ['currency' => 'AUD']);

        $this->assertSame('AUD 10.00', money(10));
        $this->assertSame('AUD', currency_symbol());
    }

    public function test_helpers_expose_symbol_and_position(): void
    {
        Setting::setMany('company', ['currency' => 'INR', 'currency_symbol_position' => 'after']);

        $this->assertSame('₹', currency_symbol());
        $this->assertSame('after', currency_position());
    }

    public function test_product_price_range_label_uses_the_configured_currency(): void
    {
        Setting::setMany('company', ['currency' => 'BDT']);

        $product = Product::factory()->create();
        $variant = $product->variants()->first()
            ?? ProductVariant::factory()->create(['product_id' => $product->id, 'is_default' => true]);

        $variant->update(['price' => 100, 'discount_type' => 'fixed', 'discount_value' => 0]);
        $product->refreshPriceAndStockCache();
        $product->refresh();

        $this->assertSame('৳100.00', $product->price_range_label);
    }

    public function test_coupon_value_label_uses_the_configured_currency(): void
    {
        Setting::setMany('company', ['currency' => 'BDT']);

        $fixed = new Coupon(['type' => 'fixed', 'value' => 50]);
        $percentage = new Coupon(['type' => 'percentage', 'value' => 12.5]);

        $this->assertSame('৳50.00', $fixed->value_label);
        $this->assertSame('12.50%', $percentage->value_label);
    }
}
