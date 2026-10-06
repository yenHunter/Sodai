<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class ReportModuleTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    // ─────────────────────────────────────────────
    // ACCESS CONTROL
    // ─────────────────────────────────────────────

    public function test_admin_with_report_view_can_access_all_report_pages(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertViewIs('admin.reports.index')
            ->assertViewHas('summary');

        $this->actingAsAdmin($admin)
            ->get(route('admin.reports.sales-by-date'))
            ->assertOk()
            ->assertViewIs('admin.reports.sales-by-date')
            ->assertViewHas(['sales', 'totals', 'from', 'to']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.reports.top-products'))
            ->assertOk()
            ->assertViewIs('admin.reports.top-products')
            ->assertViewHas('topProducts');

        $this->actingAsAdmin($admin)
            ->get(route('admin.reports.low-stock'))
            ->assertOk()
            ->assertViewIs('admin.reports.low-stock')
            ->assertViewHas('lowStock');
    }

    public function test_admin_without_report_view_permission_is_forbidden_on_all_report_pages(): void
    {
        // order.view alone must NOT grant report access
        $admin = $this->createAdminWithPermissions(['order.view']);

        foreach (['index', 'sales-by-date', 'top-products', 'low-stock'] as $page) {
            $this->actingAsAdmin($admin)
                ->get(route("admin.reports.{$page}"))
                ->assertForbidden();
        }
    }

    public function test_guest_is_redirected_to_admin_login_from_report_pages(): void
    {
        $this->get(route('admin.reports.index'))
            ->assertRedirect(route('admin.login.view'));
    }

    // ─────────────────────────────────────────────
    // SALES BY DATE — DATA CORRECTNESS
    // ─────────────────────────────────────────────

    public function test_sales_by_date_counts_only_revenue_statuses(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        // Revenue-sharing orders
        Order::factory()->withStatus('processing')->create(['total_amount' => 100]);
        Order::factory()->withStatus('shipped')->create(['total_amount' => 250]);
        Order::factory()->withStatus('delivered')->create(['total_amount' => 50]);

        // Excluded statuses
        Order::factory()->withStatus('pending')->create(['total_amount' => 999]);
        Order::factory()->withStatus('confirmed')->create(['total_amount' => 999]);
        Order::factory()->withStatus('cancelled')->create(['total_amount' => 999]);
        Order::factory()->withStatus('refunded')->create(['total_amount' => 999]);

        $response = $this->actingAsAdmin($admin)->get(route('admin.reports.sales-by-date'));

        $response->assertOk();
        $sales = $response->viewData('sales');

        $today = $sales->last(fn ($row) => $row['date'] === now()->toDateString());

        $this->assertNotNull($today);
        $this->assertSame(3, $today['orders_count']);
        $this->assertSame(400.0, $today['revenue']);
    }

    public function test_sales_by_date_aggregates_per_day_and_fills_empty_days(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $older = now()->subDays(3);
        // created_at passed explicitly in create(): Eloquent keeps a
        // dirty created_at on insert instead of stamping now()
        Order::factory()->withStatus('delivered')->create([
            'total_amount' => 120,
            'created_at' => $older,
        ]);
        Order::factory()->withStatus('delivered')->create([
            'total_amount' => 80,
            'created_at' => $older,
        ]);

        Order::factory()->withStatus('delivered')->create(['total_amount' => 40]); // today

        $from = now()->subDays(3)->toDateString();
        $to = now()->toDateString();

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.sales-by-date', ['date_from' => $from, 'date_to' => $to]));

        $sales = $response->viewData('sales');

        // 4 continuous rows (3 days ago … today), gaps zero-filled
        $this->assertCount(4, $sales);
        $this->assertSame($from, $sales->first()['date']);
        $this->assertSame($to, $sales->last()['date']);
        $this->assertSame(200.0, $sales[0]['revenue']);
        $this->assertSame(0, $sales[1]['orders_count']);
        $this->assertSame(0.0, $sales[1]['revenue']);
        $this->assertSame(0, $sales[2]['orders_count']);
        $this->assertSame(40.0, $sales[3]['revenue']);
    }

    public function test_sales_by_date_totals_and_avg_order_value(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $older = now()->subDays(3);
        Order::factory()->withStatus('delivered')->create([
            'total_amount' => 200,
            'discount_amount' => 20,
            'created_at' => $older,
        ]);
        Order::factory()->withStatus('delivered')->create([
            'total_amount' => 100,
            'discount_amount' => 0,
            'created_at' => $older,
        ]);
        // Excluded from sales — must not skew totals
        Order::factory()->withStatus('cancelled')->create(['total_amount' => 500, 'discount_amount' => 50]);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.sales-by-date', [
                'date_from' => now()->subDays(3)->toDateString(),
                'date_to' => now()->toDateString(),
            ]));

        $totals = $response->viewData('totals');

        $this->assertSame(300.0, $totals['revenue']);
        $this->assertSame(20.0, $totals['discounts']);
        $this->assertSame(2, $totals['orders']);
        $this->assertSame(150.0, $totals['avg_order_value']);
    }

    public function test_sales_by_date_respects_date_window_filter(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        Order::factory()->withStatus('delivered')->create([
            'total_amount' => 90,
            'created_at' => now()->subDays(60),
        ]);

        Order::factory()->withStatus('delivered')->create(['total_amount' => 60]); // today

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.sales-by-date', [
                'date_from' => now()->subDays(7)->toDateString(),
                'date_to' => now()->toDateString(),
            ]));

        $totals = $response->viewData('totals');
        $this->assertSame(60.0, $totals['revenue']);
        $this->assertSame(1, $totals['orders']);
    }

    public function test_sales_by_date_defaults_to_30_day_window(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        Order::factory()->withStatus('delivered')->create([
            'total_amount' => 90,
            'created_at' => now()->subDays(90),
        ]);

        $response = $this->actingAsAdmin($admin)->get(route('admin.reports.sales-by-date'));

        $this->assertSame(now()->subDays(29)->toDateString(), $response->viewData('from'));
        $this->assertSame(now()->toDateString(), $response->viewData('to'));

        $sales = $response->viewData('sales');
        $this->assertCount(30, $sales);
    }

    public function test_sales_by_date_swaps_reversed_date_range(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.sales-by-date', [
                'date_from' => '2026-10-05',
                'date_to' => '2026-09-28',
            ]));

        $this->assertSame('2026-09-28', $response->viewData('from'));
        $this->assertSame('2026-10-05', $response->viewData('to'));
    }

    // ─────────────────────────────────────────────
    // TOP PRODUCTS — DATA CORRECTNESS
    // ─────────────────────────────────────────────

    public function test_top_products_ranks_by_units_sold_within_revenue_statuses(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $best = Product::factory()->create(['name' => 'Best Seller']);
        $second = Product::factory()->create(['name' => 'Runner Up']);
        $excluded = Product::factory()->create(['name' => 'Cancelled Only']);

        $bestOrder = Order::factory()->withStatus('delivered')->create();
        $secondOrder = Order::factory()->withStatus('processing')->create();
        $cancelledOrder = Order::factory()->withStatus('cancelled')->create();

        OrderItem::factory()->create([
            'order_id' => $bestOrder->id, 'product_id' => $best->id,
            'product_name' => 'Best Seller', 'quantity' => 5, 'unit_price' => 20, 'total_price' => 100,
        ]);
        OrderItem::factory()->create([
            'order_id' => $secondOrder->id, 'product_id' => $second->id,
            'product_name' => 'Runner Up', 'quantity' => 3, 'unit_price' => 10, 'total_price' => 30,
        ]);
        // Would beat "Best Seller" by units, but sits on a cancelled order → excluded
        OrderItem::factory()->create([
            'order_id' => $cancelledOrder->id, 'product_id' => $excluded->id,
            'product_name' => 'Cancelled Only', 'quantity' => 50, 'unit_price' => 10, 'total_price' => 500,
        ]);

        $response = $this->actingAsAdmin($admin)->get(route('admin.reports.top-products'));

        $top = $response->viewData('topProducts');

        $this->assertCount(2, $top);
        $this->assertSame('Best Seller', $top[0]->product_name);
        $this->assertSame(5, $top[0]->quantity);
        $this->assertSame(100.0, $top[0]->revenue);
        $this->assertSame(1, $top[0]->orders_count);
        $this->assertSame('Runner Up', $top[1]->product_name);
        $this->assertSame(3, $top[1]->quantity);
    }

    public function test_top_products_merges_rows_across_orders_and_variants(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $product = Product::factory()->create(['name' => 'Widget']);
        $orderA = Order::factory()->withStatus('delivered')->create();
        $orderB = Order::factory()->withStatus('shipped')->create();

        // Same product across orders and variants → one merged row
        OrderItem::factory()->create([
            'order_id' => $orderA->id, 'product_id' => $product->id,
            'product_name' => 'Widget', 'quantity' => 2, 'unit_price' => 10, 'total_price' => 20,
        ]);
        OrderItem::factory()->create([
            'order_id' => $orderB->id, 'product_id' => $product->id,
            'product_name' => 'Widget', 'quantity' => 4, 'unit_price' => 10, 'total_price' => 40,
        ]);

        $top = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.top-products'))
            ->viewData('topProducts');

        $this->assertCount(1, $top);
        $this->assertSame(6, $top[0]->quantity);
        $this->assertSame(60.0, $top[0]->revenue);
        $this->assertSame(2, $top[0]->orders_count);
    }

    public function test_top_products_respects_date_window(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $product = Product::factory()->create(['name' => 'Old Product']);
        $oldOrder = Order::factory()->withStatus('delivered')->create([
            'created_at' => now()->subDays(45),
        ]);
        OrderItem::factory()->create([
            'order_id' => $oldOrder->id, 'product_id' => $product->id,
            'product_name' => 'Old Product', 'quantity' => 9, 'unit_price' => 10, 'total_price' => 90,
        ]);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.top-products', [
                'date_from' => now()->subDays(7)->toDateString(),
                'date_to' => now()->toDateString(),
            ]));

        $this->assertCount(0, $response->viewData('topProducts'));
    }

    public function test_top_products_respects_limit(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);
        $order = Order::factory()->withStatus('delivered')->create();

        foreach (range(1, 15) as $i) {
            $product = Product::factory()->create(['name' => "Product {$i}"]);
            OrderItem::factory()->create([
                'order_id' => $order->id, 'product_id' => $product->id,
                'product_name' => "Product {$i}", 'quantity' => $i, 'unit_price' => 10, 'total_price' => $i * 10,
            ]);
        }

        $top = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.top-products'))
            ->viewData('topProducts');

        $this->assertCount(10, $top);
        $this->assertSame('Product 15', $top[0]->product_name);
        $this->assertSame('Product 6', $top[9]->product_name);
    }

    public function test_top_products_flags_deleted_products(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $product = Product::factory()->create(['name' => 'Discontinued']);
        $order = Order::factory()->withStatus('delivered')->create();
        OrderItem::factory()->create([
            'order_id' => $order->id, 'product_id' => $product->id,
            'product_name' => 'Discontinued', 'quantity' => 2, 'unit_price' => 10, 'total_price' => 20,
        ]);

        $product->delete(); // soft delete after the snapshot exists

        $top = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.top-products'))
            ->viewData('topProducts');

        $this->assertCount(1, $top);
        $this->assertTrue($top[0]->product_missing);
        $this->assertSame('Discontinued', $top[0]->product_name);
    }

    // ─────────────────────────────────────────────
    // LOW STOCK — DATA CORRECTNESS
    // ─────────────────────────────────────────────

    public function test_low_stock_lists_out_of_stock_and_below_threshold_variants(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $outOfStock = Product::factory()->create(['name' => 'Out Product']);
        $outVariant = $outOfStock->defaultVariant;
        $outVariant->update(['stock_quantity' => 0]);

        $low = Product::factory()->create(['name' => 'Low Product']);
        $lowVariant = $low->defaultVariant;
        $lowVariant->update(['stock_quantity' => 2, 'low_stock_threshold' => 5]);

        $healthy = Product::factory()->create(['name' => 'Healthy Product']);
        $healthy->defaultVariant->update(['stock_quantity' => 100, 'low_stock_threshold' => 5]);

        $response = $this->actingAsAdmin($admin)->get(route('admin.reports.low-stock'));

        $lowStock = $response->viewData('lowStock');

        $this->assertCount(2, $lowStock);
        $rowsByStock = $lowStock->keyBy(fn ($r) => $r['variant']->id);

        $this->assertTrue($rowsByStock[$outVariant->id]['is_out_of_stock']);
        $this->assertSame(0, $rowsByStock[$outVariant->id]['stock_quantity']);
        $this->assertFalse($rowsByStock[$lowVariant->id]['is_out_of_stock']);
        $this->assertSame(2, $rowsByStock[$lowVariant->id]['stock_quantity']);
        $this->assertSame(5, $rowsByStock[$lowVariant->id]['threshold']);
    }

    public function test_low_stock_excludes_inactive_variants_and_inactive_products(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $inactiveVariantProduct = Product::factory()->create();
        $inactiveVariantProduct->defaultVariant->update(['stock_quantity' => 0, 'is_active' => false]);

        $inactiveProduct = Product::factory()->create(['is_active' => false]);
        $inactiveProduct->defaultVariant->update(['stock_quantity' => 0]);

        $active = Product::factory()->create();
        $active->defaultVariant->update(['stock_quantity' => 1]);

        $lowStock = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.low-stock'))
            ->viewData('lowStock');

        $this->assertCount(1, $lowStock);
        $this->assertSame($active->id, $lowStock[0]['product']->id);
    }

    public function test_low_stock_sorts_most_critical_first(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $mid = Product::factory()->create();
        $mid->defaultVariant->update(['stock_quantity' => 3, 'low_stock_threshold' => 5]);

        $out = Product::factory()->create();
        $out->defaultVariant->update(['stock_quantity' => 0]);

        $one = Product::factory()->create();
        $one->defaultVariant->update(['stock_quantity' => 1, 'low_stock_threshold' => 5]);

        $lowStock = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.low-stock'))
            ->viewData('lowStock');

        $stocks = $lowStock->pluck('stock_quantity')->values()->all();
        $this->assertSame([0, 1, 3], $stocks);
    }

    // ─────────────────────────────────────────────
    // HUB SUMMARY
    // ─────────────────────────────────────────────

    public function test_index_summary_aggregates_30_day_numbers(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $product = Product::factory()->create(['name' => 'Star Product']);
        $order = Order::factory()->withStatus('delivered')->create(['total_amount' => 150]);
        OrderItem::factory()->create([
            'order_id' => $order->id, 'product_id' => $product->id,
            'product_name' => 'Star Product', 'quantity' => 7, 'unit_price' => 10, 'total_price' => 70,
        ]);

        $low = Product::factory()->create();
        $low->defaultVariant->update(['stock_quantity' => 0]);

        $summary = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.index'))
            ->viewData('summary');

        $this->assertSame(150.0, $summary['revenue_30d']);
        $this->assertSame(1, $summary['orders_30d']);
        $this->assertSame(7, $summary['items_30d']);
        $this->assertSame(1, $summary['low_stock_count']);
        $this->assertSame('Star Product', $summary['best_seller_name']);
        $this->assertSame(7, $summary['best_seller_qty']);
    }

    // ─────────────────────────────────────────────
    // CSV EXPORTS
    // ─────────────────────────────────────────────

    public function test_sales_by_date_export_streams_csv_honoring_date_filter(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        Order::factory()->withStatus('delivered')->create([
            'total_amount' => 90,
            'created_at' => now()->subDays(60),
        ]);
        Order::factory()->withStatus('delivered')->create(['total_amount' => 60]); // today

        $from = now()->subDays(7)->toDateString();
        $to = now()->toDateString();

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.sales-by-date.export', ['date_from' => $from, 'date_to' => $to]));

        $response->assertOk();
        $response->assertHeaderContains('Content-Type', 'text/csv');
        $response->assertHeaderContains('Content-Disposition', "sales-by-date_{$from}_{$to}.csv");

        $csv = $response->streamedContent();

        // Header + today's data row + totals row
        // (fputcsv quotes fields containing spaces)
        $this->assertStringContainsString('Date,Orders,"Items Sold",Revenue,Discounts', $csv);
        $this->assertStringContainsString($to.',1,0,60.00,0.00', $csv);
        $this->assertStringContainsString('"Total (8 days)",1,0,60.00,0.00', $csv);

        // The 60-day-old order revenue must not leak into a 7-day export
        $this->assertStringNotContainsString('90.00', $csv);
    }

    public function test_top_products_export_streams_csv_honoring_date_filter(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $product = Product::factory()->create(['name' => 'Widget']);

        $currentOrder = Order::factory()->withStatus('delivered')->create();
        OrderItem::factory()->create([
            'order_id' => $currentOrder->id, 'product_id' => $product->id,
            'product_name' => 'Widget', 'product_sku' => 'WDG-1',
            'quantity' => 5, 'unit_price' => 20, 'total_price' => 100,
        ]);

        $oldOrder = Order::factory()->withStatus('delivered')->create([
            'created_at' => now()->subDays(45),
        ]);
        OrderItem::factory()->create([
            'order_id' => $oldOrder->id, 'product_id' => $product->id,
            'product_name' => 'Widget', 'product_sku' => 'WDG-1',
            'quantity' => 9, 'unit_price' => 10, 'total_price' => 90,
        ]);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.top-products.export', [
                'date_from' => now()->subDays(7)->toDateString(),
                'date_to' => now()->toDateString(),
            ]));

        $response->assertOk();
        $response->assertHeaderContains('Content-Type', 'text/csv');

        $csv = $response->streamedContent();

        $this->assertStringContainsString('Rank,Product,Variant,SKU,"Units Sold",Revenue,Orders', $csv);
        $this->assertStringContainsString('1,Widget,,WDG-1,5,100.00,1', $csv);

        // Old-window revenue excluded; rank counts both rows if filter broke
        $this->assertStringNotContainsString('90.00', $csv);
        $this->assertStringNotContainsString('2,Widget', $csv);
    }

    public function test_top_products_export_includes_more_rows_than_html_limit(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);
        $order = Order::factory()->withStatus('delivered')->create();

        foreach (range(1, 12) as $i) {
            $product = Product::factory()->create(['name' => "Product {$i}"]);
            OrderItem::factory()->create([
                'order_id' => $order->id, 'product_id' => $product->id,
                'product_name' => "Product {$i}", 'quantity' => $i,
                'unit_price' => 10, 'total_price' => $i * 10,
            ]);
        }

        $csv = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.top-products.export'))
            ->streamedContent();

        // 12 data rows + header (HTML page caps at 10 — the export must not)
        $dataRows = substr_count($csv, "\n") - 1; // minus header
        $this->assertSame(12, $dataRows);
        $this->assertStringContainsString('1,"Product 12"', $csv);
    }

    public function test_csv_exports_require_report_view_permission(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.reports.sales-by-date.export'))
            ->assertForbidden();

        $this->actingAsAdmin($admin)
            ->get(route('admin.reports.top-products.export'))
            ->assertForbidden();
    }

    public function test_export_dates_fall_back_to_defaults_when_invalid(): void
    {
        $admin = $this->createAdminWithPermissions(['report.view']);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.reports.sales-by-date.export', ['date_from' => 'not-a-date', 'date_to' => '']));

        $response->assertOk();
        $this->assertStringContainsString(
            'sales-by-date_'.now()->subDays(29)->toDateString().'_'.now()->toDateString().'.csv',
            $response->headers->get('Content-Disposition')
        );
    }
}
