<?php

namespace Tests\Unit;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAccessorsTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────
    // DISCOUNT ACCESSORS (bug: read non-existent `price` column)
    // ─────────────────────────────────────────────

    public function test_discount_amount_and_percentage_are_computed_from_list_price(): void
    {
        [$product] = $this->makeProductWithVariant([
            'price' => 100,
            'discount_type' => 'percentage',
            'discount_value' => 20,
        ]);

        $this->assertSame(100.0, (float) $product->max_price);
        $this->assertSame(80.0, $product->final_price);
        $this->assertSame(20.0, $product->discount_amount);
        $this->assertSame(20.0, $product->discount_percentage);
        $this->assertTrue($product->has_discount);
    }

    public function test_product_without_variant_discount_has_no_discount(): void
    {
        [$product] = $this->makeProductWithVariant(['price' => 100]);

        $this->assertSame(0.0, $product->discount_amount);
        $this->assertSame(0.0, $product->discount_percentage);
        $this->assertFalse($product->has_discount);
    }

    // ─────────────────────────────────────────────
    // LOW STOCK ACCESSOR (bug: read non-existent variant columns)
    // ─────────────────────────────────────────────

    public function test_low_stock_uses_total_stock_and_inventory_threshold_setting(): void
    {
        Setting::setMany('inventory', ['default_low_stock_threshold' => '10']);

        [$product] = $this->makeProductWithVariant(['stock_quantity' => 8]);

        $this->assertSame(8, $product->total_stock);
        $this->assertTrue($product->is_low_stock);
        $this->assertSame('Low Stock', $product->stock_status);
    }

    public function test_out_of_stock_product_is_not_low_stock(): void
    {
        Setting::setMany('inventory', ['default_low_stock_threshold' => '10']);

        [$product] = $this->makeProductWithVariant(['stock_quantity' => 0]);

        $this->assertTrue($product->is_out_of_stock);
        $this->assertFalse($product->is_low_stock);
        $this->assertSame('Out of Stock', $product->stock_status);
    }

    public function test_low_stock_falls_back_to_default_threshold_of_five(): void
    {
        // No inventory setting seeded — the accessor must fall back to 5.
        [$low] = $this->makeProductWithVariant(['stock_quantity' => 5]);
        [$ok] = $this->makeProductWithVariant(['stock_quantity' => 6]);

        $this->assertTrue($low->is_low_stock);
        $this->assertFalse($ok->is_low_stock);
    }

    // ─────────────────────────────────────────────
    // DELETION GUARD (bug: compared product ID against cart_items.product_variant_id)
    // ─────────────────────────────────────────────

    public function test_product_in_a_customer_cart_cannot_be_deleted(): void
    {
        [$product, $variant] = $this->makeProductWithVariant();

        CartItem::factory()->create(['product_variant_id' => $variant->id]);

        $this->assertFalse($product->canDelete());
        $this->assertStringContainsString('carts', $product->deletion_block_reason);
    }

    public function test_product_without_cart_items_can_be_deleted(): void
    {
        [$product] = $this->makeProductWithVariant();

        $this->assertTrue($product->canDelete());
        $this->assertNull($product->deletion_block_reason);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    /**
     * Product with exactly one deterministic variant. Reuses the
     * factory's auto-created default variant when present, otherwise
     * creates one — then refreshes the denormalized price/stock cache.
     *
     * @return array{0: Product, 1: ProductVariant}
     */
    private function makeProductWithVariant(array $variantAttributes = []): array
    {
        $product = Product::factory()->create();

        $variant = $product->variants()->first();
        if ($variant) {
            $variant->update($variantAttributes);
        } else {
            $variant = ProductVariant::factory()
                ->create($variantAttributes + ['product_id' => $product->id, 'is_default' => true]);
        }

        $product->refreshPriceAndStockCache();

        return [$product->refresh(), $variant->refresh()];
    }
}
