# Queue and job operations

Sodai queues customer-facing mail: the guest order confirmation is dispatched as a queued
job, and the default `QUEUE_CONNECTION=database` stores it in the `jobs` table until a
worker picks it up. Checkout never waits on delivery, so a missing worker does not break
ordering — it silently stops the emails, which is why this page exists.

`composer run dev` starts a queue listener next to the server and Vite. Everywhere else,
run a supervised worker.

## Running a worker

```bash
php artisan queue:work
```

Without a running worker, checkouts still succeed but confirmation emails are not
delivered. After deploying, check the failed-jobs table (`php artisan queue:failed`).

For production, [`deploy/supervisor/sodai-queue-worker.conf`](../deploy/supervisor/sodai-queue-worker.conf)
provides a Supervisor program that restarts the worker and recycles it hourly. Update its
`directory`, `command`, and `user` values for the host, then install it:

```bash
sudo cp deploy/supervisor/sodai-queue-worker.conf /etc/supervisor/conf.d/sodai-queue-worker.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start sodai-queue-worker:*
```

Ensure `/var/log/sodai` exists and is writable by the worker user. After each release,
run `sudo supervisorctl restart sodai-queue-worker:*` to load the new code immediately.

## Monitoring and retrying failed jobs

The Supervisor command retries a job up to three times (`--tries=3`). A job that still
fails is moved to the `failed_jobs` table; checkout is unaffected, but that confirmation
email was not delivered. Failed queued mail appears as an
`Illuminate\Mail\SendQueuedMailable` job whose payload contains the order confirmation.

```bash
# Worker process state (restarts are automatic)
sudo supervisorctl status sodai-queue-worker:*

# Failed jobs: UUID, connection, queue, job class, failure time
php artisan queue:failed

# Retry one job, or every failed job (a running worker picks them up)
php artisan queue:retry <uuid>
php artisan queue:retry all

# Remove a permanently failed job (bad address, obsolete order)
php artisan queue:forget <uuid>

# Destructive: removes every failed job
php artisan queue:flush
```

Check the worker log at `/var/log/sodai/queue-worker.log` and application errors in
`storage/logs/laravel.log`. If queueing itself fails during checkout, the checkout
service logs the order ID and exception; worker-side delivery failures are recorded in
`failed_jobs`.

Retries can duplicate an email if the provider accepted it but the job failed afterward,
so confirm the order notification state before bulk-retrying `all`.

## Queue-depth and failed-job alerts

For a manual queue-size check, run the wrapper with the configured threshold and queues:

```bash
php artisan queue:monitor-depth
```

The scheduler already registers two checks every ten minutes:

- `queue:monitor-depth`, which checks every `connection:queue` pair in the **Monitored
  Queues** setting (default `database:default`) against the **Queue Depth Threshold**
  from Settings → Notification (default 100), and appends output to
  `storage/logs/queue-monitor.log`. When a queue reaches the threshold, its `QueueBusy`
  event emails the store notification address with the depth details, at most once per
  queue per **Queue Alert Cooldown** (default 60 minutes). When the queue falls below
  the threshold, the cooldown is cleared so a new incident alerts immediately.
- `queue:alert-failed-jobs`, which emails the store notification address once per
  incident. It alerts on the first increase after the backlog was zero, stays quiet while
  the same backlog keeps growing, and sends a one-time recovery notice after
  `failed_jobs` returns to zero. Both emails include the most common failure exception
  and the affected queues and job classes; the recovery notice also includes when the
  incident started and how long it lasted. Alert details are summarized from the newest
  **Failed Jobs Sample Size** failures (default 500) so a very large backlog is not
  loaded in full; the recovery notice notes when those incident details were sampled.

Both require Laravel's scheduler to be running, normally via this cron entry on the host:

```cron
* * * * * cd /var/www/sodai && php artisan schedule:run >> /dev/null 2>&1
```

## Related

- [`deploy/supervisor/sodai-queue-worker.conf`](../deploy/supervisor/sodai-queue-worker.conf) — the worker program
- [`app/Console/Commands/MonitorQueueDepthCommand.php`](../app/Console/Commands/MonitorQueueDepthCommand.php)
- [`app/Console/Commands/AlertFailedQueueJobsCommand.php`](../app/Console/Commands/AlertFailedQueueJobsCommand.php)
- [`routes/console.php`](../routes/console.php) — the schedule that registers both checks
