<?php

namespace App\Console\Commands;

use App\Mail\Admin\QueueFailedJobsAlertMail;
use App\Mail\Admin\QueueFailedJobsRecoveredMail;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AlertFailedQueueJobsCommand extends Command
{
    protected $signature = 'queue:alert-failed-jobs';

    protected $description = 'Alert the store notification email when the failed-jobs backlog grows';

    private const CACHE_KEY = 'queue.failed_jobs.last_count';

    private const INCIDENT_KEY = 'queue.failed_jobs.incident_active';

    private const INCIDENT_STARTED_KEY = 'queue.failed_jobs.incident_started_at';

    private const INCIDENT_EXCEPTION_KEY = 'queue.failed_jobs.incident_exception';

    private const INCIDENT_QUEUES_KEY = 'queue.failed_jobs.incident_queues';

    private const INCIDENT_JOB_CLASSES_KEY = 'queue.failed_jobs.incident_job_classes';

    private const INCIDENT_SUMMARIZED_KEY = 'queue.failed_jobs.incident_summarized_count';

    private const INCIDENT_TOTAL_KEY = 'queue.failed_jobs.incident_total_count';

    public function handle(): int
    {
        $current = (int) DB::table('failed_jobs')->count();
        $previous = (int) Cache::get(self::CACHE_KEY, 0);

        if ($current === 0) {
            // The incident is over; the next failure starts a new alert cycle.
            $wasIncident = (bool) Cache::get(self::INCIDENT_KEY, false);
            $startedAt = Cache::get(self::INCIDENT_STARTED_KEY);
            $topException = (string) Cache::get(self::INCIDENT_EXCEPTION_KEY, 'Unknown failure');
            $queueSummary = (string) Cache::get(self::INCIDENT_QUEUES_KEY, 'Unknown');
            $jobClassSummary = (string) Cache::get(self::INCIDENT_JOB_CLASSES_KEY, 'Unknown job');
            $summarizedCount = (int) Cache::get(self::INCIDENT_SUMMARIZED_KEY, 0);
            $incidentCount = (int) Cache::get(self::INCIDENT_TOTAL_KEY, 0);

            Cache::forget(self::INCIDENT_KEY);
            Cache::forget(self::INCIDENT_STARTED_KEY);
            Cache::forget(self::INCIDENT_EXCEPTION_KEY);
            Cache::forget(self::INCIDENT_QUEUES_KEY);
            Cache::forget(self::INCIDENT_JOB_CLASSES_KEY);
            Cache::forget(self::INCIDENT_SUMMARIZED_KEY);
            Cache::forget(self::INCIDENT_TOTAL_KEY);
            Cache::forever(self::CACHE_KEY, 0);

            if ($wasIncident) {
                $this->sendRecoveryNotice(
                    previous: $previous,
                    startedAt: $startedAt ? Carbon::parse($startedAt) : null,
                    topException: $topException,
                    queueSummary: $queueSummary,
                    jobClassSummary: $jobClassSummary,
                    summarizedCount: $summarizedCount,
                    incidentCount: $incidentCount,
                );
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

        $sampleSize = max(1, (int) setting('notification', 'queue_failed_jobs_sample_size', 500));
        $failedJobs = DB::table('failed_jobs')
            ->orderByDesc('id')
            ->limit($sampleSize)
            ->get(['queue', 'payload', 'exception']);
        $topException = $this->mostCommonFailure($failedJobs->pluck('exception'));
        $queueSummary = $this->summarizeQueues($failedJobs);
        $jobClassSummary = $this->summarizeJobClasses($failedJobs);
        $summarizedCount = $failedJobs->count();

        try {
            Mail::to($recipient)->send(new QueueFailedJobsAlertMail(
                currentCount: $current,
                previousCount: $previous,
                topException: $topException,
                queueSummary: $queueSummary,
                jobClassSummary: $jobClassSummary,
                summarizedCount: $summarizedCount,
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
        Cache::forever(self::INCIDENT_STARTED_KEY, now()->toIso8601String());
        Cache::forever(self::INCIDENT_EXCEPTION_KEY, $topException);
        Cache::forever(self::INCIDENT_QUEUES_KEY, $queueSummary);
        Cache::forever(self::INCIDENT_JOB_CLASSES_KEY, $jobClassSummary);
        Cache::forever(self::INCIDENT_SUMMARIZED_KEY, $summarizedCount);
        Cache::forever(self::INCIDENT_TOTAL_KEY, $current);

        Log::warning('Failed-job backlog grew; incident alert sent.', [
            'current' => $current,
            'previous' => $previous,
            'recipient' => $recipient,
            'top_exception' => $topException,
        ]);

        $this->components->warn(
            "Failed jobs grew from {$previous} to {$current}; alerted {$recipient}."
        );

        return self::SUCCESS;
    }

    private function sendRecoveryNotice(
        int $previous,
        ?Carbon $startedAt,
        string $topException,
        string $queueSummary,
        string $jobClassSummary,
        int $summarizedCount,
        int $incidentCount,
    ): void {
        $recipient = setting('notification', 'admin_alert_email');

        if (empty($recipient)) {
            Log::error('Failed-job backlog recovered but no admin alert email is configured.', [
                'previous' => $previous,
            ]);

            return;
        }

        try {
            Mail::to($recipient)->send(new QueueFailedJobsRecoveredMail(
                previousCount: $previous,
                startedAt: $startedAt,
                recoveredAt: now(),
                topException: $topException,
                queueSummary: $queueSummary,
                jobClassSummary: $jobClassSummary,
                summarizedCount: $summarizedCount,
                incidentCount: $incidentCount,
            ));
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

    /**
     * @param  iterable<int, string|null>  $exceptions
     */
    private function mostCommonFailure(iterable $exceptions): string
    {
        $counts = [];

        foreach ($exceptions as $exception) {
            $summary = $this->summarizeException($exception);
            $counts[$summary] = ($counts[$summary] ?? 0) + 1;
        }

        if ($counts === []) {
            return 'Unknown failure';
        }

        arsort($counts);

        return (string) array_key_first($counts);
    }

    private function summarizeException(?string $exception): string
    {
        $firstLine = trim(preg_split('/\r\n|\r|\n/', $exception ?? '', 2)[0] ?? '');

        if ($firstLine === '') {
            return 'Unknown failure';
        }

        return Str::limit($firstLine, 300);
    }

    /**
     * @param  iterable<int, object{queue: string|null, payload: string|null}>  $failedJobs
     */
    private function summarizeQueues(iterable $failedJobs): string
    {
        $counts = [];

        foreach ($failedJobs as $job) {
            $queue = trim((string) ($job->queue ?? ''));
            $queue = $queue === '' ? 'default' : $queue;
            $counts[$queue] = ($counts[$queue] ?? 0) + 1;
        }

        return $this->formatCountSummary($counts);
    }

    /**
     * @param  iterable<int, object{payload: string|null}>  $failedJobs
     */
    private function summarizeJobClasses(iterable $failedJobs): string
    {
        $counts = [];

        foreach ($failedJobs as $job) {
            $class = $this->jobClassFromPayload($job->payload ?? null);
            $counts[$class] = ($counts[$class] ?? 0) + 1;
        }

        return $this->formatCountSummary($counts);
    }

    private function jobClassFromPayload(?string $payload): string
    {
        $data = json_decode($payload ?? '', true);

        if (! is_array($data)) {
            return 'Unknown job';
        }

        $class = $data['displayName'] ?? $data['job'] ?? null;

        return is_string($class) && trim($class) !== '' ? trim($class) : 'Unknown job';
    }

    /**
     * @param  array<string, int>  $counts
     */
    private function formatCountSummary(array $counts, int $limit = 5): string
    {
        if ($counts === []) {
            return 'Unknown';
        }

        arsort($counts);
        $total = count($counts);
        $parts = [];

        foreach (array_slice($counts, 0, $limit, true) as $name => $count) {
            $parts[] = "{$name} ({$count})";
        }

        if ($total > $limit) {
            $parts[] = sprintf('+%d more', $total - $limit);
        }

        return implode(', ', $parts);
    }
}
