<?php

namespace App\Listeners;

use App\Mail\Admin\QueueBusyAlertMail;
use Illuminate\Queue\Events\QueueBusy;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendQueueBusyAlert
{
    public function handle(QueueBusy $event): void
    {
        $recipient = setting('notification', 'admin_alert_email');

        if (empty($recipient)) {
            Log::error('Queue depth exceeded the threshold but no admin alert email is configured.', [
                'connection' => $event->connectionName,
                'queue' => $event->queue,
                'size' => $event->size,
            ]);

            return;
        }

        // The monitor runs every ten minutes while a queue is over the limit;
        // limit the email to one per queue per configured cooldown window.
        $cooldownMinutes = max(1, (int) setting('notification', 'queue_alert_cooldown_minutes', 60));
        $threshold = max(1, (int) setting('notification', 'queue_depth_threshold', 100));
        $cooldownKey = "queue.busy.alerted.{$event->connectionName}.{$event->queue}";

        if (! Cache::add($cooldownKey, true, now()->addMinutes($cooldownMinutes))) {
            return;
        }

        try {
            Mail::to($recipient)->send(new QueueBusyAlertMail(
                connectionName: $event->connectionName,
                queueName: $event->queue,
                size: $event->size,
                threshold: $threshold,
            ));
        } catch (\Throwable $e) {
            // Allow the next monitor run to retry the alert.
            Cache::forget($cooldownKey);

            Log::error('Failed to send queue-depth alert.', [
                'connection' => $event->connectionName,
                'queue' => $event->queue,
                'size' => $event->size,
                'exception' => $e->getMessage(),
            ]);

            return;
        }

        Log::warning('Queue depth exceeded the threshold; alert sent.', [
            'connection' => $event->connectionName,
            'queue' => $event->queue,
            'size' => $event->size,
            'recipient' => $recipient,
        ]);
    }
}
