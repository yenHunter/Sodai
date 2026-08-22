<?php

namespace App\Services\Visitor;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerReviewService
{
    public function getCustomerReviews(User $customer)
    {
        return Review::where('user_id', $customer->id)
            ->with('product:id,name,thumbnail')
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    /**
     * Products a customer has purchased (in a delivered order) but hasn't reviewed yet.
     */
    public function getReviewableItems(User $customer)
    {
        $deliveredOrderIds = Order::where('user_id', $customer->id)
            ->where('status', 'delivered')
            ->pluck('id');

        $reviewedPairs = Review::where('user_id', $customer->id)
            ->get(['order_id', 'product_id'])
            ->map(fn ($r) => "{$r->order_id}-{$r->product_id}")
            ->toArray();

        return OrderItem::whereIn('order_id', $deliveredOrderIds)
            ->with('product:id,name,thumbnail')
            ->get()
            ->reject(fn ($item) => in_array("{$item->order_id}-{$item->product_id}", $reviewedPairs))
            ->values();
    }

    public function store(User $customer, array $data): Review
    {
        return DB::transaction(function () use ($customer, $data) {
            $order = Order::where('id', $data['order_id'])
                ->where('user_id', $customer->id)
                ->where('status', 'delivered')
                ->first();

            if (! $order) {
                throw new \Exception('You can only review products from your delivered orders.');
            }

            $purchased = OrderItem::where('order_id', $order->id)
                ->where('product_id', $data['product_id'])
                ->exists();

            if (! $purchased) {
                throw new \Exception('This product was not part of the selected order.');
            }

            $alreadyReviewed = Review::where('user_id', $customer->id)
                ->where('order_id', $order->id)
                ->where('product_id', $data['product_id'])
                ->exists();

            if ($alreadyReviewed) {
                throw new \Exception('You have already reviewed this product for this order.');
            }

            $review = Review::create([
                'product_id' => $data['product_id'],
                'user_id' => $customer->id,
                'order_id' => $order->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'status' => 'pending',
            ]);

            return $review;
        });
    }

    public function update(Review $review, array $data): Review
    {
        if ($review->status !== 'pending') {
            throw new \Exception('Only pending reviews can be edited.');
        }

        $review->update([
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        return $review->fresh();
    }

    public function delete(Review $review): bool
    {
        return $review->delete();
    }
}
