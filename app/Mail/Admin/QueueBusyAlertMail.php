<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QueueBusyAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $connectionName,
        public string $queueName,
        public int $size,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Queue alert: {$this->queueName} has {$this->size} jobs - ".config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.queue-busy-alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
