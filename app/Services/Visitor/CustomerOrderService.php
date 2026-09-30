<?php

namespace App\Services\Visitor;

use App\Models\Order;
use App\Models\User;
use App\Services\Admin\OrderService;

class CustomerOrderService
{
    /**
     * Customers may only self-cancel before fulfilment work starts.
     * Once processing/shipped, cancellation must go through support.
     */
    private const CUSTOMER_CANCELLABLE_STATUSES = ['pending', 'confirmed'];

    public function __construct(
        private OrderService $orderService
    ) {}

    public function getOrders(User $customer)
    {
        return Order::where('user_id', $customer->id)
            ->withCount('items')
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function getOrderWithDetails(Order $order): Order
    {
        return $order->load(['items.product', 'statusHistories.admin']);
    }

    /**
     * Customer-initiated cancellation. Ownership is enforced here (not just
     * in the controller) so the rule holds for any future caller, and the
     * actual stock-restore + history bookkeeping is delegated to the same
     * OrderService::cancel() the admin panel uses.
     */
    public function cancelOrder(Order $order, User $customer, ?string $reason = null): Order
    {
        if ((int) $order->user_id !== (int) $customer->id) {
            abort(403, 'You are not authorized to cancel this order.');
        }

        if (! in_array($order->status, self::CUSTOMER_CANCELLABLE_STATUSES, true)) {
            throw new \Exception(
                "This order can no longer be cancelled because it is already {$order->status}. Please contact support."
            );
        }

        return $this->orderService->cancel($order, $reason ?? 'Cancelled by customer.');
    }

    /**
     * Whether the customer UI should offer the cancel action for this order.
     */
    public function canBeCancelledByCustomer(Order $order): bool
    {
        return in_array($order->status, self::CUSTOMER_CANCELLABLE_STATUSES, true);
    }
}
