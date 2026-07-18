<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\Customer\CartReminderMail;

class CartService
{
    public function getCartsList(array $filters = [])
    {
        $query = Cart::with(['user', 'items.product'])->whereHas('items');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->latest('updated_at')->paginate(15)->withQueryString();
    }

    public function getCartForDetails(Cart $cart): array
    {
        $cart->load(['user', 'items.product']);

        $items = $cart->items->map(fn ($item) => [
            'product_name'  => $item->product?->name ?? 'Deleted Product',
            'product_sku'   => $item->product?->sku ?? '—',
            'thumbnail_url' => $item->product?->thumbnail_url,
            'unit_price'    => $item->unit_price,
            'quantity'      => $item->quantity,
            'subtotal'      => $item->subtotal,
        ]);

        return [
            'customer_name'  => $cart->user?->name ?? 'Guest',
            'customer_email' => $cart->user?->email,
            'items'          => $items,
            'total'          => round($items->sum('subtotal'), 2),
            'updated_at'     => $cart->updated_at->format('d M Y, h:i A'),
        ];
    }

    public function delete(Cart $cart): bool
    {
        return $cart->delete();
    }

    public function sendReminderEmail(Cart $cart): void
    {
        $cart->load(['user', 'items.product']);

        if (!$cart->user || !$cart->user->email) {
            throw new \Exception('This cart has no registered customer to email.');
        }

        if ($cart->items->isEmpty()) {
            throw new \Exception('This cart is empty.');
        }

        $items = $cart->items->map(fn ($item) => [
            'name'     => $item->product?->name ?? 'Product',
            'quantity' => $item->quantity,
            'subtotal' => $item->subtotal,
        ])->toArray();

        Mail::to($cart->user->email)->send(new CartReminderMail(
            customerName: $cart->user->name,
            items:        $items,
            total:        round(collect($items)->sum('subtotal'), 2),
            cartUrl:      route('visitor.index'),
        ));

        Log::info('Cart reminder email sent.', ['cart_id' => $cart->id, 'user_id' => $cart->user_id]);
    }
}