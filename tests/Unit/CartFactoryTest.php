<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_factory_creates_a_cart_for_a_user(): void
    {
        $cart = Cart::factory()->create();

        $this->assertNotNull($cart->user_id);
        $this->assertNull($cart->session_id);
        $this->assertDatabaseHas('carts', ['id' => $cart->id]);
    }

    public function test_cart_item_factory_creates_a_valid_cart_item(): void
    {
        $cartItem = CartItem::factory()->create();

        $this->assertInstanceOf(Cart::class, $cartItem->cart);
        $this->assertInstanceOf(ProductVariant::class, $cartItem->variant);
        $this->assertGreaterThanOrEqual(1, $cartItem->quantity);
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'cart_id' => $cartItem->cart_id,
            'product_variant_id' => $cartItem->product_variant_id,
        ]);
    }

    public function test_cart_item_factory_quantity_is_cast_to_int(): void
    {
        $variant = ProductVariant::factory()->create([
            'price' => 10,
            'discount_type' => null,
            'discount_value' => null,
        ]);

        $cartItem = CartItem::factory()->create([
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        $this->assertSame(3, $cartItem->quantity);
        $this->assertSame(10.0, $cartItem->unit_price);
        $this->assertSame(30.0, $cartItem->subtotal);
    }
}
