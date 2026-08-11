<?php

namespace App\Http\Controllers\Visitor;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Visitor\CartService;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(Request $request)
    {
        $cart = $this->cartService->getOrCreateCart(
            Auth::guard('customer')->user(),
            $request->session()->getId()
        );

        $cart  = $this->cartService->getCartWithItems($cart);
        $total = $this->cartService->getCartTotal($cart);

        return view('visitor.pages.cart', compact('cart', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity'           => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->cartService->getOrCreateCart(
            Auth::guard('customer')->user(),
            $request->session()->getId()
        );

        try {
            $this->cartService->addItem(
                $cart,
                $request->input('product_variant_id'),
                $request->input('quantity')
            );

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Added to cart.']);
            }

            return redirect()->back()->with('success', 'Added to cart.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem, $request);

        $request->validate(['quantity' => ['required', 'integer', 'min:1']]);

        try {
            $this->cartService->updateQuantity($cartItem, $request->input('quantity'));

            return redirect()->back()->with('success', 'Cart updated.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem, $request);

        $this->cartService->removeItem($cartItem);

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    /**
     * Cart items have no direct customer ownership column, only via
     * cart -> user_id/session_id, so ownership must be checked through
     * the parent cart rather than a simple column match.
     */
    private function authorizeCartItem(CartItem $cartItem, Request $request): void
    {
        $cart = $cartItem->cart;
        $customerId = Auth::guard('customer')->id();

        $owned = $customerId
            ? $cart->user_id === $customerId
            : $cart->session_id === $request->session()->getId();

        abort_unless($owned, 403, 'You are not authorized to modify this cart.');
    }
}