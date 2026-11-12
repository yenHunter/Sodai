<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Queue Failed-Jobs Alert</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; }
        .header { background: #8a1f1f; padding: 30px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 22px; }
        .body { padding: 30px; color: #333333; line-height: 1.6; }
        .counts { margin: 24px 0; padding: 16px; background: #fff4f4; border-left: 4px solid #8a1f1f; }
        .counts strong { font-size: 22px; }
        code { background: #f1f1f1; padding: 2px 6px; border-radius: 3px; }
        .muted { color: #777777; font-size: 12px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Failed-Jobs Backlog Alert</h1>
        </div>
        <div class="body">
            <p>The failed-jobs backlog has grown since the previous check.</p>

            <div class="counts">
                <div>Previous count: <strong>{{ $previousCount }}</strong></div>
                <div>Current count: <strong>{{ $currentCount }}</strong></div>
            </div>

            <p>
                This usually means queued work — including guest order confirmation emails — is
                failing. Check the failure details with:
            </p>
            <p><code>php artisan queue:failed</code></p>

            <p>Retry a specific job after fixing the cause:</p>
            <p><code>php artisan queue:retry &lt;uuid&gt;</code></p>

            <p class="muted">
                Sent by the scheduled <code>queue:alert-failed-jobs</code> check. You are receiving
                this because it is configured as the store notification email.
            </p>
        </div>
    </div>
</body>
</html>
