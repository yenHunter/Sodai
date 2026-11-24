<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Refund;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoCatalogSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Pin the demo history so date assertions are deterministic.
        config([
            'app.demo_date' => '2026-01-15 09:00:00',
            'app.demo_password' => null,
        ]);
    }

    public function test_the_catalog_is_seeded_and_re_runnable(): void
    {
        $this->seedDemo();
        $this->seedDemo();

        $this->assertSame(8, Product::whereIn('slug', DemoCatalogSeeder::productSlugs())->count());
        $this->assertSame(22, ProductVariant::whereIn('sku', DemoCatalogSeeder::variantSkus())->count());
        $this->assertSame(14, Order::whereIn('order_number', DemoCatalogSeeder::orderNumbers())->count());
        $this->assertSame(6, User::whereIn('email', DemoCatalogSeeder::customerEmails())->count());
        $this->assertSame(8, Review::count());
        $this->assertSame(1, Refund::count());
        $this->assertDatabaseHas('coupons', ['code' => DemoCatalogSeeder::couponCode()]);
        $this->assertDatabaseMissing('orders', ['order_number' => 'ORD-100001']);

        // Both demo accounts still sign in with the documented password: the
        // one hash shared by the admin and the demo customer has to survive the
        // models' `hashed` cast untouched, on a re-run as well.
        $admin = Hash::check('Demo#12345', Admin::where('email', DemoCatalogSeeder::ADMIN_EMAIL)->value('password'));
        $customer = Hash::check('Demo#12345', User::where('email', DemoCatalogSeeder::CUSTOMER_EMAIL)->value('password'));

        $this->assertTrue($admin && $customer);
    }

    public function test_the_coupon_order_respects_the_coupons_maximum_discount(): void
    {
        $this->seedDemo();

        $order = Order::where('order_number', DemoCatalogSeeder::orderNumber(100003))->firstOrFail();

        $this->assertSame(7350.0, (float) $order->subtotal);
        $this->assertSame(700.0, (float) $order->discount_amount);
    }

    public function test_seeding_fills_missing_settings_and_records_only_those(): void
    {
        Setting::setMany('company', ['name' => 'Real Tea Co', 'currency' => 'USD']);

        $this->seedDemo();

        $this->assertSame('Real Tea Co', Setting::get('company', 'name'));
        $this->assertSame('USD', Setting::get('company', 'currency'));
        $this->assertSame('60', Setting::get('shipping', 'inside_area_charge'));

        $recorded = DemoCatalogSeeder::recordedSettings();

        $this->assertArrayHasKey('shipping.inside_area_charge', $recorded);
        $this->assertArrayNotHasKey('company.name', $recorded);
        $this->assertArrayNotHasKey('company.currency', $recorded);
    }

    public function test_demo_dates_follow_the_configured_anchor(): void
    {
        $this->seedDemo();

        $this->assertSame(
            '2025-12-01 11:30:00',
            Order::where('order_number', DemoCatalogSeeder::orderNumber(100001))->value('created_at')->toDateTimeString(),
        );
        $this->assertSame(
            '2026-03-16',
            Coupon::where('code', DemoCatalogSeeder::couponCode())->value('expires_at')->toDateString(),
        );
    }

    public function test_production_skips_the_demo_catalog(): void
    {
        $this->app['env'] = 'production';

        $this->seedDemo();

        $this->assertSame(0, Product::count());
    }

    public function test_environments_beyond_the_developers_machine_need_an_explicit_password(): void
    {
        $this->app['env'] = 'staging';

        $this->seedDemo();
        $this->assertSame(0, Product::count());

        config(['app.demo_password' => 'Secret#99999']);
        $this->seedDemo();
        $this->assertSame(8, Product::whereIn('slug', DemoCatalogSeeder::productSlugs())->count());
    }

    public function test_clear_removes_demo_rows_and_demo_created_settings_only(): void
    {
        $this->createRealCatalog();
        Setting::setMany('company', ['name' => 'Real Tea Co', 'currency' => 'USD']);

        $this->seedDemo();
        $this->artisan('demo:clear', ['--force' => true])->assertSuccessful();

        $this->assertDatabaseMissing('products', ['slug' => 'demo-kizen-stoneware-mug']);
        $this->assertDatabaseHas('products', ['slug' => 'kizen-stoneware-mug']);
        $this->assertSame(0, Order::whereIn('order_number', DemoCatalogSeeder::orderNumbers())->withTrashed()->count());
        $this->assertSame(1, Product::count());
        $this->assertSame('Real Tea Co', Setting::get('company', 'name'));
        $this->assertNull(Setting::get('company', 'phone'));
        $this->assertSame([], DemoCatalogSeeder::recordedSettings());
    }

    public function test_clear_keeps_a_demo_product_a_real_order_still_needs(): void
    {
        $this->seedDemo();

        $demoProduct = Product::where('slug', 'demo-kizen-stoneware-mug')->firstOrFail();
        $demoVariant = ProductVariant::where('sku', 'DEMO-SOD-MUG-SAGE-350')->firstOrFail();
        $realOrder = $this->createRealOrder();
        $item = OrderItem::create([
            'order_id' => $realOrder->id,
            'product_id' => $demoProduct->id,
            'product_variant_id' => $demoVariant->id,
            'product_name' => $demoProduct->name,
            'product_sku' => $demoVariant->sku,
            'unit_price' => 1850,
            'quantity' => 1,
            'total_price' => 1850,
        ]);

        $this->artisan('demo:clear', ['--force' => true])->assertSuccessful();

        $this->assertDatabaseHas('products', ['slug' => 'demo-kizen-stoneware-mug']);
        $this->assertDatabaseHas('orders', ['order_number' => 'ORD-100001']);

        $item->delete();
        $this->artisan('demo:clear', ['--force' => true])->assertSuccessful();

        $this->assertDatabaseMissing('products', ['slug' => 'demo-kizen-stoneware-mug']);
    }

    public function test_clear_dry_run_changes_nothing(): void
    {
        $this->seedDemo();

        $this->artisan('demo:clear', ['--dry-run' => true, '--force' => true])->assertSuccessful();

        $this->assertSame(8, Product::whereIn('slug', DemoCatalogSeeder::productSlugs())->count());
        $this->assertSame(14, Order::whereIn('order_number', DemoCatalogSeeder::orderNumbers())->count());
        $this->assertSame(6, User::whereIn('email', DemoCatalogSeeder::customerEmails())->count());
        $this->assertNotSame([], DemoCatalogSeeder::recordedSettings());
    }

    public function test_demo_seed_command_seeds_the_catalog(): void
    {
        $this->artisan('demo:seed', ['--force' => true])->assertSuccessful();

        $this->assertSame(8, Product::whereIn('slug', DemoCatalogSeeder::productSlugs())->count());
        $this->assertSame(14, Order::whereIn('order_number', DemoCatalogSeeder::orderNumbers())->count());
    }

    private function seedDemo(): void
    {
        $this->artisan('db:seed', ['--class' => DemoCatalogSeeder::class, '--force' => true])->assertSuccessful();
    }

    private function createRealCatalog(): void
    {
        $category = Category::create(['name' => 'Real Stationery', 'slug' => 'stationery', 'is_active' => true]);
        $brand = Brand::create(['name' => 'Real Aoyama', 'slug' => 'aoyama', 'is_active' => true]);
        $product = Product::create([
            'name' => 'Real Mug',
            'slug' => 'kizen-stoneware-mug',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'is_active' => true,
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SOD-MUG-SAGE-350',
            'price' => 1234,
            'stock_quantity' => 3,
            'is_default' => true,
            'is_active' => true,
        ]);
        Banner::create([
            'title' => 'The Quiet Morning Collection',
            'position' => 'home_slider',
            'image' => 'banners/real-hero.webp',
            'is_active' => true,
        ]);
    }

    private function createRealOrder(): Order
    {
        $user = User::create([
            'name' => 'Real Buyer',
            'email' => 'real.buyer@real-shop.test',
            'password' => 'Secret#12345',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        return Order::create([
            'order_number' => 'ORD-100001',
            'user_id' => $user->id,
            'status' => 'processing',
            'subtotal' => 1234,
            'discount_amount' => 0,
            'shipping_charge' => 60,
            'tax_amount' => 0,
            'total_amount' => 1294,
            'payment_method' => 'cod',
            'shipping_name' => 'Real Buyer',
            'shipping_email' => 'real.buyer@real-shop.test',
            'shipping_phone' => '+880 1000 000000',
            'shipping_address' => '1 Real Road',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'Dhaka',
            'shipping_zip' => '1200',
            'shipping_country' => 'Bangladesh',
        ]);
    }
}
