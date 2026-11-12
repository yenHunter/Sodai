<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_track_order_form_is_public(): void
    {
        $this->get(route('visitor.track-order'))
            ->assertOk()
            ->assertSee('Track Your Order')
            ->assertSee('trackOrderNumber', false);
    }

    public function test_guest_can_look_up_order_by_number_and_email(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'ORD-123456',
            'shipping_email' => 'guest@example.com',
            'subtotal' => 100,
            'discount_amount' => 10,
            'shipping_charge' => 0,
            'tax_amount' => 0,
            'total_amount' => 90,
        ]);

        OrderItem::factory()->for($order)->create([
            'product_name' => 'Tracked Product',
            'unit_price' => 100,
            'quantity' => 1,
            'total_price' => 100,
        ]);

        $response = $this->post(route('visitor.track-order.lookup'), [
            'order_number' => 'ORD-123456',
            'email' => 'guest@example.com',
        ]);

        $response->assertOk()
            ->assertSee('ORD-123456')
            ->assertSee('Tracked Product')
            ->assertSee('Total: $90.00');
    }

    public function test_lookup_matches_order_number_and_email_case_insensitively(): void
    {
        Order::factory()->create([
            'order_number' => 'ORD-654321',
            'shipping_email' => 'case@example.com',
        ]);

        $response = $this->post(route('visitor.track-order.lookup'), [
            'order_number' => 'ord-654321',
            'email' => 'CASE@EXAMPLE.COM',
        ]);

        $response->assertOk()->assertSee('ORD-654321');
    }

    public function test_unknown_order_or_email_returns_a_generic_error(): void
    {
        Order::factory()->create([
            'order_number' => 'ORD-111111',
            'shipping_email' => 'real@example.com',
        ]);

        $response = $this->from(route('visitor.track-order'))->post(route('visitor.track-order.lookup'), [
            'order_number' => 'ORD-111111',
            'email' => 'wrong@example.com',
        ]);

        $response->assertRedirect(route('visitor.track-order'));
        $response->assertSessionHas('error', 'We could not find an order with that number and email address.');
        $response->assertSessionHasInput('order_number', 'ORD-111111');
        $response->assertSessionMissing('order');
    }

    public function test_order_number_and_a_valid_email_are_required(): void
    {
        $response = $this->from(route('visitor.track-order'))->post(route('visitor.track-order.lookup'), []);

        $response->assertSessionHasErrors(['order_number', 'email']);
    }
}
