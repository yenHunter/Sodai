<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Depth Alert</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #8a5a1f; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; }
        .body { padding: 30px; color: #333333; line-height: 1.6; }
        .count { margin: 24px 0; padding: 16px; background: #fffaf0; border-left: 4px solid #8a5a1f; }
        .count strong { font-size: 22px; }
        code { background: #f1f1f1; padding: 2px 6px; border-radius: 3px; }
        .muted { color: #777777; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Queue Depth Alert</h1>
        </div>
        <div class="body">
            <p>The queue monitor found at least the configured {{ $threshold }}-job threshold.</p>

            <div class="count">
                <div>Connection: <strong>{{ $connectionName }}</strong></div>
                <div>Queue: <strong>{{ $queueName }}</strong></div>
                <div>Pending jobs: <strong>{{ $size }}</strong></div>
            </div>

            <p>
                A growing queue can delay guest order confirmation emails and other
                background work. Confirm the worker is running:
            </p>
            <p><code>sudo supervisorctl status sodai-queue-worker:*</code></p>

            <p>Then inspect queue depth and failed jobs:</p>
            <p><code>php artisan queue:monitor {{ $connectionName }}:{{ $queueName }} --max={{ $threshold }}</code><br>
                <code>php artisan queue:failed</code></p>

            <p class="muted">
                Sent when the scheduled <code>queue:monitor</code> check exceeded its threshold.
                Alerts are limited to one per queue per hour.
            </p>
        </div>
    </div>
</body>
</html>
