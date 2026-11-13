<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SystemQueueController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $queue = mb_substr(trim((string) $request->query('queue', '')), 0, 100);
        $jobClass = mb_substr(trim((string) $request->query('job_class', '')), 0, 255);

        $jobs = DB::table('jobs')
            ->when($queue !== '', fn ($query) => $query->where('queue', $queue))
            ->when($jobClass !== '', fn ($query) => $query->where('payload', 'like', '%'.$jobClass.'%'))
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
        ));
    }
}
