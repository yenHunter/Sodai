<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function failed()
    {
        $this->ensureSuperAdmin();

        $failedJobs = DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->orderByDesc('id')
            ->paginate(25);

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

        return view('admin.system.failed-jobs.index', compact('failedJobs'));
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);
    }
}
