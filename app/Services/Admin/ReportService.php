<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    /**
     * Revenue-sharing statuses: only these order states count as sales.
     * Pending/confirmed haven't been fulfilled, cancelled/refunded release revenue.
     */
    private const SALES_STATUSES = ['processing', 'shipped', 'delivered'];

    /**
     * Daily sales aggregates between two dates (inclusive), keyed off the
     * order's created_at. Days without activity are returned as zero rows so
     * the table/chart renders a continuous series.
     *
     * @return Collection<int, array{date: string, orders_count: int, items_sold: int, revenue: float, discounts: float}>
     */
    public function getSalesByDate(string $from, string $to): Collection
    {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();

        $rows = Order::query()
            ->whereIn('status', self::SALES_STATUSES)
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as orders_count')
            ->selectRaw('COALESCE(SUM(total_amount), 0) as revenue')
            ->selectRaw('COALESCE(SUM(discount_amount), 0) as discounts')
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->keyBy('date');

        $itemsByDate = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', self::SALES_STATUSES)
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('DATE(orders.created_at) as date')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as items_sold')
            ->groupByRaw('DATE(orders.created_at)')
            ->pluck('items_sold', 'date');

        $sales = collect();
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $date = $day->toDateString();
            $row = $rows->get($date);

            $sales->push([
                'date' => $date,
                'orders_count' => (int) ($row->orders_count ?? 0),
                'items_sold' => (int) $itemsByDate->get($date, 0),
                'revenue' => (float) ($row->revenue ?? 0),
                'discounts' => (float) ($row->discounts ?? 0),
            ]);
        }

        return $sales;
    }

    /**
     * Products ranked by units sold within a date window (inclusive).
     * Aggregated from order_items snapshots, so rows stay correct even after
     * the product/variant is renamed, deleted, or its price changes later.
     * `product_missing` flags snapshots whose product row no longer resolves
     * (deleted product or legacy row), so the view can badge them.
     *
     * @return Collection<int, object{product_id: ?int, product_name: string, product_sku: ?string, variant_options: ?string, quantity: int, revenue: float, orders_count: int, product_missing: bool}>
     */
    public function getTopProducts(string $from, string $to, int $limit = 10): Collection
    {
        $from = Carbon::parse($from)->startOfDay();
        $to = Carbon::parse($to)->endOfDay();

        return OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('products', 'products.id', '=', 'order_items.product_id')
            ->whereIn('orders.status', self::SALES_STATUSES)
            ->whereBetween('orders.created_at', [$from, $to])
            ->selectRaw('order_items.product_id')
            ->selectRaw('order_items.product_name')
            ->selectRaw('MAX(order_items.product_sku) as product_sku')
            ->selectRaw('MAX(order_items.variant_options) as variant_options')
            ->selectRaw('SUM(order_items.quantity) as quantity')
            ->selectRaw('COALESCE(SUM(order_items.total_price), 0) as revenue')
            ->selectRaw('COUNT(DISTINCT order_items.order_id) as orders_count')
            ->selectRaw('MAX(CASE WHEN products.id IS NULL OR products.deleted_at IS NOT NULL THEN 1 ELSE 0 END) as product_missing')
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('quantity')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->quantity = (int) $row->quantity;
                $row->revenue = (float) $row->revenue;
                $row->orders_count = (int) $row->orders_count;
                $row->product_missing = (bool) $row->product_missing;

                return $row;
            });
    }

    /**
     * Low-stock report: every active variant that needs restocking, i.e.
     * out-of-stock (0 units) or low-stock per its own threshold — matching
     * ProductVariant::is_low_stock / is_out_of_stock semantics. Rows are
     * sorted with the most critical (least stock) first.
     *
     * @return Collection<int, array{variant: ProductVariant, product: Product, stock_quantity: int, threshold: int, is_out_of_stock: bool}>
     */
    public function getLowStockVariants(): Collection
    {
        return ProductVariant::query()
            ->active()
            ->where(function ($q) {
                $q->where('stock_quantity', '<=', 0)
                    ->orWhere(function ($q2) {
                        $q2->where('stock_quantity', '>', 0)
                            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
                    });
            })
            ->whereHas('product', fn ($q) => $q->active())
            ->with(['product' => fn ($q) => $q->select('id', 'name', 'slug')])
            ->orderBy('stock_quantity')
            ->get()
            ->map(function (ProductVariant $variant) {
                return [
                    'variant' => $variant,
                    'product' => $variant->product,
                    'stock_quantity' => $variant->stock_quantity,
                    'threshold' => (int) $variant->low_stock_threshold,
                    'is_out_of_stock' => $variant->stock_quantity <= 0,
                ];
            });
    }

    /**
     * Full metrics bundle for the previous Mon–Sun week, used by the
     * scheduled weekly sales summary email. Low-stock is a live snapshot
     * (not a week-window metric) so the recipient sees current urgency.
     */
    public function getWeeklySummary(): array
    {
        $start = now()->subWeek()->startOfWeek(); // previous Monday 00:00
        $end = $start->copy()->endOfWeek();       // previous Sunday 23:59:59

        return $this->buildPeriodSummary($start, $end);
    }

    /**
     * Full metrics bundle for the previous calendar month, used by the
     * scheduled monthly sales summary email (sent on the 1st). Anchoring on
     * the current month's 1st before stepping back keeps the window
     * overflow-proof even when the command runs on the 29th–31st.
     */
    public function getMonthlySummary(): array
    {
        $start = now()->startOfMonth()->subMonth(); // 1st of previous month 00:00
        $end = $start->copy()->endOfMonth();        // last day of previous month 23:59:59

        return $this->buildPeriodSummary($start, $end);
    }

    /**
     * Shared aggregation behind the scheduled weekly/monthly summaries.
     *
     * @return array{period_start: Carbon, period_end: Carbon, revenue: float, discounts: float, orders: int, items_sold: int, avg_order_value: float, top_products: Collection, low_stock_count: int}
     */
    private function buildPeriodSummary(Carbon $start, Carbon $end): array
    {
        $sales = $this->getSalesByDate($start->toDateString(), $end->toDateString());

        $orders = (int) $sales->sum('orders_count');
        $revenue = (float) $sales->sum('revenue');

        return [
            'period_start' => $start->copy()->startOfDay(),
            'period_end' => $end,
            'revenue' => $revenue,
            'discounts' => (float) $sales->sum('discounts'),
            'orders' => $orders,
            'items_sold' => (int) $sales->sum('items_sold'),
            'avg_order_value' => $orders > 0 ? round($revenue / $orders, 2) : 0.0,
            'top_products' => $this->getTopProducts($start->toDateString(), $end->toDateString(), 5),
            'low_stock_count' => $this->getLowStockVariants()->count(),
        ];
    }

    /**
     * Headline numbers for the reports hub page (last 30 days of sales,
     * lifetime best seller, low-stock count).
     */
    public function getIndexSummary(): array
    {
        $sales = $this->getSalesByDate(now()->subDays(29)->toDateString(), now()->toDateString());

        $top = $this->getTopProducts(now()->subDays(29)->toDateString(), now()->toDateString(), 1)->first();

        return [
            'revenue_30d' => (float) $sales->sum('revenue'),
            'orders_30d' => (int) $sales->sum('orders_count'),
            'items_30d' => (int) $sales->sum('items_sold'),
            'low_stock_count' => $this->getLowStockVariants()->count(),
            'best_seller_name' => $top?->product_name,
            'best_seller_qty' => (int) ($top->quantity ?? 0),
        ];
    }
}
