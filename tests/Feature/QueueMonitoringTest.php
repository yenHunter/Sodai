<?php

namespace Tests\Feature;

use App\Mail\Admin\QueueBusyAlertMail;
use App\Mail\Admin\QueueFailedJobsAlertMail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\QueueBusy;
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
                && $mail->previousCount === 0;
        });
    }

    public function test_does_not_repeat_the_alert_when_the_backlog_is_unchanged(): void
    {
        $this->recordFailedJob();

        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, 1);
    }

    public function test_alerts_again_when_the_backlog_grows_further(): void
    {
        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        $this->recordFailedJob();
        $this->artisan('queue:alert-failed-jobs')->assertExitCode(0);

        Mail::assertSent(QueueFailedJobsAlertMail::class, function (QueueFailedJobsAlertMail $mail) {
            return $mail->currentCount === 2 && $mail->previousCount === 1;
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

    public function test_alert_email_renders_the_counts_and_recovery_commands(): void
    {
        $html = (new QueueFailedJobsAlertMail(
            currentCount: 4,
            previousCount: 2,
        ))->render();

        $this->assertStringContainsString('Previous count: <strong>2</strong>', $html);
        $this->assertStringContainsString('Current count: <strong>4</strong>', $html);
        $this->assertStringContainsString('php artisan queue:failed', $html);
        $this->assertStringContainsString('php artisan queue:retry', $html);
    }

    public function test_queue_busy_event_emails_the_admin_with_depth_details(): void
    {
        Event::dispatch(new QueueBusy('database', 'default', 150));

        Mail::assertSent(QueueBusyAlertMail::class, function (QueueBusyAlertMail $mail) {
            return $mail->hasTo('ops@example.com')
                && $mail->connectionName === 'database'
                && $mail->queueName === 'default'
                && $mail->size === 150;
        });
    }

    public function test_queue_busy_alert_is_limited_to_once_per_hour_per_queue(): void
    {
        Event::dispatch(new QueueBusy('database', 'default', 150));
        Event::dispatch(new QueueBusy('database', 'default', 200));

        Mail::assertSent(QueueBusyAlertMail::class, 1);
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
        ))->render();

        $this->assertStringContainsString('database', $html);
        $this->assertStringContainsString('emails', $html);
        $this->assertStringContainsString('250', $html);
        $this->assertStringContainsString('sudo supervisorctl status', $html);
    }

    public function test_queue_monitor_and_failed_jobs_alert_are_scheduled(): void
    {
        $this->artisan('schedule:list')
            ->expectsOutputToContain('queue:monitor database:default --max=100')
            ->expectsOutputToContain('queue:alert-failed-jobs')
            ->assertExitCode(0);
    }

    private function recordFailedJob(): void
    {
        DB::table('failed_jobs')->insert([
            'uuid' => (string) Str::uuid(),
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['displayName' => 'App\\Mail\\Customer\\OrderConfirmationMail']),
            'exception' => 'SMTP timeout',
            'failed_at' => now(),
        ]);
    }
}
