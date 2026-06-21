<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $resetUrl,
        public string $adminName,
        public int    $expiresInMinutes = 30,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Admin Password Reset Request - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.password-reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}