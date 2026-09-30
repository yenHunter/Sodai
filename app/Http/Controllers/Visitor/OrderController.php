<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Visitor\CustomerOrderService;
use App\Traits\Visitor\EnsuresCustomerOwnership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use EnsuresCustomerOwnership;

    public function __construct(
        private CustomerOrderService $orderService
    ) {}

    public function index()
    {
        $orders = $this->orderService->getOrders(Auth::guard('customer')->user());

        return view('visitor.pages.user-order-history', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->ensureOwnedByCustomer($order);

        $order = $this->orderService->getOrderWithDetails($order);

        $trackingSteps = $this->buildTrackingSteps($order->status);
        $canCancel = $this->orderService->canBeCancelledByCustomer($order);

        return view('visitor.pages.user-order-details', compact('order', 'trackingSteps', 'canCancel'));
    }

    public function cancel(Request $request, Order $order)
    {
        $this->ensureOwnedByCustomer($order);

        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->orderService->cancelOrder(
                $order,
                Auth::guard('customer')->user(),
                $validated['cancel_reason'] ?? null
            );

            return redirect()
                ->route('visitor.account.orders.show', $order)
                ->with('success', 'Your order has been cancelled.');
        } catch (\Exception $e) {
            return redirect()
                ->route('visitor.account.orders.show', $order)
                ->with('error', $e->getMessage());
        }
    }

    private function buildTrackingSteps(string $status): array
    {
        $steps = [
            'pending' => 'Order Placed',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
        ];

        $order = array_keys($steps);
        $currentIdx = array_search($status, $order, true);

        // Cancelled/refunded orders never reach a linear step, so no step is "current".
        $currentIdx = $currentIdx === false ? -1 : $currentIdx;

        return collect($steps)->values()->map(function ($label, $i) use ($currentIdx) {
            return [
                'label' => $label,
                'icon' => $i + 1,
                'active' => $currentIdx >= 0 && $i <= $currentIdx,
            ];
        })->toArray();
    }
}
