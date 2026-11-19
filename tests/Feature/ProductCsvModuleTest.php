<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Admin\ProductCsvService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class ProductCsvModuleTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_view_import_export_page(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.product.import-export'))
            ->assertOk()
            ->assertViewIs('admin.ecommerce.product.import-export')
            ->assertSee('product_name');
    }

    public function test_export_downloads_every_product_and_variant_row(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view']);
        $product = Product::factory()->create(['name' => 'Exported Widget']);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.product.export'));

        $response->assertOk()->assertDownload();

        $csv = $response->streamedContent();

        $this->assertStringContainsString('product_name,category,brand', $csv);
        $this->assertStringContainsString('Exported Widget', $csv);
        $this->assertStringContainsString($product->defaultVariant->sku, $csv);
    }

    public function test_admin_can_download_import_template(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view']);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.product.import-template'));

        $response->assertOk()->assertDownload('products-import-template.csv');

        $this->assertStringContainsString('Classic Cotton T-Shirt', $response->streamedContent());
    }

    public function test_import_creates_products_and_variants(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);
        $category = Category::factory()->create(['name' => 'Apparel']);

        $csv = $this->csv([
            ProductCsvService::COLUMNS,
            ['Imported Tee', 'Apparel', '', 'A soft tee.', '', '1', '0', 'summer, cotton', 'IMP-TEE-1', '19.99', '9.50', '', '', '25', '5', '0.20', 'kg', '1', '1', 'Color: Black | Size: M'],
            ['Imported Tee', 'Apparel', '', 'A soft tee.', '', '1', '0', 'summer, cotton', 'IMP-TEE-2', '21.99', '', '', '', '10', '5', '', '', '1', '0', 'Color: Black | Size: L'],
        ]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.import'), [
                'file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $product = Product::where('name', 'Imported Tee')->firstOrFail();

        $this->assertSame($category->id, $product->category_id);
        $this->assertSame(2, $product->variants()->count());
        $this->assertEquals(35, $product->total_stock);
        $this->assertTrue($product->tags()->where('name', 'summer')->exists());

        $variant = ProductVariant::where('sku', 'IMP-TEE-1')->firstOrFail();
        $this->assertEquals(19.99, (float) $variant->price);
        $this->assertTrue($variant->is_default);
        $this->assertStringContainsString('Color: Black', $variant->options_label);
        $this->assertStringContainsString('Size: M', $variant->options_label);
    }

    public function test_reimport_updates_product_and_variant_by_sku_without_duplicating(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);
        $category = Category::factory()->create(['name' => 'Apparel']);
        $product = Product::factory()->create([
            'name' => 'Existing Hoodie',
            'category_id' => $category->id,
        ]);
        $variant = $product->defaultVariant;
        $variant->update(['sku' => 'IMP-HOODIE-1', 'price' => 40, 'stock_quantity' => 3]);

        $csv = $this->csv([
            ProductCsvService::COLUMNS,
            ['Existing Hoodie', 'Apparel', 'Fresh Brand', '', '', '0', '1', '', 'IMP-HOODIE-1', '35.50', '', 'percentage', '10', '12', '4', '', '', '1', '1', 'Size: XL'],
        ]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.import'), [
                'file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
            ])
            ->assertSessionHas('success');

        $product->refresh();
        $variant->refresh();

        $this->assertSame(1, $product->variants()->count());
        $this->assertEquals(35.50, (float) $variant->price);
        $this->assertSame(12, $variant->stock_quantity);
        $this->assertSame('percentage', $variant->discount_type);
        $this->assertFalse($product->is_active);
        $this->assertTrue($product->is_featured);
        $this->assertSame('Fresh Brand', Brand::find($product->brand_id)?->name);
    }

    public function test_import_reports_rows_with_unknown_category_and_writes_nothing(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);

        $csv = $this->csv([
            ProductCsvService::COLUMNS,
            ['Orphan Product', 'No Such Category', '', '', '', '1', '0', '', 'IMP-ORPHAN-1', '10.00', '', '', '', '5', '2', '', '', '1', '1', ''],
        ]);

        $response = $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.import'), [
                'file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
            ]);

        $response->assertRedirect()->assertSessionHas('error');

        $this->assertDatabaseMissing('products', ['name' => 'Orphan Product']);
        $this->assertSame(0, ProductVariant::where('sku', 'IMP-ORPHAN-1')->count());
    }

    public function test_import_rejects_a_sku_owned_by_another_product(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);
        $category = Category::factory()->create(['name' => 'Apparel']);
        $product = Product::factory()->create(['name' => 'Owner Product', 'category_id' => $category->id]);
        $variant = $product->defaultVariant;
        $variant->update(['sku' => 'IMP-CLAIMED-1']);

        $csv = $this->csv([
            ProductCsvService::COLUMNS,
            ['Thief Product', 'Apparel', '', '', '', '1', '0', '', 'IMP-CLAIMED-1', '10.00', '', '', '', '5', '2', '', '', '1', '1', ''],
        ]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.import'), [
                'file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
            ])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('products', ['name' => 'Thief Product']);
        $this->assertSame($product->id, $variant->fresh()->product_id);
    }

    public function test_admin_without_create_permission_cannot_import(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view']);

        $csv = $this->csv([
            ProductCsvService::COLUMNS,
            ['Blocked Product', 'Apparel', '', '', '', '1', '0', '', '', '10.00', '', '', '', '5', '2', '', '', '1', '1', ''],
        ]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.import'), [
                'file' => UploadedFile::fake()->createWithContent('products.csv', $csv),
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('products', ['name' => 'Blocked Product']);
    }

    /**
     * @param  array<int, array<int, string>>  $rows
     */
    private function csv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($handle, $row, ',', '"', '');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }
}
