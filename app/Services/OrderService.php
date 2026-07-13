<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class OrderService
{
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Order
    {
        try {
            return DB::transaction(function () use ($data) {

                $items    = $this->buildItemsWithStockLock($data['items']);
                $subtotal = collect($items)->sum('total_price');

                $order = Order::create([
                    'order_number'     => $this->generateUniqueOrderNumber(),
                    'user_id'          => $data['user_id'],
                    'status'           => 'pending',
                    'subtotal'         => $subtotal,
                    'discount_amount'  => $data['discount_amount'] ?? 0,
                    'shipping_charge'  => $data['shipping_charge'] ?? 0,
                    'tax_amount'       => $data['tax_amount'] ?? 0,
                    'total_amount'     => $this->calculateTotal($subtotal, $data),
                    'coupon_code'      => $data['coupon_code'] ?? null,
                    'shipping_name'    => $data['shipping_name'],
                    'shipping_email'   => $data['shipping_email'],
                    'shipping_phone'   => $data['shipping_phone'],
                    'shipping_address' => $data['shipping_address'],
                    'shipping_city'    => $data['shipping_city'],
                    'shipping_state'   => $data['shipping_state'],
                    'shipping_zip'     => $data['shipping_zip'],
                    'shipping_country' => $data['shipping_country'],
                    'notes'            => $data['notes'] ?? null,
                ]);

                $this->persistItemsAndAdjustStock($order, $items, decrement: true);

                Log::info('Order created successfully.', [
                    'order_id'     => $order->id,
                    'order_number' => $order->order_number,
                ]);

                return $order->fresh(['items', 'user']);
            });
        } catch (\Exception $e) {
            Log::error('Order creation failed.', ['exception' => $e]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Order $order, array $data): Order
    {
        if (!$order->isEditable()) {
            throw new \Exception('This order can no longer be edited because of its current status.');
        }

        try {
            return DB::transaction(function () use ($order, $data) {

                // Restore stock from previously placed items before reapplying new ones.
                $this->restoreStockForItems($order->items);
                $order->items()->delete();

                $items    = $this->buildItemsWithStockLock($data['items']);
                $subtotal = collect($items)->sum('total_price');

                $order->update([
                    'user_id'          => $data['user_id'],
                    'subtotal'         => $subtotal,
                    'discount_amount'  => $data['discount_amount'] ?? 0,
                    'shipping_charge'  => $data['shipping_charge'] ?? 0,
                    'tax_amount'       => $data['tax_amount'] ?? 0,
                    'total_amount'     => $this->calculateTotal($subtotal, $data),
                    'coupon_code'      => $data['coupon_code'] ?? null,
                    'shipping_name'    => $data['shipping_name'],
                    'shipping_email'   => $data['shipping_email'],
                    'shipping_phone'   => $data['shipping_phone'],
                    'shipping_address' => $data['shipping_address'],
                    'shipping_city'    => $data['shipping_city'],
                    'shipping_state'   => $data['shipping_state'],
                    'shipping_zip'     => $data['shipping_zip'],
                    'shipping_country' => $data['shipping_country'],
                    'notes'            => $data['notes'] ?? null,
                ]);

                $this->persistItemsAndAdjustStock($order, $items, decrement: true);

                Log::info('Order updated successfully.', ['order_id' => $order->id]);

                return $order->fresh(['items', 'user']);
            });
        } catch (\Exception $e) {
            Log::error('Order update failed.', ['exception' => $e, 'order_id' => $order->id]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // STATUS TRANSITIONS
    // ─────────────────────────────────────────────

    public function updateStatus(Order $order, string $status): Order
    {
        if (!in_array($status, Order::STATUSES)) {
            throw new \Exception('Invalid order status.');
        }

        if ($status === 'cancelled') {
            return $this->cancel($order, null);
        }

        return DB::transaction(function () use ($order, $status) {
            $update = ['status' => $status];

            if ($status === 'shipped')   $update['shipped_at']   = now();
            if ($status === 'delivered') $update['delivered_at'] = now();

            if ($status === 'refunded' && $order->isCancellable()) {
                $this->restoreStockForItems($order->items);
            }

            $order->update($update);

            Log::info('Order status updated.', ['order_id' => $order->id, 'status' => $status]);

            return $order->fresh();
        });
    }

    public function cancel(Order $order, ?string $reason): Order
    {
        if (!$order->isCancellable()) {
            throw new \Exception('This order cannot be cancelled from its current status.');
        }

        return DB::transaction(function () use ($order, $reason) {
            $this->restoreStockForItems($order->items);

            $order->update([
                'status'        => 'cancelled',
                'cancelled_at'  => now(),
                'cancel_reason' => $reason,
            ]);

            Log::info('Order cancelled.', ['order_id' => $order->id, 'reason' => $reason]);

            return $order->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Order $order): bool
    {
        if (!$order->isDeletable()) {
            throw new \Exception('Only cancelled or refunded orders can be deleted.');
        }

        return $order->delete();
    }

    // ─────────────────────────────────────────────
    // QUICK CUSTOMER CREATE (POS convenience —
    // stopgap until customer self-registration ships)
    // ─────────────────────────────────────────────

    public function quickCreateCustomer(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make(Str::random(24)),
            'status'   => 'active',
        ]);
    }

    // ─────────────────────────────────────────────
    // STOCK / ITEM HELPERS
    // ─────────────────────────────────────────────

    /**
     * Locks each product row, validates stock, and builds item
     * payloads with server-trusted pricing. Does NOT persist or
     * mutate stock yet — call persistItemsAndAdjustStock() after.
     */
    private function buildItemsWithStockLock(array $submittedItems): array
    {
        $items = [];

        // Merge duplicate product_id entries (same product added twice).
        $merged = [];
        foreach ($submittedItems as $row) {
            $pid = (int) $row['product_id'];
            $merged[$pid] = ($merged[$pid] ?? 0) + (int) $row['quantity'];
        }

        foreach ($merged as $productId => $quantity) {
            $product = Product::where('id', $productId)->lockForUpdate()->first();

            if (!$product) {
                throw new \Exception("Product #{$productId} no longer exists.");
            }

            if ($product->stock_quantity < $quantity) {
                throw new \Exception("Insufficient stock for \"{$product->name}\" (available: {$product->stock_quantity}, requested: {$quantity}).");
            }

            $unitPrice = $product->final_price;

            $items[] = [
                'product'       => $product,
                'product_id'    => $product->id,
                'product_name'  => $product->name,
                'product_sku'   => $product->sku,
                'product_image' => $product->thumbnail,
                'unit_price'    => $unitPrice,
                'quantity'      => $quantity,
                'total_price'   => round($unitPrice * $quantity, 2),
            ];
        }

        return $items;
    }

    private function persistItemsAndAdjustStock(Order $order, array $items, bool $decrement): void
    {
        foreach ($items as $item) {
            OrderItem::create([
                'order_id'      => $order->id,
                'product_id'    => $item['product_id'],
                'product_name'  => $item['product_name'],
                'product_sku'   => $item['product_sku'],
                'product_image' => $item['product_image'],
                'unit_price'    => $item['unit_price'],
                'quantity'      => $item['quantity'],
                'total_price'   => $item['total_price'],
            ]);

            if ($decrement) {
                /** @var Product $product */
                $product = $item['product'];
                $product->decrementStock($item['quantity']);
                $product->increment('total_sales', $item['quantity']);
            }
        }
    }

    private function restoreStockForItems($items): void
    {
        foreach ($items as $item) {
            $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
            if ($product) {
                $product->incrementStock($item->quantity);
                $product->decrement('total_sales', min($item->quantity, $product->total_sales));
            }
        }
    }

    private function calculateTotal(float $subtotal, array $data): float
    {
        $total = $subtotal
            - (float) ($data['discount_amount'] ?? 0)
            + (float) ($data['shipping_charge'] ?? 0)
            + (float) ($data['tax_amount'] ?? 0);

        return max(0, round($total, 2));
    }

    // ─────────────────────────────────────────────
    // ORDER NUMBER
    // ─────────────────────────────────────────────

    private function generateUniqueOrderNumber(): string
    {
        $lastNumber = Order::where('order_number', 'like', 'ORD-%')
            ->lockForUpdate()
            ->orderBy('order_number', 'desc')
            ->value('order_number');

        $next = 1;
        if ($lastNumber) {
            $next = (int) substr($lastNumber, strrpos($lastNumber, '-') + 1) + 1;
        }

        return 'ORD-' . str_pad($next, 6, '0', STR_PAD_LEFT);
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getOrdersList(array $filters = [])
    {
        $query = Order::with('user')->withCount('items');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->ofStatus($filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate(15)->withQueryString();
    }

    public function getOrderForEdit(Order $order): Order
    {
        return $order->load(['items.product', 'user']);
    }

    public function getOrderForDetails(Order $order): Order
    {
        return $order->load(['items.product', 'user']);
    }
}