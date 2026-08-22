<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomerVerifyEmailNotification extends BaseVerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address - '.config('app.name'))
            ->view('visitor.emails.verify-email', [
                'customerName' => $notifiable->name,
                'verificationUrl' => $verificationUrl,
                'expiresInMinutes' => (int) config('auth.verification.expire', 60),
            ]);
    }
}
