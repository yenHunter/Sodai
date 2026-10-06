<?php

namespace Tests\Feature;

use App\Mail\Admin\WeeklySalesSummaryMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class MonthlySalesSummaryTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function enableSummary(string $email = 'owner@sodai.com'): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => $email,
            'monthly_sales_summary_enabled' => '1',
        ]);
    }

    private function disableSummary(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'owner@sodai.com',
            'monthly_sales_summary_enabled' => '0',
        ]);
    }

    private function deliveredOrder(float $total, int $quantity = 1): Order
    {
        $order = Order::factory()->withStatus('delivered')->create(['total_amount' => $total]);
        $product = Product::factory()->create(['name' => 'Summary Product']);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Summary Product',
            'quantity' => $quantity,
            'unit_price' => 10,
            'total_price' => $quantity * 10,
        ]);

        return $order;
    }

    // ─────────────────────────────────────────────
    // SCHEDULED SEND
    // ─────────────────────────────────────────────

    public function test_command_queues_summary_to_admin_alert_email(): void
    {
        $this->enableSummary('owner@sodai.com');

        $order = $this->deliveredOrder(total: 150, quantity: 5);
        $order->created_at = now()->startOfMonth()->subMonth()->addDays(3); // inside last month
        $order->save();

        Queue::fake();

        $this->artisan('reports:send-monthly-summary')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, 1);
        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable instanceof WeeklySalesSummaryMail
                && $job->mailable->periodLabel === 'Monthly';
        });
    }

    public function test_command_is_a_no_op_when_disabled_in_settings(): void
    {
        $this->disableSummary();
        $this->deliveredOrder(total: 150);

        Queue::fake();

        $this->artisan('reports:send-monthly-summary')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_command_is_a_no_op_with_default_settings(): void
    {
        // Fresh install: toggle never saved, must default to disabled
        // (matching weekly_sales_summary_enabled's opt-in default).
        Queue::fake();

        $this->artisan('reports:send-monthly-summary')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_summary_counts_only_last_months_revenue_orders(): void
    {
        $this->enableSummary('owner@sodai.com');

        // Last month: anchor back from the current month's 1st so the date is
        // always inside the calendar-month window regardless of today.
        $inMonth = now()->startOfMonth()->subMonth()->addDays(5);
        $order = $this->deliveredOrder(total: 120, quantity: 4);
        $order->created_at = $inMonth;
        $order->save();

        // Two months ago — outside the window
        $old = $this->deliveredOrder(total: 900);
        $old->created_at = now()->startOfMonth()->subMonths(2);
        $old->save();

        // Excluded status even when dated inside the window
        $cancelled = Order::factory()->withStatus('cancelled')->create(['total_amount' => 500]);
        $cancelled->created_at = $inMonth;
        $cancelled->save();

        Queue::fake();

        $this->artisan('reports:send-monthly-summary')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable instanceof WeeklySalesSummaryMail
                && $job->mailable->revenue == 120.0
                && $job->mailable->orders == 1
                && $job->mailable->itemsSold == 4;
        });
    }

    public function test_window_covers_month_boundaries_exactly(): void
    {
        $this->enableSummary();

        // First second of the previous month — included
        $first = $this->deliveredOrder(total: 60);
        $first->created_at = now()->startOfMonth()->subMonth()->startOfDay();
        $first->save();

        // Last second of the previous month — included
        $last = $this->deliveredOrder(total: 40);
        $last->created_at = now()->startOfMonth()->subMonth()->endOfMonth()->endOfDay();
        $last->save();

        // First day of the current month — belongs to the next period
        $current = $this->deliveredOrder(total: 500);
        $current->created_at = now()->startOfMonth()->startOfDay();
        $current->save();

        Queue::fake();

        $this->artisan('reports:send-monthly-summary')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable->revenue == 100.0
                && $job->mailable->orders == 2
                && $job->mailable->periodStart->toDateString() === now()->startOfMonth()->subMonth()->toDateString()
                && $job->mailable->periodEnd->toDateString() === now()->startOfMonth()->subMonth()->endOfMonth()->toDateString();
        });
    }

    public function test_month_window_is_overflow_proof_on_short_months(): void
    {
        // One fake for the whole test: re-faking mid-test would strand pushes
        // on the first fake, because the resolved mailer keeps that instance.
        Queue::fake();

        // Running on the 31st must still produce the previous calendar month,
        // never spilling into March via the Feb-31 overflow trap.
        Carbon::setTestNow('2026-03-31 15:00:00');
        $this->enableSummary();

        $order = $this->deliveredOrder(total: 80);
        $order->created_at = Carbon::parse('2026-02-14 12:00:00');
        $order->save();

        $this->artisan('reports:send-monthly-summary')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, 1);
        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable->revenue == 80.0
                && $job->mailable->periodStart->toDateString() === '2026-02-01'
                && $job->mailable->periodEnd->toDateString() === '2026-02-28';
        });

        // On a leap year, February 29 belongs to the window too.
        Carbon::setTestNow('2028-03-31 15:00:00');

        $leap = $this->deliveredOrder(total: 20);
        $leap->created_at = Carbon::parse('2028-02-29 23:00:00');
        $leap->save();

        $this->artisan('reports:send-monthly-summary')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, 2);
        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable->revenue == 20.0
                && $job->mailable->periodStart->toDateString() === '2028-02-01'
                && $job->mailable->periodEnd->toDateString() === '2028-02-29';
        });
    }

    // ─────────────────────────────────────────────
    // EMAIL CONTENT
    // ─────────────────────────────────────────────

    public function test_summary_email_content_carries_month_metrics(): void
    {
        $lowStock = Product::factory()->create();
        $lowStock->defaultVariant->update(['stock_quantity' => 0]);

        $mailable = new WeeklySalesSummaryMail(
            periodStart: now()->startOfMonth()->subMonth(),
            periodEnd: now()->startOfMonth()->subMonth()->endOfMonth(),
            revenue: 200.0,
            orders: 1,
            avgOrderValue: 200.0,
            itemsSold: 2,
            discounts: 0.0,
            topProducts: collect([(object) [
                'product_name' => 'Summary Product',
                'quantity' => 2,
                'revenue' => 20.0,
                'product_missing' => false,
            ]]),
            lowStockCount: 1,
            periodLabel: 'Monthly',
        );

        $rendered = $mailable->render();

        $this->assertStringContainsString('Monthly Sales Summary', $rendered);
        $this->assertStringContainsString('last month', $rendered);
        $this->assertStringContainsString('$200.00', $rendered);
        $this->assertStringContainsString('Summary Product', $rendered);
        // Low-stock nudge renders when count > 0
        $this->assertStringContainsString('low or out of stock', $rendered);
    }

    public function test_summary_email_renders_empty_month_without_top_products_section(): void
    {
        $mailable = new WeeklySalesSummaryMail(
            periodStart: now()->startOfMonth()->subMonth(),
            periodEnd: now()->startOfMonth()->subMonth()->endOfMonth(),
            revenue: 0.0,
            orders: 0,
            avgOrderValue: 0.0,
            itemsSold: 0,
            discounts: 0.0,
            topProducts: collect([]),
            lowStockCount: 0,
            periodLabel: 'Monthly',
        );

        $rendered = $mailable->render();

        $this->assertStringContainsString('$0.00', $rendered);
        $this->assertStringContainsString('No product sales last month', $rendered);
        $this->assertStringContainsString('No low-stock items right now', $rendered);
    }

    public function test_summary_email_has_descriptive_subject(): void
    {
        $mailable = new WeeklySalesSummaryMail(
            periodStart: now()->startOfMonth()->subMonth(),
            periodEnd: now()->startOfMonth()->subMonth()->endOfMonth(),
            revenue: 0.0,
            orders: 0,
            avgOrderValue: 0.0,
            itemsSold: 0,
            discounts: 0.0,
            topProducts: collect([]),
            lowStockCount: 0,
            periodLabel: 'Monthly',
        );

        $this->assertStringContainsString(
            'Monthly Sales Summary',
            $mailable->envelope()->subject
        );
    }
}
