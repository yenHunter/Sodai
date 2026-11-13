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

    private function recordQueuedJob(): void
    {
        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => json_encode([
                'displayName' => 'App\\Mail\\Customer\\OrderConfirmationMail',
                'job' => 'Illuminate\\Queue\\CallQueuedHandler@call',
            ]),
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp,
        ]);
    }
}
