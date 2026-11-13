<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Recovered</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #1f5c3a; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; }
        .body { padding: 30px; color: #333333; line-height: 1.6; }
        .count { margin: 24px 0; padding: 16px; background: #f2fbf6; border-left: 4px solid #1f5c3a; }
        .count strong { font-size: 22px; }
        code { background: #f1f1f1; padding: 2px 6px; border-radius: 3px; }
        .muted { color: #777777; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Failed-Jobs Backlog Cleared</h1>
        </div>
        <div class="body">
            <p>The failed-jobs backlog has returned to zero.</p>

            <div class="count">
                <div>Backlog before recovery: <strong>{{ $previousCount }}</strong></div>
                <div>Current backlog: <strong>0</strong></div>
                @if ($startedAt)
                    <div>Incident started: <strong>{{ $startedAt->format('d M Y, H:i') }}</strong></div>
                    <div>Duration: <strong>{{ $duration }}</strong></div>
                @else
                    <div>Incident start time: <strong>unknown</strong></div>
                @endif
            </div>

            @if ($summarizedCount > 0 && $summarizedCount < $incidentCount)
                <p class="muted">Failure details are based on the most recent {{ number_format($summarizedCount) }} of {{ number_format($incidentCount) }} failed jobs when this incident was alerted.</p>
            @endif

            <p><strong>Most common failure:</strong><br>
                <code>{{ $topException }}</code></p>

            <p><strong>Affected queues:</strong><br>{{ $queueSummary }}</p>

            <p><strong>Job classes:</strong><br>{{ $jobClassSummary }}</p>

            <p>
                Guest order confirmation emails and other queued work should be flowing again.
                Confirm the worker is healthy and no new failures arrive:
            </p>
            <p><code>sudo supervisorctl status sodai-queue-worker:*</code><br>
                <code>php artisan queue:failed</code></p>

            <p class="muted">
                Sent once when the incident tracked by <code>queue:alert-failed-jobs</code> clears.
            </p>
        </div>
    </div>
</body>
</html>
