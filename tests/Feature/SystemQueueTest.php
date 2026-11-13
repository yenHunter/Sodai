<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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
            ->assertSee('default')
            ->assertSee('OrderConfirmationMail')
            ->assertSee('Pending');
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
