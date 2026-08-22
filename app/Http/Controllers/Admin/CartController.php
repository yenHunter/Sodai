<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\Admin\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(Request $request)
    {
        $carts = $this->cartService->getCartsList($request->only(['search']));

        return view('admin.ecommerce.cart.index', compact('carts'));
    }

    public function show(Cart $cart)
    {
        return response()->json($this->cartService->getCartForDetails($cart));
    }

    public function destroy(Cart $cart)
    {
        try {
            $this->cartService->delete($cart);

            return redirect()
                ->route('admin.ecommerce.cart.index')
                ->with('success', 'Cart deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.cart.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')), fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return redirect()->route('admin.ecommerce.cart.index')->with('error', 'No carts selected.');
        }

        $count = Cart::whereIn('id', $ids)->delete();

        return redirect()
            ->route('admin.ecommerce.cart.index')
            ->with('success', "{$count} cart(s) deleted successfully.");
    }

    public function sendReminder(Cart $cart)
    {
        try {
            $this->cartService->sendReminderEmail($cart);

            return redirect()
                ->route('admin.ecommerce.cart.index')
                ->with('success', 'Reminder email sent to the customer.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.cart.index')
                ->with('error', 'Failed to send email: '.$e->getMessage());
        }
    }
}
