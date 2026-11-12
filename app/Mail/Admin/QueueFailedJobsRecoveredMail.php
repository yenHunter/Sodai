<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QueueFailedJobsRecoveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public int $previousCount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Queue recovered: failed-jobs backlog is clear - '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.queue-failed-jobs-recovered',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
