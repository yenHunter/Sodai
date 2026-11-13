<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SystemQueueController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureSuperAdmin();

        $queue = $this->queryString($request, 'queue', 100);
        $jobClass = $this->queryString($request, 'job_class', 255);
        $status = strtolower($this->queryString($request, 'status', 20));

        if (! in_array($status, ['pending', 'delayed', 'reserved'], true)) {
            $status = '';
        }

        $now = now()->timestamp;

        $jobs = DB::table('jobs')
            ->when($queue !== '', fn ($query) => $query->where('queue', $queue))
            ->when($jobClass !== '', fn ($query) => $query->where('payload', 'like', '%'.$jobClass.'%'))
            ->when($status === 'reserved', fn ($query) => $query->whereNotNull('reserved_at'))
            ->when($status === 'delayed', fn ($query) => $query
                ->whereNull('reserved_at')
                ->where('available_at', '>', $now))
            ->when($status === 'pending', fn ($query) => $query
                ->whereNull('reserved_at')
                ->where('available_at', '<=', $now))
            ->orderByDesc('id')
            ->paginate(25, ['*'], 'queued_page')
            ->withQueryString();

        $queues = DB::table('jobs')
            ->select('queue')
            ->distinct()
            ->orderBy('queue')
            ->pluck('queue');

        if ($queue !== '' && ! $queues->contains($queue)) {
            $queues = $queues->push($queue)->sort()->values();
        }

        $this->transformQueuedJobs($jobs);

        $failedQueue = $this->queryString($request, 'failed_queue', 100);
        $failedJobClass = $this->queryString($request, 'failed_job_class', 255);

        $failedJobs = DB::table('failed_jobs')
            ->when($failedQueue !== '', fn ($query) => $query->where('queue', $failedQueue))
            ->when($failedJobClass !== '', fn ($query) => $query->where('payload', 'like', '%'.$failedJobClass.'%'))
            ->orderByDesc('failed_at')
            ->orderByDesc('id')
            ->paginate(25, ['*'], 'failed_page')
            ->withQueryString();

        $queueSummary = DB::table('failed_jobs')
            ->select('queue')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('queue')
            ->orderByDesc('total')
            ->orderBy('queue')
            ->get();

        $totalFailedJobs = $queueSummary->sum('total');

        $failedQueues = DB::table('failed_jobs')
            ->select('queue')
            ->distinct()
            ->orderBy('queue')
            ->pluck('queue');

        if ($failedQueue !== '' && ! $failedQueues->contains($failedQueue)) {
            $failedQueues = $failedQueues->push($failedQueue)->sort()->values();
        }

        $this->transformFailedJobs($failedJobs);

        $failedJobFilters = array_filter([
            'tab' => 'failed',
            'failed_queue' => $failedQueue,
            'failed_job_class' => $failedJobClass,
        ], fn ($value) => $value !== '');

        $activeTab = $request->query('tab') === 'failed' ? 'failed' : 'queued';

        return view('admin.system.jobs.index', compact(
            'jobs',
            'queues',
            'queue',
            'jobClass',
            'status',
            'failedJobs',
            'failedQueues',
            'failedQueue',
            'failedJobClass',
            'failedJobFilters',
            'queueSummary',
            'totalFailedJobs',
            'activeTab',
        ));
    }

    public function failed(Request $request): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $params = ['tab' => 'failed'];

        $queue = $this->queryString($request, 'queue', 100);
        $jobClass = $this->queryString($request, 'job_class', 255);

        if ($queue !== '') {
            $params['failed_queue'] = $queue;
        }

        if ($jobClass !== '') {
            $params['failed_job_class'] = $jobClass;
        }

        $page = $request->query('page');

        if (is_numeric($page) && (int) $page > 0) {
            $params['failed_page'] = (int) $page;
        }

        return redirect()->route('admin.system.queued-jobs.index', $params);
    }

    public function bulkRetry(Request $request)
    {
        $this->ensureSuperAdmin();

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => ['string', 'uuid', 'distinct'],
        ]);

        $ids = array_values($validated['ids']);
        $existing = DB::table('failed_jobs')
            ->whereIn('uuid', $ids)
            ->orderBy('id')
            ->pluck('uuid')
            ->all();

        if ($existing === []) {
            return $this->failedJobsRedirect($request)
                ->with('error', 'No retryable failed jobs were found.');
        }

        try {
            Artisan::call('queue:retry', ['id' => $existing]);
        } catch (Throwable $exception) {
            report($exception);

            return $this->failedJobsRedirect($request)
                ->with('error', 'The failed jobs could not be queued for retry.');
        }

        $remaining = DB::table('failed_jobs')->whereIn('uuid', $existing)->count();
        $retried = count($existing) - $remaining;

        if ($retried === 0) {
            return $this->failedJobsRedirect($request)
                ->with('error', 'The failed jobs could not be queued for retry.');
        }

        $message = $retried.' '.Str::plural('failed job', $retried).' queued for retry';

        if ($remaining > 0) {
            return $this->failedJobsRedirect($request)
                ->with('warning', $message.'; '.$remaining.' could not be queued.');
        }

        return $this->failedJobsRedirect($request)
            ->with('success', $message.'.');
    }

    public function retry(Request $request, string $uuid)
    {
        $this->ensureSuperAdmin();

        if (! DB::table('failed_jobs')->where('uuid', $uuid)->exists()) {
            return $this->failedJobsRedirect($request)
                ->with('error', 'Failed job not found.');
        }

        try {
            Artisan::call('queue:retry', ['id' => [$uuid]]);
        } catch (Throwable $exception) {
            report($exception);

            return $this->failedJobsRedirect($request)
                ->with('error', 'The failed job could not be queued for retry.');
        }

        if (DB::table('failed_jobs')->where('uuid', $uuid)->exists()) {
            return $this->failedJobsRedirect($request)
                ->with('error', 'The failed job could not be queued for retry.');
        }

        return $this->failedJobsRedirect($request)
            ->with('success', 'Failed job queued for retry.');
    }

    private function transformQueuedJobs(LengthAwarePaginator $jobs): void
    {
        $jobs->getCollection()->transform(function ($job) {
            $payload = json_decode((string) $job->payload, true);
            $class = is_array($payload) ? ($payload['displayName'] ?? $payload['job'] ?? null) : null;

            $job->job_class = is_string($class) && trim($class) !== ''
                ? trim($class)
                : 'Unknown job';
            $job->status = $job->reserved_at
                ? 'Reserved'
                : ($job->available_at > now()->timestamp ? 'Delayed' : 'Pending');
            $job->available_at_display = Carbon::createFromTimestamp($job->available_at)
                ->format('d M Y, H:i');
            $job->created_at_display = Carbon::createFromTimestamp($job->created_at)
                ->format('d M Y, H:i');

            return $job;
        });
    }

    private function transformFailedJobs(LengthAwarePaginator $failedJobs): void
    {
        $failedJobs->getCollection()->transform(function ($job) {
            $payload = json_decode((string) $job->payload, true);
            $class = is_array($payload) ? ($payload['displayName'] ?? $payload['job'] ?? null) : null;

            $job->job_class = is_string($class) && trim($class) !== ''
                ? trim($class)
                : 'Unknown job';

            $exceptionFirstLine = preg_split('/\R/u', trim((string) $job->exception), 2)[0] ?? '';
            $job->exception_summary = Str::limit(trim($exceptionFirstLine), 180) ?: 'Unknown exception';
            $job->failed_at_display = $job->failed_at
                ? Carbon::parse($job->failed_at)->format('d M Y, H:i')
                : '—';

            return $job;
        });
    }

    private function failedJobsRedirect(Request $request): RedirectResponse
    {
        $filters = ['tab' => 'failed'];

        foreach (['failed_queue' => 100, 'failed_job_class' => 255] as $key => $limit) {
            $value = $request->query($key);

            if (is_string($value) && trim($value) !== '') {
                $filters[$key] = mb_substr(trim($value), 0, $limit);
            }
        }

        return redirect()->route('admin.system.queued-jobs.index', $filters);
    }

    private function queryString(Request $request, string $key, int $limit): string
    {
        $value = $request->query($key);

        return is_string($value) ? mb_substr(trim($value), 0, $limit) : '';
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
    }
}
