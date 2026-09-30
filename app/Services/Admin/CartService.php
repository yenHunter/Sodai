<?php

namespace App\Services\Admin;

use App\Mail\Admin\CartReminderMail;
use App\Models\Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CartService
{
    public function getCartsList(array $filters = [])
    {
        $query = Cart::with(['user', 'items.product'])->whereHas('items');

        if (! empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->latest('updated_at')->paginate(15)->withQueryString();
    }

    public function getCartForDetails(Cart $cart): array
    {
        $cart->load(['user', 'items.product']);

        $items = $cart->items->map(fn ($item) => [
            'product_name' => $item->product?->name ?? 'Deleted Product',
            'product_sku' => $item->product?->sku ?? '—',
            'thumbnail_url' => $item->product?->thumbnail_url,
            'unit_price' => $item->unit_price,
            'quantity' => $item->quantity,
            'subtotal' => $item->subtotal,
        ]);

        return [
            'customer_name' => $cart->user?->name ?? 'Guest',
            'customer_email' => $cart->user?->email,
            'items' => $items,
            'total' => round($items->sum('subtotal'), 2),
            'updated_at' => $cart->updated_at->format('d M Y, h:i A'),
        ];
    }

    public function delete(Cart $cart): bool
    {
        return $cart->delete();
    }

    public function sendReminderEmail(Cart $cart): void
    {
        $cart->load(['user', 'items.product']);

        if (! $cart->user || ! $cart->user->email) {
            throw new \Exception('This cart has no registered customer to email.');
        }

        if ($cart->items->isEmpty()) {
            throw new \Exception('This cart is empty.');
        }

        $this->queueReminder($cart);
    }

    /**
     * Carts with at least one item, owned by a registered customer,
     * inactive for at least $hours, not already reminded since their
     * last activity, and with every item still in stock.
     */
    public function getAbandonedCarts(int $hours): Collection
    {
        return Cart::query()
            ->whereNotNull('user_id')
            ->whereHas('items')
            ->where('updated_at', '<=', now()->subHours($hours))
            ->where(fn ($q) => $q->whereNull('reminder_sent_at')
                ->orWhere('reminder_sent_at', '<', DB::raw('carts.updated_at')))
            ->with(['user', 'items.variant'])
            ->get();
    }

    /**
     * Queue a reminder mailable for the cart (shared by the manual admin
     * send and the scheduled auto-send) and stamp reminder_sent_at.
     * Silently skips carts with no mailable owner, no items, or any
     * out-of-stock item — reminding a customer about an unavailable
     * product builds distrust.
     */
    public function queueReminder(Cart $cart): bool
    {
        if (! $cart->user || ! $cart->user->email) {
            return false; // guest carts have nobody to email
        }

        $cart->load('items.variant');

        if ($cart->items->isEmpty()) {
            return false;
        }

        if ($cart->items->contains(fn ($item) => ! ($item->variant?->is_in_stock ?? false))) {
            return false;
        }

        $items = $cart->items->map(fn ($item) => [
            'name' => $item->product?->name ?? 'Product',
            'quantity' => $item->quantity,
            'subtotal' => $item->subtotal,
        ])->toArray();

        Mail::to($cart->user->email)->queue(new CartReminderMail(
            customerName: $cart->user->name,
            items: $items,
            total: round(collect($items)->sum('subtotal'), 2),
            cartUrl: route('visitor.index'),
        ));

        $cart->forceFill(['reminder_sent_at' => now()])->save();

        Log::info('Cart reminder email queued.', ['cart_id' => $cart->id, 'user_id' => $cart->user_id]);

        return true;
    }
}
