<?php

namespace Tests\Feature;

use App\Mail\Admin\WeeklySalesSummaryMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class WeeklySalesSummaryTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    private function enableSummary(string $email = 'owner@sodai.com'): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => $email,
            'weekly_sales_summary_enabled' => '1',
        ]);
    }

    private function disableSummary(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'owner@sodai.com',
            'weekly_sales_summary_enabled' => '0',
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
        $order->created_at = now()->subDays(3); // inside last week
        $order->save();

        Queue::fake();

        $this->artisan('reports:send-weekly-summary')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, 1);
        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable instanceof WeeklySalesSummaryMail;
        });
    }

    public function test_command_is_a_no_op_when_disabled_in_settings(): void
    {
        $this->disableSummary();
        $this->deliveredOrder(total: 150);

        Queue::fake();

        $this->artisan('reports:send-weekly-summary')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_command_is_a_no_op_with_default_settings(): void
    {
        // Fresh install: toggle never saved, must default to disabled
        // (matching abandoned_cart_enabled's opt-in default).
        Queue::fake();

        $this->artisan('reports:send-weekly-summary')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_summary_counts_only_last_weeks_revenue_orders(): void
    {
        $this->enableSummary('owner@sodai.com');

        // Last week (previous Mon–Sun): the report uses Carbon week windows
        // that always include 7 days ago, so backdate against that anchor.
        $inWeek = now()->subDays(3);
        $order = $this->deliveredOrder(total: 120, quantity: 4);
        $order->created_at = $inWeek;
        $order->save();

        // Six weeks ago — far outside the window
        $old = $this->deliveredOrder(total: 900);
        $old->created_at = now()->subWeeks(6);
        $old->save();

        // Excluded status even when dated inside the window
        $cancelled = Order::factory()->withStatus('cancelled')->create(['total_amount' => 500]);
        $cancelled->created_at = $inWeek;
        $cancelled->save();

        Queue::fake();

        $this->artisan('reports:send-weekly-summary')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable instanceof WeeklySalesSummaryMail
                && $job->mailable->revenue == 120.0
                && $job->mailable->orders == 1
                && $job->mailable->itemsSold == 4;
        });
    }

    // ─────────────────────────────────────────────
    // EMAIL CONTENT
    // ─────────────────────────────────────────────

    public function test_summary_email_content_carries_week_metrics(): void
    {
        $this->enableSummary('owner@sodai.com');

        $order = $this->deliveredOrder(total: 200, quantity: 2);
        $order->created_at = now()->subDays(2);
        $order->save();

        $lowStock = Product::factory()->create();
        $lowStock->defaultVariant->update(['stock_quantity' => 0]);

        $mailable = new WeeklySalesSummaryMail(
            periodStart: now()->subWeek()->startOfWeek(),
            periodEnd: now()->subWeek()->endOfWeek(),
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
        );

        $rendered = $mailable->render();

        $this->assertStringContainsString('Weekly Sales Summary', $rendered);
        $this->assertStringContainsString('$200.00', $rendered);
        $this->assertStringContainsString('Summary Product', $rendered);
        // Low-stock nudge renders when count > 0
        $this->assertStringContainsString('low or out of stock', $rendered);
    }

    public function test_summary_email_renders_empty_week_without_top_products_section(): void
    {
        $this->enableSummary('owner@sodai.com');

        $mailable = new WeeklySalesSummaryMail(
            periodStart: now()->subWeek()->startOfWeek(),
            periodEnd: now()->subWeek()->endOfWeek(),
            revenue: 0.0,
            orders: 0,
            avgOrderValue: 0.0,
            itemsSold: 0,
            discounts: 0.0,
            topProducts: collect([]),
            lowStockCount: 0,
        );

        $rendered = $mailable->render();

        $this->assertStringContainsString('$0.00', $rendered);
        $this->assertStringContainsString('No product sales last week', $rendered);
        $this->assertStringContainsString('No low-stock items right now', $rendered);
    }

    public function test_summary_email_has_descriptive_subject(): void
    {
        $mailable = new WeeklySalesSummaryMail(
            periodStart: now()->subWeek()->startOfWeek(),
            periodEnd: now()->subWeek()->endOfWeek(),
            revenue: 0.0,
            orders: 0,
            avgOrderValue: 0.0,
            itemsSold: 0,
            discounts: 0.0,
            topProducts: collect([]),
            lowStockCount: 0,
        );

        $this->assertStringContainsString(
            'Weekly Sales Summary',
            $mailable->envelope()->subject
        );
    }
}
