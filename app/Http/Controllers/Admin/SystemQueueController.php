<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SystemQueueController extends Controller
{
    public function index()
    {
        abort_unless(auth('admin')->user()?->isSuperAdmin(), 403);

        $jobs = DB::table('jobs')
            ->orderByDesc('id')
            ->paginate(25);

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

        return view('admin.system.queued-jobs.index', compact('jobs'));
    }
}
