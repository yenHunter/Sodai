<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Visitor\Review\StoreReviewRequest;
use App\Http\Requests\Visitor\Review\UpdateReviewRequest;
use App\Models\Review;
use App\Services\Visitor\CustomerReviewService;
use App\Traits\Visitor\EnsuresCustomerOwnership;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    use EnsuresCustomerOwnership;

    public function __construct(
        private CustomerReviewService $reviewService
    ) {}

    public function index()
    {
        $customer = Auth::guard('customer')->user();
        $reviews = $this->reviewService->getCustomerReviews($customer);
        $reviewable = $this->reviewService->getReviewableItems($customer);

        return view('visitor.pages.user-reviews', compact('reviews', 'reviewable'));
    }

    public function store(StoreReviewRequest $request)
    {
        try {
            $this->reviewService->store(Auth::guard('customer')->user(), $request->validated());

            return redirect()->back()->with('success', 'Review submitted. It will appear once approved.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->ensureOwnedByCustomer($review);

        try {
            $this->reviewService->update($review, $request->validated());

            return redirect()->back()->with('success', 'Review updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Review $review)
    {
        $this->ensureOwnedByCustomer($review);

        $this->reviewService->delete($review);

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}
