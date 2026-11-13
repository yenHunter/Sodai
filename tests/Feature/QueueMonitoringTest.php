<?php

namespace Tests\Feature;

use App\Listeners\SendQueueBusyAlert;
use App\Mail\Admin\QueueBusyAlertMail;
use App\Mail\Admin\QueueFailedJobsAlertMail;
use App\Mail\Admin\QueueFailedJobsRecoveredMail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\QueueBusy;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class QueueMonitoringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Mail::fake();

        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
        ]);
    }

    public function test_alerts_when_the_failed_jobs_backlog_grows(): void
    {
        $this->recordFailedJob();

        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, function (QueueFailedJobsAlertMail $mail) {
            return $mail->hasTo('ops@example.com')
                && $mail->currentCount === 1
                && $mail->previousCount === 0
                && $mail->topException === 'SMTP timeout'
                && $mail->queueSummary === 'default (1)'
                && $mail->jobClassSummary === 'App\\Mail\\Customer\\OrderConfirmationMail (1)'
                && $mail->summarizedCount === 1;
        });
    }

    public function test_alert_reports_the_affected_queues_and_job_classes(): void
    {
        $this->recordFailedJob('SMTP timeout', 'default', 'App\\Mail\\Customer\\OrderConfirmationMail');
        $this->recordFailedJob('SMTP timeout', 'emails', 'App\\Mail\\Admin\\QueueBusyAlertMail');
        $this->recordFailedJob('SMTP timeout', 'default', 'App\\Mail\\Customer\\OrderConfirmationMail');

        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, function (QueueFailedJobsAlertMail $mail) {
            return $mail->queueSummary === 'default (2), emails (1)'
                && $mail->jobClassSummary === 'App\\Mail\\Customer\\OrderConfirmationMail (2), App\\Mail\\Admin\\QueueBusyAlertMail (1)'
                && $mail->summarizedCount === 3;
        });
    }

    public function test_alert_summarizes_only_the_latest_five_hundred_failed_jobs(): void
    {
        for ($i = 0; $i < 501; $i++) {
            $this->recordFailedJob();
        }

        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, function (QueueFailedJobsAlertMail $mail) {
            return $mail->currentCount === 501
                && $mail->summarizedCount === 500;
        });
    }

    public function test_alert_uses_the_configured_failed_jobs_sample_size(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
            'queue_failed_jobs_sample_size' => 2,
        ]);

        $this->recordFailedJob();
        $this->recordFailedJob();
        $this->recordFailedJob();

        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, function (QueueFailedJobsAlertMail $mail) {
            return $mail->currentCount === 3
                && $mail->summarizedCount === 2;
        });
    }

    public function test_alert_reports_the_most_common_failure_exception(): void
    {
        $this->recordFailedJob('SMTP timeout');
        $this->recordFailedJob('SQLSTATE[HY000]: database is locked');
        $this->recordFailedJob('SMTP timeout');

        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, function (QueueFailedJobsAlertMail $mail) {
            return $mail->topException === 'SMTP timeout';
        });
    }

    public function test_does_not_repeat_the_alert_when_the_backlog_is_unchanged(): void
    {
        $this->recordFailedJob();

        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, 1);
    }

    public function test_alerts_once_per_incident_while_the_burst_grows(): void
    {
        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        $this->recordFailedJob();
        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, 1);
    }

    public function test_a_new_incident_alerts_after_the_backlog_returns_to_zero(): void
    {
        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        DB::table('failed_jobs')->delete();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, 2);
        Mail::assertSent(QueueFailedJobsAlertMail::class, function (QueueFailedJobsAlertMail $mail) {
            return $mail->currentCount === 1 && $mail->previousCount === 0;
        });
    }

    public function test_sends_one_recovery_notice_when_the_backlog_returns_to_zero(): void
    {
        $startedAt = now()->copy();

        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        $this->travel(35)->minutes();
        DB::table('failed_jobs')->delete();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsRecoveredMail::class, function (QueueFailedJobsRecoveredMail $mail) use ($startedAt) {
            return $mail->hasTo('ops@example.com')
                && $mail->previousCount === 1
                && $mail->startedAt?->timestamp === $startedAt->timestamp
                && $mail->duration === '35 minutes'
                && $mail->topException === 'SMTP timeout'
                && $mail->queueSummary === 'default (1)'
                && $mail->jobClassSummary === 'App\\Mail\\Customer\\OrderConfirmationMail (1)'
                && $mail->summarizedCount === 1
                && $mail->incidentCount === 1;
        });

        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsRecoveredMail::class, 1);
    }

    public function test_recovery_email_tracks_when_incident_details_were_sampled(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
            'queue_failed_jobs_sample_size' => 1,
        ]);

        $this->recordFailedJob();
        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        DB::table('failed_jobs')->delete();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsRecoveredMail::class, function (QueueFailedJobsRecoveredMail $mail) {
            return $mail->summarizedCount === 1
                && $mail->incidentCount === 2;
        });
    }

    public function test_shrinking_backlog_updates_the_baseline_without_an_alert(): void
    {
        $this->recordFailedJob();
        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        DB::table('failed_jobs')->delete();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, function (QueueFailedJobsAlertMail $mail) {
            return $mail->currentCount === 1 && $mail->previousCount === 0;
        });
    }

    public function test_recovery_email_renders_the_cleared_backlog_details(): void
    {
        $html = (new QueueFailedJobsRecoveredMail(
            previousCount: 6,
            startedAt: Carbon::parse('2026-11-12 10:00:00'),
            recoveredAt: Carbon::parse('2026-11-12 10:45:00'),
            topException: 'SMTP timeout',
            queueSummary: 'default (5), emails (1)',
            jobClassSummary: 'App\\Mail\\Customer\\OrderConfirmationMail (5), App\\Mail\\Admin\\QueueBusyAlertMail (1)',
            summarizedCount: 6,
            incidentCount: 6,
        ))->render();

        $this->assertStringContainsString('returned to zero', $html);
        $this->assertStringContainsString('before recovery: <strong>6</strong>', $html);
        $this->assertStringContainsString('Incident started: <strong>12 Nov 2026, 10:00</strong>', $html);
        $this->assertStringContainsString('Duration: <strong>45 minutes</strong>', $html);
        $this->assertStringContainsString('Most common failure:', $html);
        $this->assertStringContainsString('SMTP timeout', $html);
        $this->assertStringContainsString('Affected queues:', $html);
        $this->assertStringContainsString('default (5), emails (1)', $html);
        $this->assertStringContainsString('Job classes:', $html);
        $this->assertStringContainsString('App\\Mail\\Customer\\OrderConfirmationMail (5)', $html);
        $this->assertStringContainsString('php artisan queue:failed', $html);
    }

    public function test_recovery_email_notes_when_incident_details_were_sampled(): void
    {
        $html = (new QueueFailedJobsRecoveredMail(
            previousCount: 2,
            startedAt: Carbon::parse('2026-11-12 10:00:00'),
            recoveredAt: Carbon::parse('2026-11-12 10:20:00'),
            topException: 'SMTP timeout',
            queueSummary: 'default (1)',
            jobClassSummary: 'App\\Mail\\Customer\\OrderConfirmationMail (1)',
            summarizedCount: 1,
            incidentCount: 2,
        ))->render();

        $this->assertStringContainsString(
            'Failure details are based on the most recent 1 of 2 failed jobs when this incident was alerted.',
            $html
        );
    }

    public function test_recovery_email_omits_sampling_note_for_a_fully_sampled_incident(): void
    {
        $html = (new QueueFailedJobsRecoveredMail(
            previousCount: 3,
            startedAt: Carbon::parse('2026-11-12 10:00:00'),
            recoveredAt: Carbon::parse('2026-11-12 10:20:00'),
            topException: 'SMTP timeout',
            queueSummary: 'default (3)',
            jobClassSummary: 'App\\Mail\\Customer\\OrderConfirmationMail (3)',
            summarizedCount: 3,
            incidentCount: 3,
        ))->render();

        $this->assertStringNotContainsString('Failure details are based on', $html);
    }

    public function test_alert_email_renders_the_counts_and_recovery_commands(): void
    {
        $html = (new QueueFailedJobsAlertMail(
            currentCount: 4,
            previousCount: 2,
            topException: 'SMTP timeout',
            queueSummary: 'default (3), emails (1)',
            jobClassSummary: 'App\\Mail\\Customer\\OrderConfirmationMail (3), App\\Mail\\Admin\\QueueBusyAlertMail (1)',
            summarizedCount: 4,
        ))->render();

        $this->assertStringContainsString('Previous count: <strong>2</strong>', $html);
        $this->assertStringContainsString('Current count: <strong>4</strong>', $html);
        $this->assertStringContainsString('Most common failure:', $html);
        $this->assertStringContainsString('SMTP timeout', $html);
        $this->assertStringContainsString('Affected queues:', $html);
        $this->assertStringContainsString('default (3), emails (1)', $html);
        $this->assertStringContainsString('Job classes:', $html);
        $this->assertStringContainsString('App\\Mail\\Customer\\OrderConfirmationMail (3)', $html);
        $this->assertStringContainsString('php artisan queue:failed', $html);
        $this->assertStringContainsString('php artisan queue:retry', $html);
    }

    public function test_alert_email_notes_when_failure_details_are_sampled(): void
    {
        $html = (new QueueFailedJobsAlertMail(
            currentCount: 900,
            previousCount: 800,
            topException: 'SMTP timeout',
            queueSummary: 'default (500)',
            jobClassSummary: 'App\\Mail\\Customer\\OrderConfirmationMail (500)',
            summarizedCount: 500,
        ))->render();

        $this->assertStringContainsString('Failure details are based on the most recent', $html);
        $this->assertStringContainsString('500 of 900 failed jobs', $html);
    }

    public function test_queue_busy_event_emails_the_admin_with_depth_details(): void
    {
        Event::dispatch(new QueueBusy('database', 'default', 150));

        Mail::assertSent(QueueBusyAlertMail::class, function (QueueBusyAlertMail $mail) {
            return $mail->hasTo('ops@example.com')
                && $mail->connectionName === 'database'
                && $mail->queueName === 'default'
                && $mail->size === 150
                && $mail->threshold === 100;
        });
    }

    public function test_queue_busy_alert_uses_the_configured_cooldown(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
            'queue_alert_cooldown_minutes' => 5,
        ]);

        Event::dispatch(new QueueBusy('database', 'default', 150));
        Event::dispatch(new QueueBusy('database', 'default', 200));

        Mail::assertSent(QueueBusyAlertMail::class, 1);

        $this->travel(6)->minutes();
        Event::dispatch(new QueueBusy('database', 'default', 250));

        Mail::assertSent(QueueBusyAlertMail::class, 2);
    }

    public function test_queue_depth_monitor_uses_the_configured_threshold(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
            'queue_depth_threshold' => 1,
        ]);

        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => json_encode(['displayName' => 'TestJob']),
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp,
        ]);

        Event::fake([QueueBusy::class]);

        $this->artisan('queue:monitor-depth')->assertExitCode(0);

        Event::assertDispatched(QueueBusy::class, function (QueueBusy $event) {
            return $event->connectionName === 'database'
                && $event->queue === 'default'
                && $event->size === 1;
        });
    }

    public function test_queue_depth_monitor_checks_every_configured_queue(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
            'queue_depth_threshold' => 1,
            'queue_monitor_queues' => 'database:default, database:emails',
        ]);

        $this->recordQueuedJob('default');
        $this->recordQueuedJob('emails');

        Event::fake([QueueBusy::class]);

        $this->artisan('queue:monitor-depth')->assertExitCode(0);

        Event::assertDispatched(QueueBusy::class, function (QueueBusy $event) {
            return $event->connectionName === 'database'
                && $event->queue === 'default'
                && $event->size === 1;
        });

        Event::assertDispatched(QueueBusy::class, function (QueueBusy $event) {
            return $event->connectionName === 'database'
                && $event->queue === 'emails'
                && $event->size === 1;
        });
    }

    public function test_queue_depth_monitor_cli_options_override_configured_queues(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
            'queue_depth_threshold' => 1,
            'queue_monitor_queues' => 'database:default, database:emails',
        ]);

        $this->recordQueuedJob('default');
        $this->recordQueuedJob('emails');

        Event::fake([QueueBusy::class]);

        $this->artisan('queue:monitor-depth', [
            '--connection' => 'database',
            '--queue' => 'emails',
        ])->assertExitCode(0);

        Event::assertDispatchedTimes(QueueBusy::class, 1);
        Event::assertDispatched(QueueBusy::class, function (QueueBusy $event) {
            return $event->connectionName === 'database'
                && $event->queue === 'emails'
                && $event->size === 1;
        });
        Event::assertNotDispatched(QueueBusy::class, function (QueueBusy $event) {
            return $event->queue === 'default';
        });
    }

    public function test_queue_depth_monitor_clears_the_cooldown_below_the_threshold(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
            'queue_depth_threshold' => 2,
        ]);

        $key = SendQueueBusyAlert::cooldownKey('database', 'default');
        Cache::put($key, true, now()->addHour());

        $this->artisan('queue:monitor-depth')->assertExitCode(0);

        $this->assertFalse(Cache::has($key));
    }

    public function test_queue_depth_monitor_keeps_the_cooldown_at_the_threshold(): void
    {
        Setting::setMany('notification', [
            'admin_alert_email' => 'ops@example.com',
            'queue_depth_threshold' => 1,
        ]);

        $this->recordQueuedJob();

        $key = SendQueueBusyAlert::cooldownKey('database', 'default');
        Cache::put($key, true, now()->addHour());

        $this->artisan('queue:monitor-depth')->assertExitCode(0);

        $this->assertTrue(Cache::has($key));
    }

    public function test_queue_busy_alert_skips_when_no_recipient_is_configured(): void
    {
        Setting::setMany('notification', ['admin_alert_email' => '']);

        Event::dispatch(new QueueBusy('database', 'default', 150));

        Mail::assertNothingSent();
    }

    public function test_queue_busy_alert_email_renders_the_depth_details(): void
    {
        $html = (new QueueBusyAlertMail(
            connectionName: 'database',
            queueName: 'emails',
            size: 250,
            threshold: 200,
        ))->render();

        $this->assertStringContainsString('database', $html);
        $this->assertStringContainsString('emails', $html);
        $this->assertStringContainsString('250', $html);
        $this->assertStringContainsString('200-job threshold', $html);
        $this->assertStringContainsString('sudo supervisorctl status', $html);
    }

    public function test_queue_monitor_and_failed_jobs_alert_are_scheduled(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('queue:monitor-depth')
            ->expectsOutputToContain('queue:alert-failed-jobs')
            ->assertExitCode(0);
    }

    private function recordQueuedJob(string $queue = 'default'): void
    {
        DB::table('jobs')->insert([
            'queue' => $queue,
            'payload' => json_encode(['displayName' => 'TestJob']),
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp,
        ]);
    }

    private function recordFailedJob(
        string $exception = 'SMTP timeout',
        string $queue = 'default',
        string $jobClass = 'App\\Mail\\Customer\\OrderConfirmationMail',
    ): void {
        DB::table('failed_jobs')->insert([
            'uuid' => (string) Str::uuid(),
            'connection' => 'database',
            'queue' => $queue,
            'payload' => json_encode(['displayName' => $jobClass]),
            'exception' => $exception,
            'failed_at' => now(),
        ]);
    }
}
