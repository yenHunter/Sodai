<?php

namespace App\Http\Controllers\Visitor;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Services\Visitor\WishlistService;
use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
    public function __construct(
        private WishlistService $wishlistService
    ) {}

    public function index()
    {
        $wishlist = $this->wishlistService->getWishlist(Auth::guard('customer')->user());

        return view('visitor.pages.user-wishlist', compact('wishlist'));
    }

    public function toggle(Product $product)
    {
        $result = $this->wishlistService->toggle(Auth::guard('customer')->user(), $product);

        return response()->json([
            'success' => true,
            'added'   => $result['added'],
            'message' => $result['added'] ? 'Added to wishlist.' : 'Removed from wishlist.',
        ]);
    }

    public function destroy(Product $product)
    {
        $this->wishlistService->remove(Auth::guard('customer')->user(), $product);

        return redirect()->back()->with('success', 'Removed from wishlist.');
    }
}