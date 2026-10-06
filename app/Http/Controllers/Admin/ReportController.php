<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Last 30 days by default for every date-window report, matching the
     * reports hub summary so all pages agree out of the box.
     */
    private const DEFAULT_DAYS = 30;

    public function __construct(private ReportService $reportService) {}

    // ─────────────────────────────────────────────
    // REPORTS HUB
    // ─────────────────────────────────────────────

    public function index()
    {
        return view('admin.reports.index', [
            'summary' => $this->reportService->getIndexSummary(),
        ]);
    }

    // ─────────────────────────────────────────────
    // SALES BY DATE
    // ─────────────────────────────────────────────

    public function salesByDate(Request $request)
    {
        [$from, $to] = $this->resolveDateWindow($request);

        $sales = $this->reportService->getSalesByDate($from, $to);

        return view('admin.reports.sales-by-date', [
            'from' => $from,
            'to' => $to,
            'sales' => $sales,
            'totals' => [
                'revenue' => (float) $sales->sum('revenue'),
                'discounts' => (float) $sales->sum('discounts'),
                'orders' => (int) $sales->sum('orders_count'),
                'items' => (int) $sales->sum('items_sold'),
                'avg_order_value' => $sales->sum('orders_count') > 0
                    ? round($sales->sum('revenue') / $sales->sum('orders_count'), 2)
                    : 0.0,
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // TOP PRODUCTS
    // ─────────────────────────────────────────────

    public function topProducts(Request $request)
    {
        [$from, $to] = $this->resolveDateWindow($request);

        return view('admin.reports.top-products', [
            'from' => $from,
            'to' => $to,
            'topProducts' => $this->reportService->getTopProducts($from, $to),
        ]);
    }

    // ─────────────────────────────────────────────
    // LOW STOCK
    // ─────────────────────────────────────────────

    public function lowStock()
    {
        return view('admin.reports.low-stock', [
            'lowStock' => $this->reportService->getLowStockVariants(),
        ]);
    }

    // ─────────────────────────────────────────────
    // CSV EXPORTS (same filters as the HTML pages)
    // ─────────────────────────────────────────────

    public function exportSalesByDate(Request $request)
    {
        [$from, $to] = $this->resolveDateWindow($request);
        $sales = $this->reportService->getSalesByDate($from, $to);

        $totals = [
            'revenue' => (float) $sales->sum('revenue'),
            'discounts' => (float) $sales->sum('discounts'),
            'orders' => (int) $sales->sum('orders_count'),
            'items' => (int) $sales->sum('items_sold'),
        ];

        return $this->csvResponse(
            "sales-by-date_{$from}_{$to}.csv",
            function ($file) use ($sales, $totals) {
                fputcsv($file, ['Date', 'Orders', 'Items Sold', 'Revenue', 'Discounts']);

                foreach ($sales as $day) {
                    fputcsv($file, [
                        $day['date'],
                        $day['orders_count'],
                        $day['items_sold'],
                        number_format($day['revenue'], 2, '.', ''),
                        number_format($day['discounts'], 2, '.', ''),
                    ]);
                }

                fputcsv($file, [
                    "Total ({$sales->count()} days)",
                    $totals['orders'],
                    $totals['items'],
                    number_format($totals['revenue'], 2, '.', ''),
                    number_format($totals['discounts'], 2, '.', ''),
                ]);
            }
        );
    }

    public function exportTopProducts(Request $request)
    {
        [$from, $to] = $this->resolveDateWindow($request);
        // HTML page shows the top 10; the export gets a much wider cut so
        // spreadsheets hold the full picture (same cap as dashboard widgets).
        $topProducts = $this->reportService->getTopProducts($from, $to, 500);

        return $this->csvResponse(
            "top-products_{$from}_{$to}.csv",
            function ($file) use ($topProducts) {
                fputcsv($file, ['Rank', 'Product', 'Variant', 'SKU', 'Units Sold', 'Revenue', 'Orders']);

                foreach ($topProducts as $index => $item) {
                    fputcsv($file, [
                        $index + 1,
                        $item->product_name.($item->product_missing ? ' (deleted)' : ''),
                        $item->variant_options,
                        $item->product_sku,
                        $item->quantity,
                        number_format($item->revenue, 2, '.', ''),
                        $item->orders_count,
                    ]);
                }
            }
        );
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    /**
     * Validates and normalizes the date filter. Falls back to the default
     * 30-day window when inputs are missing or malformed instead of 500ing.
     * Reversed ranges (from > to) are swapped so results stay sensible.
     *
     * @return array{0: string, 1: string} Y-m-d date strings
     */
    private function resolveDateWindow(Request $request): array
    {
        $from = $request->input('date_from');
        $to = $request->input('date_to');

        $from = $this->isValidDateString($from) ? $from : now()->subDays(self::DEFAULT_DAYS - 1)->toDateString();
        $to = $this->isValidDateString($to) ? $to : now()->toDateString();

        if (Carbon::parse($from)->gt(Carbon::parse($to))) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }

    private function isValidDateString(?string $value): bool
    {
        if (is_null($value) || $value === '') {
            return false;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value) !== false;
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Builds a streamed CSV download. Streaming keeps memory flat for long
     * date ranges; fputcsv handles quoting/escaping of commas and quotes.
     *
     * @param  callable(resource): void  $writer  Receives an open file handle
     */
    private function csvResponse(string $filename, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($writer) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens the file with correct encoding
            fwrite($file, "\xEF\xBB\xBF");

            $writer($file);

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
