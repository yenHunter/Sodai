<?php

namespace App\Services\Visitor;

use App\Models\Order;
use App\Models\User;

class CustomerOrderService
{
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
}