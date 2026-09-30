<?php

namespace App\Console\Commands;

use App\Services\Admin\CartService;
use Illuminate\Console\Command;

class SendCartRemindersCommand extends Command
{
    protected $signature = 'carts:send-reminders
        {--hours= : Hours of inactivity before a cart is considered abandoned (defaults to the configured setting)}';

    protected $description = 'Queue cart reminder emails for abandoned carts of registered customers';

    public function handle(CartService $cartService): int
    {
        if (setting('notification', 'abandoned_cart_enabled', '0') !== '1') {
            $this->components->info('Abandoned-cart reminders are disabled in settings.');

            return self::SUCCESS;
        }

        $hours = (int) ($this->option('hours') ?: setting('notification', 'abandoned_cart_hours', 24));
        $hours = max(1, $hours);

        $carts = $cartService->getAbandonedCarts($hours);

        if ($carts->isEmpty()) {
            $this->components->info("No abandoned carts found (inactivity threshold: {$hours}h).");

            return self::SUCCESS;
        }

        $queued = 0;
        foreach ($carts as $cart) {
            if ($cartService->queueReminder($cart)) {
                $queued++;
            }
        }

        $skipped = $carts->count() - $queued;
        $this->components->info("Queued {$queued} cart reminder(s) for delivery".($skipped > 0 ? ", skipped {$skipped}." : '.'));

        return self::SUCCESS;
    }
}
