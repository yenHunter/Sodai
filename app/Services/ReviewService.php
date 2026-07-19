<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    public function approve(Review $review): Review
    {
        $review->update(['status' => 'approved']);
        $this->recalculateProductRating($review->product_id);

        Log::info('Review approved.', ['review_id' => $review->id]);

        return $review->fresh();
    }

    public function reject(Review $review): Review
    {
        $review->update(['status' => 'rejected']);
        $this->recalculateProductRating($review->product_id);

        Log::info('Review rejected.', ['review_id' => $review->id]);

        return $review->fresh();
    }

    public function delete(Review $review): bool
    {
        $productId = $review->product_id;
        $deleted   = $review->delete();

        $this->recalculateProductRating($productId);

        return $deleted;
    }

    private function recalculateProductRating(int $productId): void
    {
        $product = \App\Models\Product::find($productId);
        if (!$product) return;

        $approved = Review::where('product_id', $productId)->approved();

        $product->update([
            'average_rating' => round($approved->avg('rating') ?? 0, 2),
            'review_count'   => $approved->count(),
        ]);
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getReviewsList(array $filters = [])
    {
        $query = Review::with(['product:id,name,thumbnail', 'user:id,name,email']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['rating'])) {
            $query->ofRating($filters['rating']);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    public function getReviewStats(): array
    {
        $counts = Review::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        return [
            'total'    => (int) $counts->sum(),
            'pending'  => (int) ($counts->get('pending') ?? 0),
            'approved' => (int) ($counts->get('approved') ?? 0),
            'rejected' => (int) ($counts->get('rejected') ?? 0),
        ];
    }
}