<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Visitor\Checkout\StoreCheckoutRequest;
use App\Services\Visitor\AddressService;
use App\Services\Visitor\CartService;
use App\Services\Visitor\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private CheckoutService $checkoutService,
        private AddressService $addressService
    ) {}

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $cart = $this->cartService->getOrCreateCart($customer, $request->session()->getId());
        $cart = $this->cartService->getCartWithItems($cart);

        if ($cart->items->isEmpty()) {
            return redirect()->route('visitor.cart.index')->with('error', 'Your cart is empty.');
        }

        $addresses = $customer ? $this->addressService->getAddresses($customer) : collect();
        $summary = $this->checkoutService->getCheckoutSummary($cart);
        $allowGuestCheckout = (bool) setting('order', 'allow_guest_checkout', false);

        return view('visitor.pages.checkout', compact('cart', 'addresses', 'summary', 'allowGuestCheckout'));
    }

    public function store(StoreCheckoutRequest $request)
    {
        $customer = Auth::guard('customer')->user();

        $cart = $this->cartService->getOrCreateCart($customer, $request->session()->getId());
        $cart = $this->cartService->getCartWithItems($cart);

        try {
            $order = $this->checkoutService->placeOrder($cart, $customer, $request->validated());

            $request->session()->put('last_order_id', $order->id);

            return redirect()
                ->route('visitor.order-confirm')
                ->with('success', 'Your order has been placed successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->route('visitor.checkout')
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => ['required', 'string', 'max:50']]);

        $customer = Auth::guard('customer')->user();
        $cart = $this->cartService->getOrCreateCart($customer, $request->session()->getId());
        $cart = $this->cartService->getCartWithItems($cart);
        $subtotal = $this->cartService->getCartTotal($cart);

        try {
            $result = $this->checkoutService->previewCoupon($request->input('code'), $customer, $subtotal);

            return response()->json(['success' => true] + $result);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }
}
