<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $queue = mb_substr(trim((string) $request->query('queue', '')), 0, 100);
        $jobClass = mb_substr(trim((string) $request->query('job_class', '')), 0, 255);
        $status = strtolower(trim((string) $request->query('status', '')));

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
            ->paginate(25)
            ->withQueryString();

        $queues = DB::table('jobs')
            ->select('queue')
            ->distinct()
            ->orderBy('queue')
            ->pluck('queue');

        if ($queue !== '' && ! $queues->contains($queue)) {
            $queues = $queues->push($queue)->sort()->values();
        }

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

        return view('admin.system.queued-jobs.index', compact(
            'jobs',
            'queues',
            'queue',
            'jobClass',
            'status',
        ));
    }

    public function failed(Request $request)
    {
        $this->ensureSuperAdmin();

        $queue = mb_substr(trim((string) $request->query('queue', '')), 0, 100);
        $jobClass = mb_substr(trim((string) $request->query('job_class', '')), 0, 255);

        $failedJobs = DB::table('failed_jobs')
            ->when($queue !== '', fn ($query) => $query->where('queue', $queue))
            ->when($jobClass !== '', fn ($query) => $query->where('payload', 'like', '%'.$jobClass.'%'))
            ->orderByDesc('failed_at')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $queues = DB::table('failed_jobs')
            ->select('queue')
            ->distinct()
            ->orderBy('queue')
            ->pluck('queue');

        if ($queue !== '' && ! $queues->contains($queue)) {
            $queues = $queues->push($queue)->sort()->values();
        }

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

        $failedJobFilters = array_filter([
            'queue' => $queue,
            'job_class' => $jobClass,
        ], fn ($value) => $value !== '');

        return view('admin.system.failed-jobs.index', compact(
            'failedJobs',
            'queues',
            'queue',
            'jobClass',
            'failedJobFilters',
        ));
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

    private function failedJobsRedirect(Request $request): RedirectResponse
    {
        $filters = [];

        foreach (['queue' => 100, 'job_class' => 255] as $key => $limit) {
            $value = $request->query($key);

            if (is_string($value) && trim($value) !== '') {
                $filters[$key] = mb_substr(trim($value), 0, $limit);
            }
        }

        return redirect()->route('admin.system.failed-jobs.index', $filters);
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
    }
}
