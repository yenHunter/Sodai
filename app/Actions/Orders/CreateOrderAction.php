<?php

namespace App\Actions\Orders;

use App\Models\User;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Exception;

class CreateOrderAction
{
    public function execute(User $user, array $itemsData): Order
    {
        return DB::transaction(function () use ($user, $itemsData) {
            
            $totalAmount = 0;
            $orderItemsPayload = [];

            foreach ($itemsData as $item) {
                // 1. PESSIMISTIC LOCKING
                // We use lockForUpdate() so no other request can modify this row 
                // until this transaction finishes.
                $variant = ProductVariant::where('id', $item['variant_id'])
                    ->lockForUpdate() 
                    ->firstOrFail();

                // 2. Validate Stock
                if ($variant->stock_quantity < $item['quantity']) {
                    throw new Exception("Insufficient stock for SKU: {$variant->sku}");
                }

                // 3. Deduct Inventory
                $variant->decrement('stock_quantity', $item['quantity']);

                // 4. Calculate Price (Use the database price, not what user sent)
                $lineTotal = $variant->price * $item['quantity'];
                $totalAmount += $lineTotal;

                $orderItemsPayload[] = [
                    'product_variant_id' => $variant->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $variant->price,
                ];
            }

            // 5. Create Order
            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::PENDING,
                'total_amount' => $totalAmount,
                'invoice_number' => 'INV-' . time() . '-' . $user->id,
            ]);

            // 6. Save Items
            $order->items()->createMany($orderItemsPayload);

            return $order;
        });
    }
}
