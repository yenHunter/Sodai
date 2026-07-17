<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerSetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $setPasswordUrl,
        public string $customerName,
        public int    $expiresInMinutes = 60,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Set Your Password - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.customer-set-password',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}