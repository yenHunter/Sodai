<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class WeeklySalesSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Carbon  $periodStart
     * @param  Carbon  $periodEnd
     * @param  Collection<int, object>  $topProducts
     * @param  string  $periodLabel  Heading/subject descriptor — 'Weekly' or 'Monthly'.
     */
    public function __construct(
        public $periodStart,
        public $periodEnd,
        public float $revenue,
        public int $orders,
        public float $avgOrderValue,
        public int $itemsSold,
        public float $discounts,
        public $topProducts,
        public int $lowStockCount,
        public string $periodLabel = 'Weekly',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "{$this->periodLabel} Sales Summary — ".config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.weekly-sales-summary',
        );
    }
}
