<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QueueFailedJobsAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int $currentCount,
        public int $previousCount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Queue alert: {$this->currentCount} failed job(s) - ".config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.queue-failed-jobs-alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
