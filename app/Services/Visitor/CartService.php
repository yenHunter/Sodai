<?php

namespace App\Services\Visitor;

use App\Models\Cart;
use App\Models\User;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(?User $customer, ?string $sessionId): Cart
    {
        if ($customer) {
            return Cart::firstOrCreate(['user_id' => $customer->id]);
        }

        return Cart::firstOrCreate(['session_id' => $sessionId]);
    }

    public function getExistingCart(?User $customer, ?string $sessionId): ?Cart
    {
        if ($customer) {
            return Cart::where('user_id', $customer->id)->first();
        }

        return Cart::where('session_id', $sessionId)->first();
    }

    public function getCartWithItems(Cart $cart): Cart
    {
        return $cart->load(['items.variant.product', 'items.variant.optionValues.option']);
    }

    public function addItem(Cart $cart, int $variantId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cart, $variantId, $quantity) {
            $variant = ProductVariant::where('id', $variantId)->lockForUpdate()->firstOrFail();

            if (!$variant->is_active) {
                throw new \Exception('This product option is no longer available.');
            }

            $existing = CartItem::where('cart_id', $cart->id)
                ->where('product_variant_id', $variantId)
                ->first();

            $desiredQuantity = $quantity + ($existing?->quantity ?? 0);

            if ($variant->stock_quantity < $desiredQuantity) {
                throw new \Exception("Only {$variant->stock_quantity} unit(s) available.");
            }

            if ($existing) {
                $existing->update(['quantity' => $desiredQuantity]);
                return $existing->fresh();
            }

            return CartItem::create([
                'cart_id'            => $cart->id,
                'product_variant_id' => $variantId,
                'quantity'           => $quantity,
            ]);
        });
    }

    public function updateQuantity(CartItem $item, int $quantity): CartItem
    {
        if ($quantity < 1) {
            throw new \Exception('Quantity must be at least 1.');
        }

        $variant = $item->variant;

        if ($variant->stock_quantity < $quantity) {
            throw new \Exception("Only {$variant->stock_quantity} unit(s) available.");
        }

        $item->update(['quantity' => $quantity]);

        return $item->fresh();
    }

    public function removeItem(CartItem $item): bool
    {
        return $item->delete();
    }

    public function getCartTotal(Cart $cart): float
    {
        return round($cart->items->sum('subtotal'), 2);
    }
}
