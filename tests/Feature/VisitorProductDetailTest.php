<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_page_links_related_products_without_a_server_error(): void
    {
        $product = Product::factory()->create(['name' => 'Kizen Stoneware Mug']);
        $related = Product::factory()->create(['name' => 'Aoyama Ceramic Teapot']);

        $product->relatedProducts()->attach($related->id);
        $related->refreshPriceAndStockCache();
        $related->refresh();

        $response = $this->get(route('visitor.products.show', $product->slug));

        $response->assertOk();
        $response->assertSee('Related products');
        $response->assertSee('href="'.route('visitor.products.show', $related->slug).'"', false);
        $response->assertSee($related->name);
        $response->assertSee($related->price_range_label);
    }
}
