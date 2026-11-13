<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class SystemQueueTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    public function test_super_admin_can_view_queued_jobs(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordQueuedJob();

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index'))
            ->assertOk()
            ->assertSee('Queued Jobs')
            ->assertSee('Failed Jobs')
            ->assertSee('id="queued-jobs-pane"', false)
            ->assertSee('id="failed-jobs-pane"', false)
            ->assertSee('system-jobs-table', false)
            ->assertSee('jobs-sticky-column', false)
            ->assertSee('default')
            ->assertSee('OrderConfirmationMail')
            ->assertSee('Pending');
    }

    public function test_super_admin_can_view_failed_jobs(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob();

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', ['tab' => 'failed']))
            ->assertOk()
            ->assertSee('Failed Jobs')
            ->assertSee('emails')
            ->assertSee('PaymentConfirmationMail')
            ->assertSee('RuntimeException: SMTP connection failed')
            ->assertSee('View details')
            ->assertSee('failure-details-1', false)
            ->assertSee('data-copy-failure', false)
            ->assertSee('data-copy-target="failure-exception-1"', false)
            ->assertSee('id="failure-exception-1"', false)
            ->assertSee('Copy exception')
            ->assertSee('bulkRetryForm', false)
            ->assertSee('data-failed-job-checkbox', false)
            ->assertSee('Retry selected')
            ->assertSee('system-jobs-table--failed', false)
            ->assertSee('jobs-sticky-column', false)
            ->assertSee('name="tab" value="failed"', false)
            ->assertSee('Stack trace: #0 {main}');
    }

    public function test_legacy_failed_jobs_route_redirects_to_combined_page(): void
    {
        $admin = $this->createSuperAdmin();

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.failed-jobs.index', [
                'queue' => 'emails',
                'job_class' => 'PaymentConfirmationMail',
                'page' => 2,
            ]))
            ->assertRedirect(route('admin.system.queued-jobs.index', [
                'tab' => 'failed',
                'failed_queue' => 'emails',
                'failed_job_class' => 'PaymentConfirmationMail',
                'failed_page' => 2,
            ]));
    }

    public function test_failed_jobs_summary_counts_jobs_by_queue(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob('emails', 'App\\Jobs\\FirstJob');
        $this->recordFailedJob('emails', 'App\\Jobs\\SecondJob');
        $this->recordFailedJob('default', 'App\\Jobs\\ThirdJob');

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', ['tab' => 'failed']))
            ->assertOk()
            ->assertSee('Failed Jobs by Queue')
            ->assertSee('All Failed Jobs')
            ->assertSee(e(route('admin.system.queued-jobs.index', ['tab' => 'failed'])), false)
            ->assertSee(e(route('admin.system.queued-jobs.index', [
                'tab' => 'failed',
                'failed_queue' => 'emails',
            ])), false)
            ->assertSee(e(route('admin.system.queued-jobs.index', [
                'tab' => 'failed',
                'failed_queue' => 'default',
            ])), false)
            ->assertViewHas('totalFailedJobs', 3)
            ->assertViewHas('queueSummary', function ($summary) {
                return $summary->pluck('total', 'queue')->all() === ['emails' => 2, 'default' => 1];
            });
    }

    public function test_all_failed_jobs_summary_card_is_active_without_queue_filter(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob('emails', 'App\\Jobs\\FirstJob');
        $this->recordFailedJob('default', 'App\\Jobs\\SecondJob');

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', ['tab' => 'failed']))
            ->assertOk()
            ->assertSee('Active')
            ->assertSee('aria-current="true"', false);

        $this->assertSame(1, substr_count($response->getContent(), 'aria-current="true"'));
    }

    public function test_active_queue_summary_card_is_highlighted(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob('emails', 'App\\Jobs\\FirstJob');
        $this->recordFailedJob('default', 'App\\Jobs\\SecondJob');

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', [
                'tab' => 'failed',
                'failed_queue' => 'emails',
            ]))
            ->assertOk()
            ->assertSee('Active')
            ->assertSee('aria-current="true"', false);

        $this->assertSame(1, substr_count($response->getContent(), 'aria-current="true"'));
    }

    public function test_super_admin_can_filter_failed_jobs_by_queue(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob('emails', 'App\\Mail\\Customer\\PaymentConfirmationMail');
        $this->recordFailedJob('default', 'App\\Mail\\Admin\\QueueBusyAlertMail');

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', [
                'tab' => 'failed',
                'failed_queue' => 'default',
            ]))
            ->assertOk()
            ->assertSee('QueueBusyAlertMail')
            ->assertDontSee('PaymentConfirmationMail');
    }

    public function test_super_admin_can_filter_failed_jobs_by_job_class(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob('emails', 'App\\Mail\\Customer\\PaymentConfirmationMail');
        $this->recordFailedJob('default', 'App\\Mail\\Admin\\QueueBusyAlertMail');

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', [
                'tab' => 'failed',
                'failed_job_class' => 'PaymentConfirmationMail',
            ]))
            ->assertOk()
            ->assertSee('PaymentConfirmationMail')
            ->assertDontSee('QueueBusyAlertMail');
    }

    public function test_super_admin_can_retry_a_failed_job(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob();
        $uuid = (string) DB::table('failed_jobs')->value('uuid');

        Artisan::shouldReceive('call')
            ->once()
            ->with('queue:retry', ['id' => [$uuid]])
            ->andReturnUsing(function () use ($uuid) {
                DB::table('failed_jobs')->where('uuid', $uuid)->delete();

                return 0;
            });

        $this->actingAsAdmin($admin)
            ->post(route('admin.system.failed-jobs.retry', $uuid))
            ->assertRedirect(route('admin.system.queued-jobs.index', ['tab' => 'failed']))
            ->assertSessionHas('success', 'Failed job queued for retry.');

        $this->assertDatabaseMissing('failed_jobs', ['uuid' => $uuid]);
    }

    public function test_retry_preserves_failed_job_filters(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob();
        $uuid = (string) DB::table('failed_jobs')->value('uuid');
        $filters = [
            'tab' => 'failed',
            'failed_queue' => 'emails',
            'failed_job_class' => 'PaymentConfirmationMail',
        ];

        Artisan::shouldReceive('call')
            ->once()
            ->with('queue:retry', ['id' => [$uuid]])
            ->andReturnUsing(function () use ($uuid) {
                DB::table('failed_jobs')->where('uuid', $uuid)->delete();

                return 0;
            });

        $this->actingAsAdmin($admin)
            ->post(route('admin.system.failed-jobs.retry', ['uuid' => $uuid] + $filters))
            ->assertRedirect(route('admin.system.queued-jobs.index', $filters))
            ->assertSessionHas('success', 'Failed job queued for retry.');
    }

    public function test_super_admin_can_bulk_retry_failed_jobs(): void
    {
        $admin = $this->createSuperAdmin();
        $firstUuid = $this->recordFailedJob('emails', 'App\\Jobs\\FirstJob');
        $secondUuid = $this->recordFailedJob('default', 'App\\Jobs\\SecondJob');

        Artisan::shouldReceive('call')
            ->once()
            ->with('queue:retry', ['id' => [$firstUuid, $secondUuid]])
            ->andReturnUsing(function () use ($firstUuid, $secondUuid) {
                DB::table('failed_jobs')->whereIn('uuid', [$firstUuid, $secondUuid])->delete();

                return 0;
            });

        $filters = [
            'tab' => 'failed',
            'failed_queue' => 'emails',
            'failed_job_class' => 'PaymentConfirmationMail',
        ];

        $this->actingAsAdmin($admin)
            ->post(route('admin.system.failed-jobs.bulk-retry', $filters), ['ids' => [$firstUuid, $secondUuid]])
            ->assertRedirect(route('admin.system.queued-jobs.index', $filters))
            ->assertSessionHas('success', '2 failed jobs queued for retry.');

        $this->assertDatabaseMissing('failed_jobs', ['uuid' => $firstUuid]);
        $this->assertDatabaseMissing('failed_jobs', ['uuid' => $secondUuid]);
    }

    public function test_bulk_retry_reports_partial_failure(): void
    {
        $admin = $this->createSuperAdmin();
        $firstUuid = $this->recordFailedJob('emails', 'App\\Jobs\\FirstJob');
        $secondUuid = $this->recordFailedJob('default', 'App\\Jobs\\SecondJob');

        Artisan::shouldReceive('call')
            ->once()
            ->with('queue:retry', ['id' => [$firstUuid, $secondUuid]])
            ->andReturnUsing(function () use ($firstUuid) {
                DB::table('failed_jobs')->where('uuid', $firstUuid)->delete();

                return 0;
            });

        $this->actingAsAdmin($admin)
            ->post(route('admin.system.failed-jobs.bulk-retry'), ['ids' => [$firstUuid, $secondUuid]])
            ->assertRedirect(route('admin.system.queued-jobs.index', ['tab' => 'failed']))
            ->assertSessionHas('warning', '1 failed job queued for retry; 1 could not be queued.');

        $this->assertDatabaseMissing('failed_jobs', ['uuid' => $firstUuid]);
        $this->assertDatabaseHas('failed_jobs', ['uuid' => $secondUuid]);
    }

    public function test_bulk_retry_requires_at_least_one_selection(): void
    {
        $admin = $this->createSuperAdmin();

        $this->actingAsAdmin($admin)
            ->post(route('admin.system.failed-jobs.bulk-retry'), ['ids' => []])
            ->assertSessionHasErrors('ids');
    }

    public function test_non_super_admin_cannot_bulk_retry_failed_jobs(): void
    {
        $admin = $this->createAdminWithPermissions(['admin.view']);
        $uuid = $this->recordFailedJob();

        $this->actingAsAdmin($admin)
            ->post(route('admin.system.failed-jobs.bulk-retry'), ['ids' => [$uuid]])
            ->assertForbidden();

        $this->assertDatabaseHas('failed_jobs', ['uuid' => $uuid]);
    }

    public function test_retry_failure_keeps_the_failed_job(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordFailedJob();
        $uuid = (string) DB::table('failed_jobs')->value('uuid');

        Artisan::shouldReceive('call')
            ->once()
            ->andThrow(new RuntimeException('Queue unavailable.'));

        $this->actingAsAdmin($admin)
            ->post(route('admin.system.failed-jobs.retry', $uuid))
            ->assertRedirect(route('admin.system.queued-jobs.index', ['tab' => 'failed']))
            ->assertSessionHas('error', 'The failed job could not be queued for retry.');

        $this->assertDatabaseHas('failed_jobs', ['uuid' => $uuid]);
    }

    public function test_non_super_admin_cannot_retry_a_failed_job(): void
    {
        $admin = $this->createAdminWithPermissions(['admin.view']);
        $this->recordFailedJob();
        $uuid = (string) DB::table('failed_jobs')->value('uuid');

        $this->actingAsAdmin($admin)
            ->post(route('admin.system.failed-jobs.retry', $uuid))
            ->assertForbidden();

        $this->assertDatabaseHas('failed_jobs', ['uuid' => $uuid]);
    }

    public function test_non_super_admin_cannot_view_failed_jobs(): void
    {
        $admin = $this->createAdminWithPermissions(['admin.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.failed-jobs.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_filter_queued_jobs_by_queue(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordQueuedJob('default', 'App\\Mail\\Customer\\OrderConfirmationMail');
        $this->recordQueuedJob('emails', 'App\\Mail\\Admin\\QueueBusyAlertMail');

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', ['queue' => 'emails']))
            ->assertOk()
            ->assertSee('QueueBusyAlertMail')
            ->assertDontSee('OrderConfirmationMail');
    }

    public function test_super_admin_can_filter_queued_jobs_by_job_class(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordQueuedJob('default', 'App\\Mail\\Customer\\OrderConfirmationMail');
        $this->recordQueuedJob('emails', 'App\\Mail\\Admin\\QueueBusyAlertMail');

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', ['job_class' => 'OrderConfirmationMail']))
            ->assertOk()
            ->assertSee('OrderConfirmationMail')
            ->assertDontSee('QueueBusyAlertMail');
    }

    public function test_super_admin_can_filter_queued_jobs_by_status(): void
    {
        $admin = $this->createSuperAdmin();
        $this->recordQueuedJob('default', 'App\\Jobs\\PendingJob', availableAt: now()->timestamp - 60);
        $this->recordQueuedJob('default', 'App\\Jobs\\DelayedJob', availableAt: now()->timestamp + 3600);
        $this->recordQueuedJob(
            'default',
            'App\\Jobs\\ReservedJob',
            reservedAt: now()->timestamp,
            availableAt: now()->timestamp,
        );

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('PendingJob')
            ->assertDontSee('DelayedJob')
            ->assertDontSee('ReservedJob');

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', ['status' => 'delayed']))
            ->assertOk()
            ->assertSee('DelayedJob')
            ->assertDontSee('PendingJob')
            ->assertDontSee('ReservedJob');

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index', ['status' => 'reserved']))
            ->assertOk()
            ->assertSee('ReservedJob')
            ->assertDontSee('PendingJob')
            ->assertDontSee('DelayedJob');
    }

    public function test_non_super_admin_cannot_view_queued_jobs(): void
    {
        $admin = $this->createAdminWithPermissions(['admin.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.queued-jobs.index'))
            ->assertForbidden();
    }

    public function test_queued_jobs_menu_is_only_visible_to_super_admin(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $otherAdmin = $this->createAdminWithPermissions(['admin.view']);

        $this->actingAsAdmin($superAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.system.queued-jobs.index'), false);

        $this->actingAsAdmin($otherAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee(route('admin.system.queued-jobs.index'), false);
    }

    public function test_guest_is_redirected_from_queued_jobs(): void
    {
        $this->get(route('admin.system.queued-jobs.index'))
            ->assertRedirect(route('admin.login.view'));
    }

    public function test_guest_is_redirected_from_failed_jobs(): void
    {
        $this->get(route('admin.system.failed-jobs.index'))
            ->assertRedirect(route('admin.login.view'));
    }

    public function test_guest_is_redirected_when_retrying_a_failed_job(): void
    {
        $this->recordFailedJob();
        $uuid = (string) DB::table('failed_jobs')->value('uuid');

        $this->post(route('admin.system.failed-jobs.retry', $uuid))
            ->assertRedirect(route('admin.login.view'));
    }

    private function recordFailedJob(
        string $queue = 'emails',
        string $jobClass = 'App\\Mail\\Customer\\PaymentConfirmationMail',
    ): string {
        $uuid = (string) str()->uuid();

        DB::table('failed_jobs')->insert([
            'uuid' => $uuid,
            'connection' => 'database',
            'queue' => $queue,
            'payload' => json_encode([
                'displayName' => $jobClass,
                'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
            ]),
            'exception' => "RuntimeException: SMTP connection failed\nStack trace: #0 {main}",
            'failed_at' => now(),
        ]);

        return $uuid;
    }

    private function recordQueuedJob(
        string $queue = 'default',
        string $jobClass = 'App\\Mail\\Customer\\OrderConfirmationMail',
        ?int $reservedAt = null,
        ?int $availableAt = null,
    ): void {
        DB::table('jobs')->insert([
            'queue' => $queue,
            'payload' => json_encode([
                'displayName' => $jobClass,
                'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
            ]),
            'attempts' => 0,
            'reserved_at' => $reservedAt,
            'available_at' => $availableAt ?? now()->timestamp,
            'created_at' => now()->timestamp,
        ]);
    }
}
