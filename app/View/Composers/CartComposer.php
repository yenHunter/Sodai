<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Services\Visitor\CartService;

class CartComposer
{
    public function __construct(private CartService $cartService) {}

    public function compose(View $view): void
    {
        $customer  = Auth::guard('customer')->user();
        $sessionId = session()->getId();

        $cart = $this->cartService->getExistingCart($customer, $sessionId);
        $cart = $cart ? $this->cartService->getCartWithItems($cart) : null;

        $view->with('miniCart', $cart);
        $view->with('miniCartCount', $cart?->items->sum('quantity') ?? 0);
        $view->with('miniCartTotal', $cart ? $this->cartService->getCartTotal($cart) : 0);
    }
}