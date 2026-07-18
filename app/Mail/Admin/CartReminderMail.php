<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CartReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $customerName,
        public array  $items,
        public float  $total,
        public string $cartUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You left something in your cart at ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'admin.emails.cart-reminder',
        );
    }
}