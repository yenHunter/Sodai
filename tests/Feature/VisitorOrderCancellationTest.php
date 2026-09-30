<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorOrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * An order for $customer holding $quantity units of a product, with the
     * variant stock already decremented (as order placement would have done).
     *
     * @return array{0: Order, 1: Product, 2: ProductVariant}
     */
    private function makeOrder(User $customer, string $status = 'pending', int $quantity = 3, int $stock = 10): array
    {
        $product = Product::factory()->create();
        $variant = $product->defaultVariant;
        $variant->update(['price' => 100, 'stock_quantity' => $stock]);
        $product->refreshPriceAndStockCache();

        $order = Order::factory()->withStatus($status)->for($customer)->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
        ]);

        // Simulate the decrement that placing the order performed.
        $variant->decrement('stock_quantity', $quantity);
        $product->refreshPriceAndStockCache();

        return [$order, $product, $variant];
    }

    // ─────────────────────────────────────────────
    // HAPPY PATH
    // ─────────────────────────────────────────────

    public function test_customer_can_cancel_own_pending_order_and_stock_is_restored(): void
    {
        $customer = User::factory()->create();
        [$order, $product, $variant] = $this->makeOrder($customer);

        $response = $this->actingAs($customer, 'customer')
            ->post(route('visitor.account.orders.cancel', $order), [
                'cancel_reason' => 'Changed my mind',
            ]);

        $response->assertRedirect(route('visitor.account.orders.show', $order));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        $this->assertSame('Changed my mind', $order->cancel_reason);
        $this->assertNotNull($order->cancelled_at);

        // Stock restored to its pre-order level via the shared admin cancel logic.
        $this->assertSame(10, (int) $variant->refresh()->stock_quantity);
        $this->assertSame(10, (int) $product->refresh()->total_stock);

        // Status history recorded without an admin actor.
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => 'pending',
            'to_status' => 'cancelled',
        ]);
    }

    public function test_customer_can_cancel_own_confirmed_order(): void
    {
        $customer = User::factory()->create();
        [$order] = $this->makeOrder($customer, status: 'confirmed');

        $this->actingAs($customer, 'customer')
            ->post(route('visitor.account.orders.cancel', $order), [
                'cancel_reason' => null,
            ])
            ->assertSessionHas('success');

        $order->refresh();
        $this->assertSame('cancelled', $order->status);
        // Falls back to the default reason when none supplied.
        $this->assertSame('Cancelled by customer.', $order->cancel_reason);
    }

    // ─────────────────────────────────────────────
    // OWNERSHIP & AUTH
    // ─────────────────────────────────────────────

    public function test_customer_cannot_cancel_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        [$order] = $this->makeOrder($owner);

        $this->actingAs($intruder, 'customer')
            ->post(route('visitor.account.orders.cancel', $order))
            ->assertForbidden();

        $this->assertSame('pending', $order->refresh()->status);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        [$order] = $this->makeOrder(User::factory()->create());

        $this->post(route('visitor.account.orders.cancel', $order))
            ->assertRedirect(route('visitor.login'));

        $this->assertSame('pending', $order->refresh()->status);
    }

    // ─────────────────────────────────────────────
    // STATUS GUARDS
    // ─────────────────────────────────────────────

    public function test_shipped_order_cannot_be_cancelled_by_customer(): void
    {
        $customer = User::factory()->create();
        [$order, , $variant] = $this->makeOrder($customer, status: 'shipped');

        $this->actingAs($customer, 'customer')
            ->post(route('visitor.account.orders.cancel', $order))
            ->assertRedirect(route('visitor.account.orders.show', $order))
            ->assertSessionHas('error');

        $this->assertSame('shipped', $order->refresh()->status);
        $this->assertSame(7, (int) $variant->refresh()->stock_quantity); // untouched
    }

    public function test_delivered_and_refunded_orders_cannot_be_cancelled(): void
    {
        $customer = User::factory()->create();
        [$delivered] = $this->makeOrder($customer, status: 'delivered');
        [$refunded] = $this->makeOrder($customer, status: 'refunded');

        $this->actingAs($customer, 'customer')
            ->post(route('visitor.account.orders.cancel', $delivered))
            ->assertSessionHas('error');
        $this->assertSame('delivered', $delivered->refresh()->status);

        $this->actingAs($customer, 'customer')
            ->post(route('visitor.account.orders.cancel', $refunded))
            ->assertSessionHas('error');
        $this->assertSame('refunded', $refunded->refresh()->status);
    }

    // ─────────────────────────────────────────────
    // UI WIRING
    // ─────────────────────────────────────────────

    public function test_details_view_shows_cancel_button_only_when_cancellable(): void
    {
        $customer = User::factory()->create();
        [$pending] = $this->makeOrder($customer, status: 'pending');
        [$shipped] = $this->makeOrder($customer, status: 'shipped');

        $this->actingAs($customer, 'customer')
            ->get(route('visitor.account.orders.show', $pending))
            ->assertOk()
            ->assertSee('Cancel Order');

        $this->actingAs($customer, 'customer')
            ->get(route('visitor.account.orders.show', $shipped))
            ->assertOk()
            ->assertDontSee('Cancel Order');
    }
}
