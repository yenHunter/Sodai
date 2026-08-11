<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Services\Admin\ReviewService;
use App\Http\Controllers\Controller;

class ReviewController extends Controller
{
    public function __construct(
        private ReviewService $reviewService
    ) {}

    public function index(Request $request)
    {
        $reviews = $this->reviewService->getReviewsList($request->only(['search', 'status', 'rating']));
        $stats   = $this->reviewService->getReviewStats();

        return view('admin.ecommerce.review.index', compact('reviews', 'stats'));
    }

    public function approve(Review $review)
    {
        try {
            $this->reviewService->approve($review);

            return redirect()->route('admin.ecommerce.review.index')->with('success', 'Review approved.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.review.index')->with('error', $e->getMessage());
        }
    }

    public function reject(Review $review)
    {
        try {
            $this->reviewService->reject($review);

            return redirect()->route('admin.ecommerce.review.index')->with('success', 'Review rejected.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.review.index')->with('error', $e->getMessage());
        }
    }

    public function destroy(Review $review)
    {
        try {
            $this->reviewService->delete($review);

            return redirect()->route('admin.ecommerce.review.index')->with('success', 'Review deleted.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ecommerce.review.index')->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')), fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return redirect()->route('admin.ecommerce.review.index')->with('error', 'No reviews selected.');
        }

        $count = 0;
        foreach (Review::whereIn('id', $ids)->get() as $review) {
            $this->reviewService->delete($review);
            $count++;
        }

        return redirect()->route('admin.ecommerce.review.index')->with('success', "{$count} review(s) deleted.");
    }
}