<?php

namespace App\Console\Commands;

use App\Mail\Admin\QueueFailedJobsAlertMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertFailedQueueJobsCommand extends Command
{
    protected $signature = 'queue:alert-failed-jobs';

    protected $description = 'Alert the store notification email when the failed-jobs backlog grows';

    private const CACHE_KEY = 'queue.failed_jobs.last_count';

    public function handle(): int
    {
        $current = (int) DB::table('failed_jobs')->count();
        $previous = (int) Cache::get(self::CACHE_KEY, 0);

        if ($current <= $previous) {
            Cache::forever(self::CACHE_KEY, $current);
            $this->components->info("Failed-job backlog did not grow ({$current}, previous {$previous}).");

            return self::SUCCESS;
        }

        $recipient = setting('notification', 'admin_alert_email');

        if (empty($recipient)) {
            Log::error('Failed-job backlog grew but no admin alert email is configured.', [
                'current' => $current,
                'previous' => $previous,
            ]);

            return self::FAILURE;
        }

        try {
            Mail::to($recipient)->send(new QueueFailedJobsAlertMail(
                currentCount: $current,
                previousCount: $previous,
            ));
        } catch (\Throwable $e) {
            // Leave the cached baseline unchanged so the next run retries the alert.
            Log::error('Failed to send failed-job backlog alert.', [
                'current' => $current,
                'previous' => $previous,
                'exception' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }

        Cache::forever(self::CACHE_KEY, $current);

        Log::warning('Failed-job backlog grew; alert sent.', [
            'current' => $current,
            'previous' => $previous,
            'recipient' => $recipient,
        ]);

        $this->components->warn(
            "Failed jobs grew from {$previous} to {$current}; alerted {$recipient}."
        );

        return self::SUCCESS;
    }
}
