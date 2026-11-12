<?php

namespace App\Console\Commands;

use App\Mail\Admin\QueueFailedJobsAlertMail;
use App\Mail\Admin\QueueFailedJobsRecoveredMail;
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

    private const INCIDENT_KEY = 'queue.failed_jobs.incident_active';

    public function handle(): int
    {
        $current = (int) DB::table('failed_jobs')->count();
        $previous = (int) Cache::get(self::CACHE_KEY, 0);

        if ($current === 0) {
            // The incident is over; the next failure starts a new alert cycle.
            $wasIncident = (bool) Cache::get(self::INCIDENT_KEY, false);
            Cache::forget(self::INCIDENT_KEY);
            Cache::forever(self::CACHE_KEY, 0);

            if ($wasIncident) {
                $this->sendRecoveryNotice($previous);
            } else {
                $this->components->info('Failed-job backlog is clear.');
            }

            return self::SUCCESS;
        }

        if ($current <= $previous) {
            Cache::forever(self::CACHE_KEY, $current);
            $this->components->info("Failed-job backlog did not grow ({$current}, previous {$previous}).");

            return self::SUCCESS;
        }

        if (Cache::get(self::INCIDENT_KEY, false)) {
            // A burst can add several failures before the backlog clears; keep
            // the baseline current but send only one notification per incident.
            Cache::forever(self::CACHE_KEY, $current);
            $this->components->info(
                "Failed-job backlog grew to {$current}, but this incident was already alerted."
            );

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
        Cache::forever(self::INCIDENT_KEY, true);

        Log::warning('Failed-job backlog grew; incident alert sent.', [
            'current' => $current,
            'previous' => $previous,
            'recipient' => $recipient,
        ]);

        $this->components->warn(
            "Failed jobs grew from {$previous} to {$current}; alerted {$recipient}."
        );

        return self::SUCCESS;
    }

    private function sendRecoveryNotice(int $previous): void
    {
        $recipient = setting('notification', 'admin_alert_email');

        if (empty($recipient)) {
            Log::error('Failed-job backlog recovered but no admin alert email is configured.', [
                'previous' => $previous,
            ]);

            return;
        }

        try {
            Mail::to($recipient)->send(new QueueFailedJobsRecoveredMail(previousCount: $previous));
        } catch (\Throwable $e) {
            Log::error('Failed to send failed-job recovery notice.', [
                'previous' => $previous,
                'exception' => $e->getMessage(),
            ]);

            return;
        }

        Log::info('Failed-job backlog recovered; notice sent.', [
            'previous' => $previous,
            'recipient' => $recipient,
        ]);

        $this->components->info("Failed-job backlog cleared from {$previous}; recovery notice sent to {$recipient}.");
    }
}
