<?php

namespace App\Console\Commands;

use App\Mail\Admin\WeeklySalesSummaryMail;
use App\Services\Admin\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMonthlySalesSummaryCommand extends Command
{
    protected $signature = 'reports:send-monthly-summary';

    protected $description = "Queue last month's sales summary email to the store notification email";

    public function handle(ReportService $reportService): int
    {
        if (setting('notification', 'monthly_sales_summary_enabled', '0') !== '1') {
            $this->components->info('Monthly sales summary is disabled in settings.');

            return self::SUCCESS;
        }

        $recipient = setting('notification', 'admin_alert_email');
        $summary = $reportService->getMonthlySummary();

        Mail::to($recipient)->queue(new WeeklySalesSummaryMail(
            periodStart: $summary['period_start'],
            periodEnd: $summary['period_end'],
            revenue: $summary['revenue'],
            orders: $summary['orders'],
            avgOrderValue: $summary['avg_order_value'],
            itemsSold: $summary['items_sold'],
            discounts: $summary['discounts'],
            topProducts: $summary['top_products'],
            lowStockCount: $summary['low_stock_count'],
            periodLabel: 'Monthly',
        ));

        Log::info('Monthly sales summary email queued.', [
            'recipient' => $recipient,
            'period_start' => $summary['period_start']->toDateString(),
            'period_end' => $summary['period_end']->toDateString(),
            'revenue' => $summary['revenue'],
            'orders' => $summary['orders'],
        ]);

        $this->components->info(
            "Monthly summary for {$summary['period_start']->toDateString()} → {$summary['period_end']->toDateString()} "
            ."queued to {$recipient} ({$summary['orders']} orders, {$summary['revenue']} revenue)."
        );

        return self::SUCCESS;
    }
}
