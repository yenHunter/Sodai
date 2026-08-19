<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class OrderModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    public function test_admin_can_view_order_index_with_stats(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view']);
        Order::factory()->count(2)->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.order.index'))
            ->assertOk()
            ->assertViewHas('stats');
    }

    public function test_admin_can_create_order_and_stock_decrements(): void
    {
        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();
        $product->defaultVariant->update(['price' => 20, 'stock_quantity' => 10]);
        $product->refreshPriceAndStockCache();

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.order.store'), [
                'user_id'          => $customer->id,
                'shipping_name'    => 'John Doe',
                'shipping_email'   => 'john@example.com',
                'shipping_phone'   => '01700000000',
                'shipping_address' => '123 Street',
                'shipping_city'    => 'Dhaka',
                'shipping_state'   => 'Dhaka',
                'shipping_zip'     => '1200',
                'shipping_country' => 'Bangladesh',
                'items'            => [
                    ['product_id' => $product->id, 'quantity' => 3],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('product_variants', [
            'id'             => $product->defaultVariant->id,
            'stock_quantity' => 7,
        ]);
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_order_creation_fails_with_insufficient_stock(): void
    {
        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();
        $product->defaultVariant->update(['stock_quantity' => 2]);
        $product->refreshPriceAndStockCache();

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.order.store'), [
                'user_id'          => $customer->id,
                'shipping_name'    => 'Jane',
                'shipping_email'   => 'jane@example.com',
                'shipping_phone'   => '01700000000',
                'shipping_address' => 'Address',
                'shipping_city'    => 'Dhaka',
                'shipping_state'   => 'Dhaka',
                'shipping_zip'     => '1200',
                'shipping_country' => 'Bangladesh',
                'items'            => [
                    ['product_id' => $product->id, 'quantity' => 5],
                ],
            ])
            ->assertRedirect(route('admin.ecommerce.order.create'));

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('product_variants', ['id' => $product->defaultVariant->id, 'stock_quantity' => 2]);
    }

    public function test_creating_order_logs_initial_status_history(): void
    {
        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.order.store'), [
            'user_id' => $customer->id,
            'shipping_name' => 'A',
            'shipping_email' => 'a@a.com',
            'shipping_phone' => '01700000000',
            'shipping_address' => 'x',
            'shipping_city' => 'x',
            'shipping_state' => 'x',
            'shipping_zip' => '1200',
            'shipping_country' => 'BD',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $order = Order::first();
        $this->assertDatabaseHas('order_status_histories', [
            'order_id'    => $order->id,
            'from_status' => null,
            'to_status'   => 'pending',
        ]);
    }

    public function test_admin_can_update_order_status_and_history_is_recorded(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view', 'order.update-status']);
        $order = Order::factory()->withStatus('pending')->create();

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.order.status.update', $order), [
                'status' => 'confirmed',
                'note'   => 'Confirmed by admin.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id'    => $order->id,
            'from_status' => 'pending',
            'to_status'   => 'confirmed',
            'note'        => 'Confirmed by admin.',
        ]);
    }

    public function test_cancelling_order_restores_stock(): void
    {
        $admin   = $this->createAdminWithPermissions(['order.view', 'order.cancel']);
        $product = Product::factory()->create();
        $variant = $product->defaultVariant;
        $variant->update(['stock_quantity' => 5]);
        $product->refreshPriceAndStockCache();

        $order = Order::factory()->withStatus('pending')->create();
        \App\Models\OrderItem::factory()->create([
            'order_id'           => $order->id,
            'product_id'         => $product->id,
            'product_variant_id' => $variant->id,
            'quantity'           => 3,
        ]);
        $variant->decrement('stock_quantity', 3); // simulate the original decrement
        $product->refreshPriceAndStockCache();

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.order.cancel', $order), ['cancel_reason' => 'Customer request'])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock_quantity' => 5]);
    }

    public function test_delivered_order_cannot_be_edited(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view', 'order.edit']);
        $order = Order::factory()->withStatus('delivered')->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.order.edit', $order))
            ->assertRedirect(route('admin.ecommerce.order.show', $order));
    }

    public function test_only_cancelled_or_refunded_orders_are_deletable(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view', 'order.delete']);
        $order = Order::factory()->withStatus('processing')->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.order.destroy', $order))
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'deleted_at' => null]);
    }

    public function test_product_search_endpoint_excludes_out_of_stock(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view']);

        $available = Product::factory()->create(['name' => 'Available Item']);
        $available->defaultVariant->update(['stock_quantity' => 5]);
        $available->refreshPriceAndStockCache();

        $soldOut = Product::factory()->outOfStock()->create(['name' => 'Sold Out Item']);

        $response = $this->actingAsAdmin($admin)
            ->getJson(route('admin.ecommerce.order.products.search', ['q' => 'Item']));

        $response->assertOk();
        $names = collect($response->json())->pluck('name');
        $this->assertContains('Available Item', $names);
        $this->assertNotContains('Sold Out Item', $names);
    }

    public function test_order_number_uses_configured_prefix(): void
    {
        \App\Models\Setting::setMany('order', ['order_number_prefix' => 'SODAI-']);

        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.order.store'), [
            'user_id' => $customer->id,
            'shipping_name' => 'A',
            'shipping_email' => 'a@a.com',
            'shipping_phone' => '01700000000',
            'shipping_address' => 'x',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'x',
            'shipping_zip' => '1200',
            'shipping_country' => 'BD',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $order = Order::first();
        $this->assertStringStartsWith('SODAI-', $order->order_number);
    }

    public function test_shipping_charge_is_computed_from_settings_when_not_provided(): void
    {
        \App\Models\Setting::setMany('shipping', [
            'operation_areas'     => json_encode(['Dhaka']),
            'inside_area_charge'  => 80,
            'outside_area_charge' => 130,
        ]);

        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();
        $product->defaultVariant->update(['price' => 100]);
        $product->refreshPriceAndStockCache();

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.order.store'), [
            'user_id' => $customer->id,
            'shipping_name' => 'A',
            'shipping_email' => 'a@a.com',
            'shipping_phone' => '01700000000',
            'shipping_address' => 'x',
            'shipping_city' => 'Chattogram',
            'shipping_state' => 'x',
            'shipping_zip' => '1200',
            'shipping_country' => 'BD',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $order = Order::first();
        $this->assertEquals(130.00, (float) $order->shipping_charge); // outside configured area
    }

    public function test_manual_shipping_charge_overrides_settings_calculation(): void
    {
        \App\Models\Setting::setMany('shipping', [
            'operation_areas'     => json_encode(['Dhaka']),
            'inside_area_charge'  => 80,
            'outside_area_charge' => 130,
        ]);

        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.order.store'), [
            'user_id' => $customer->id,
            'shipping_name' => 'A',
            'shipping_email' => 'a@a.com',
            'shipping_phone' => '01700000000',
            'shipping_address' => 'x',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'x',
            'shipping_zip' => '1200',
            'shipping_country' => 'BD',
            'shipping_charge' => 250, // explicit override
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $order = Order::first();
        $this->assertEquals(250.00, (float) $order->shipping_charge);
    }

    public function test_tax_is_computed_from_settings_when_not_provided(): void
    {
        \App\Models\Setting::setMany('tax', ['tax_enabled' => '1', 'tax_rate' => 10, 'prices_include_tax' => '0']);

        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();
        $product->defaultVariant->update(['price' => 100]);
        $product->refreshPriceAndStockCache();

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.order.store'), [
            'user_id' => $customer->id,
            'shipping_name' => 'A',
            'shipping_email' => 'a@a.com',
            'shipping_phone' => '01700000000',
            'shipping_address' => 'x',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'x',
            'shipping_zip' => '1200',
            'shipping_country' => 'BD',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $order = Order::first();
        $this->assertEquals(10.00, (float) $order->tax_amount); // 10% of 100
    }

    public function test_valid_coupon_applies_discount_and_increments_usage(): void
    {
        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();
        $product->defaultVariant->update(['price' => 200]);
        $product->refreshPriceAndStockCache();

        $coupon = \App\Models\Coupon::factory()->create([
            'code' => 'SAVE20',
            'type' => 'percentage',
            'value' => 20,
            'used_count' => 0,
        ]);

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.order.store'), [
            'user_id' => $customer->id,
            'shipping_name' => 'A',
            'shipping_email' => 'a@a.com',
            'shipping_phone' => '01700000000',
            'shipping_address' => 'x',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'x',
            'shipping_zip' => '1200',
            'shipping_country' => 'BD',
            'coupon_code' => 'save20',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $order = Order::first();
        $this->assertEquals('SAVE20', $order->coupon_code);
        $this->assertEquals(40.00, (float) $order->discount_amount); // 20% of 200
        $this->assertEquals(1, $coupon->fresh()->used_count);
    }

    public function test_expired_coupon_is_rejected_on_order_creation(): void
    {
        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();

        \App\Models\Coupon::factory()->create([
            'code' => 'EXPIRED10',
            'expires_at' => now()->subDay(),
        ]);

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.order.store'), [
            'user_id' => $customer->id,
            'shipping_name' => 'A',
            'shipping_email' => 'a@a.com',
            'shipping_phone' => '01700000000',
            'shipping_address' => 'x',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'x',
            'shipping_zip' => '1200',
            'shipping_country' => 'BD',
            'coupon_code' => 'EXPIRED10',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertRedirect(route('admin.ecommerce.order.create'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_coupon_below_minimum_order_amount_is_rejected(): void
    {
        $admin    = $this->createAdminWithPermissions(['order.view', 'order.create']);
        $customer = User::factory()->create();
        $product  = Product::factory()->create();
        $product->defaultVariant->update(['price' => 10]);
        $product->refreshPriceAndStockCache();

        \App\Models\Coupon::factory()->create([
            'code' => 'BIGORDER',
            'minimum_order_amount' => 500,
        ]);

        $this->actingAsAdmin($admin)->post(route('admin.ecommerce.order.store'), [
            'user_id' => $customer->id,
            'shipping_name' => 'A',
            'shipping_email' => 'a@a.com',
            'shipping_phone' => '01700000000',
            'shipping_address' => 'x',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'x',
            'shipping_zip' => '1200',
            'shipping_country' => 'BD',
            'coupon_code' => 'BIGORDER',
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ])->assertRedirect(route('admin.ecommerce.order.create'));

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_admin_can_preview_shipping_charge_via_ajax(): void
    {
        \App\Models\Setting::setMany('shipping', [
            'operation_areas'     => json_encode(['Dhaka']),
            'inside_area_charge'  => 80,
            'outside_area_charge' => 130,
        ]);

        $admin = $this->createAdminWithPermissions(['order.view']);

        $this->actingAsAdmin($admin)
            ->getJson(route('admin.ecommerce.order.preview-shipping', ['city' => 'Dhaka', 'subtotal' => 500]))
            ->assertOk()
            ->assertJson(['shipping_charge' => 80, 'within_area' => true]);
    }

    public function test_admin_can_apply_coupon_via_ajax(): void
    {
        $admin    = $this->createAdminWithPermissions(['order.view']);
        $customer = User::factory()->create();
        \App\Models\Coupon::factory()->create(['code' => 'AJAX10', 'type' => 'fixed', 'value' => 10]);

        $this->actingAsAdmin($admin)
            ->postJson(route('admin.ecommerce.order.apply-coupon'), [
                'code' => 'ajax10',
                'user_id' => $customer->id,
                'subtotal' => 100,
            ])
            ->assertOk()
            ->assertJson(['success' => true, 'code' => 'AJAX10', 'discount_amount' => 10]);
    }
}
