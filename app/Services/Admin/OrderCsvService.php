<?php

namespace App\Services\Admin;

use App\Models\Order;

/**
 * CSV export for the admin orders index.
 *
 * One row per order, using the order's shipping snapshot rather than the
 * customer's current profile, so historical exports stay accurate. Filters
 * are delegated to OrderService::applyIndexFilters(), the same method the
 * index page uses, so a filtered download matches the table exactly.
 */
class OrderCsvService
{
    public const COLUMNS = [
        'order_number',
        'order_date',
        'status',
        'payment_method',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_city',
        'shipping_state',
        'shipping_country',
        'items_quantity',
        'items',
        'subtotal',
        'discount',
        'coupon_code',
        'shipping',
        'tax',
        'total',
        'tracking_number',
    ];

    public function __construct(private OrderService $orderService) {}

    /**
     * Writes the orders matching $filters to an open stream. Chunked so a
     * large order history is never loaded in full.
     *
     * @param  array{search?: string|null, status?: string|null, date_from?: string|null, date_to?: string|null}  $filters
     */
    public function writeExport($handle, array $filters = []): void
    {
        fputcsv($handle, self::COLUMNS, ',', '"', '');

        $query = Order::query()->with('items');

        $this->orderService->applyIndexFilters($query, $filters);

        $query->orderByDesc('orders.id')->chunk(200, function ($orders) use ($handle) {
            foreach ($orders as $order) {
                fputcsv($handle, $this->exportRow($order), ',', '"', '');
            }
        });
    }

    private function exportRow(Order $order): array
    {
        return [
            $order->order_number,
            $order->created_at?->format('Y-m-d H:i'),
            $order->status,
            $order->payment_method,
            $order->shipping_name,
            $order->shipping_email,
            $order->shipping_phone,
            $order->shipping_city,
            $order->shipping_state,
            $order->shipping_country,
            $order->items->sum('quantity'),
            $order->items
                ->map(fn ($item) => "{$item->product_name} ({$item->product_sku}) x{$item->quantity}")
                ->implode(' | '),
            number_format((float) $order->subtotal, 2, '.', ''),
            number_format((float) $order->discount_amount, 2, '.', ''),
            $order->coupon_code ?? '',
            number_format((float) $order->shipping_charge, 2, '.', ''),
            number_format((float) $order->tax_amount, 2, '.', ''),
            number_format((float) $order->total_amount, 2, '.', ''),
            $order->tracking_number ?? '',
        ];
    }
}
