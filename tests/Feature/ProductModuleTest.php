<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class ProductModuleTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_view_product_index(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view']);
        Product::factory()->count(3)->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.product.index'))
            ->assertOk();
    }

    public function test_product_index_search_filters_results(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view']);
        Product::factory()->create(['name' => 'Blue Widget']);
        Product::factory()->create(['name' => 'Red Gadget']);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.product.index', ['search' => 'Widget']));

        $response->assertOk();
        $response->assertViewHas('products', function ($products) {
            return $products->total() === 1;
        });
    }

    public function test_admin_can_create_product_with_thumbnail(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);
        $category = Category::factory()->create();

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.store'), [
                'category_id' => $category->id,
                'name' => 'Test Product',
                'is_active' => 'active',
                'thumbnail' => UploadedFile::fake()->image('thumb.png'),
                'variants' => [
                    [
                        'price' => 99.99,
                        'stock_quantity' => 10,
                        'low_stock_threshold' => 5,
                        'is_active' => 'active',
                        'is_default' => 'true',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);

        $product = Product::where('name', 'Test Product')->first();
        Storage::disk('public')->assertExists($product->thumbnail);
        $this->assertStringEndsWith('.webp', $product->thumbnail);
    }

    public function test_product_creation_requires_category(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.store'), [
                'name' => 'Test Product',
                'price' => 50,
            ])
            ->assertSessionHasErrors('category_id');
    }

    public function test_sku_is_auto_generated_on_create(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);
        $category = Category::factory()->create();

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.product.store'), [
            'category_id' => $category->id,
            'name' => 'SKU Test Product',
            'is_active' => 'active',
            'variants' => [
                ['price' => 10, 'stock_quantity' => 5, 'low_stock_threshold' => 1, 'is_active' => 'active'],
            ],
        ]);

        $product = Product::where('name', 'SKU Test Product')->first();
        $this->assertNotEmpty($product->defaultVariant->sku);
    }

    public function test_admin_can_view_product_details_page(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view']);
        $product = Product::factory()->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.product.show', $product))
            ->assertOk()
            ->assertViewIs('admin.ecommerce.product.details')
            ->assertSee($product->name);
    }

    public function test_admin_can_update_product(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.edit']);
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id, 'name' => 'Old Name']);
        $variant = $product->defaultVariant;

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.update', $product), [
                'category_id' => $category->id,
                'name' => 'Updated Name',
                'is_active' => 'active',
                'variants' => [
                    [
                        'id' => $variant->id,
                        'price' => 150,
                        'stock_quantity' => 20,
                        'low_stock_threshold' => 5,
                        'is_active' => 'active',
                        'is_default' => 'true',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name']);
    }

    public function test_admin_can_quick_update_stock(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.edit']);
        $product = Product::factory()->create();
        $variant = $product->defaultVariant;
        $variant->update(['stock_quantity' => 10]);

        $this->actingAsAdmin($admin)
            ->patchJson(route('admin.ecommerce.product.stock.update', [$product, $variant]), [
                'stock_quantity' => 25,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock_quantity' => 25]);
    }

    public function test_product_with_orders_cannot_be_deleted(): void
    {
        // Adjust this test once OrderItem factory linkage is finalized;
        // placeholder assumes Product::canDelete() checks orderItems() relation.
        $admin = $this->createAdminWithPermissions(['product.view', 'product.delete']);
        $product = Product::factory()->create();

        OrderItem::factory()->create(['product_id' => $product->id]);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.product.destroy', $product))
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    public function test_out_of_stock_scope_excludes_products_with_stock(): void
    {
        Product::factory()->outOfStock()->create();
        Product::factory()->create(); // default variant has stock

        $this->assertEquals(1, Product::where('total_stock', '<=', 0)->count());
    }
}
