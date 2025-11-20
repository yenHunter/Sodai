<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Exception;

class CancelOrderAction
{
    public function execute(Order $order): Order
    {
        // 1. Validation: Cannot cancel if already shipped
        if ($order->status === OrderStatus::SHIPPED || $order->status === OrderStatus::DELIVERED) {
            throw new Exception("Cannot cancel an order that has already been shipped or delivered.");
        }

        if ($order->status === OrderStatus::CANCELLED) {
            throw new Exception("Order is already cancelled.");
        }

        return DB::transaction(function () use ($order) {
            // 2. Restore Inventory
            foreach ($order->items as $item) {
                $item->variant->increment('stock_quantity', $item->quantity);
            }

            // 3. Update Status
            $order->update(['status' => OrderStatus::CANCELLED]);

            return $order;
        });
    }
}
