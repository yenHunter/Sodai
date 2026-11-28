<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

/**
 * Hardens a public demo deployment.
 *
 * Setting QUEUE_CONNECTION=sync and MAIL_MAILER=log in the demo's .env is not
 * a guarantee: the file is copied from a real deployment, someone can point a
 * mailer at SMTP, and code can ask for a mailer by name. So when demo mode is
 * on, every configured mailer is replaced with the log transport and the
 * queue is forced to sync. Guests can still exercise registration, password
 * reset and guest checkout — the messages are written to the log instead of
 * being delivered.
 */
final class DemoEnvironment
{
    public static function enabled(): bool
    {
        return (bool) config('demo.enabled', false);
    }

    public static function enforce(): void
    {
        if (! self::enabled()) {
            return;
        }

        $configuredMailer = config('mail.default');
        $logChannel = config('mail.mailers.log.channel');

        $mailers = [];

        foreach (array_keys((array) config('mail.mailers', [])) as $name) {
            $mailers[$name] = array_filter([
                'transport' => 'log',
                'channel' => $logChannel,
            ], fn ($value) => $value !== null);
        }

        config([
            'mail.default' => 'log',
            'mail.mailers' => $mailers,
            'queue.default' => 'sync',
        ]);

        if ($configuredMailer !== 'log') {
            Log::warning('Demo mode forced the mail transport to log.', [
                'configured_mailer' => $configuredMailer,
            ]);
        }
    }
}
