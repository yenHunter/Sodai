<?php

namespace App\Services\Visitor;

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;

class WishlistService
{
    public function getWishlist(User $customer)
    {
        return Wishlist::where('user_id', $customer->id)
            ->with(['product.category', 'product.brand'])
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }

    /**
     * @return array{added: bool}
     */
    public function toggle(User $customer, Product $product): array
    {
        $existing = Wishlist::where('user_id', $customer->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return ['added' => false];
        }

        Wishlist::create([
            'user_id'    => $customer->id,
            'product_id' => $product->id,
        ]);

        return ['added' => true];
    }

    public function remove(User $customer, Product $product): bool
    {
        return Wishlist::where('user_id', $customer->id)
            ->where('product_id', $product->id)
            ->delete() > 0;
    }
}