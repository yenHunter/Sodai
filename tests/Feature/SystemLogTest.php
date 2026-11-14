<?php

namespace Tests\Feature;

use App\Support\LaravelLogReader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class SystemLogTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    private string $logDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->logDirectory = storage_path('framework/testing/system-logs-'.Str::uuid());
        File::ensureDirectoryExists($this->logDirectory);
        config(['system_logs.directory' => $this->logDirectory]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->logDirectory);

        parent::tearDown();
    }

    public function test_super_admin_can_view_laravel_system_logs(): void
    {
        $admin = $this->createSuperAdmin();
        $this->writeLog($this->logContents());

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.logs.index', ['file' => 'laravel-2026-11-14.log']))
            ->assertOk()
            ->assertSee('Laravel System Logs')
            ->assertSee('laravel-2026-11-14.log')
            ->assertSee('Order confirmation email queued.')
            ->assertSee('Payment gateway timeout')
            ->assertSee('local')
            ->assertSee('ERROR')
            ->assertSee('INFO')
            ->assertSee('View details')
            ->assertSee('[stacktrace]')
            ->assertSee('log-details-0', false);
    }

    public function test_log_reader_strips_json_context_from_the_display_message(): void
    {
        $this->writeLog($this->logContents());

        $entries = (new LaravelLogReader($this->logDirectory))
            ->read('laravel-2026-11-14.log')['entries'];

        $this->assertSame('Order confirmation email queued.', $entries[0]['message']);
        $this->assertSame('Payment gateway timeout', $entries[1]['message']);
        $this->assertStringContainsString('{"order_id":42}', $entries[0]['raw']);
    }

    public function test_super_admin_can_filter_logs_by_level_and_search(): void
    {
        $admin = $this->createSuperAdmin();
        $this->writeLog($this->logContents());

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.logs.index', [
                'file' => 'laravel-2026-11-14.log',
                'level' => 'error',
            ]))
            ->assertOk()
            ->assertSee('Payment gateway timeout')
            ->assertDontSee('Order confirmation email queued.');

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.logs.index', [
                'file' => 'laravel-2026-11-14.log',
                'q' => 'gateway',
            ]))
            ->assertOk()
            ->assertSee('Payment gateway timeout')
            ->assertDontSee('Order confirmation email queued.');
    }

    public function test_invalid_log_file_selection_falls_back_to_the_newest_file(): void
    {
        $admin = $this->createSuperAdmin();
        $this->writeLog($this->logContents());

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.logs.index', ['file' => '../../.env']))
            ->assertOk()
            ->assertSee('laravel-2026-11-14.log')
            ->assertDontSee('APP_KEY');
    }

    public function test_system_logs_page_handles_missing_log_files(): void
    {
        $admin = $this->createSuperAdmin();
        File::deleteDirectory($this->logDirectory);

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.logs.index'))
            ->assertOk()
            ->assertSee('No Laravel log files found.');
    }

    public function test_non_super_admin_cannot_view_system_logs(): void
    {
        $admin = $this->createAdminWithPermissions(['admin.view']);
        $this->writeLog($this->logContents());

        $this->actingAsAdmin($admin)
            ->get(route('admin.system.logs.index'))
            ->assertForbidden();
    }

    public function test_log_menu_is_only_visible_to_super_admin_and_follows_jobs(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $otherAdmin = $this->createAdminWithPermissions(['admin.view']);

        $this->actingAsAdmin($superAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.system.logs.index'), false)
            ->assertSeeInOrder([
                route('admin.system.queued-jobs.index'),
                route('admin.system.logs.index'),
            ], false);

        $this->actingAsAdmin($otherAdmin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee(route('admin.system.logs.index'), false);
    }

    private function writeLog(string $contents): void
    {
        File::put($this->logDirectory.'/laravel-2026-11-14.log', $contents);
    }

    private function logContents(): string
    {
        return implode("\n", [
            '[2026-11-14 10:00:00] local.INFO: Order confirmation email queued. {"order_id":42}',
            '[2026-11-14 10:01:00] local.ERROR: Payment gateway timeout {"exception":"[object] (RuntimeException(code: 0): Payment gateway timeout at C:/app/Payment.php:12)"}',
            '[stacktrace]',
            '#0 C:/app/Payment.php(12): App\\Payment->charge()',
            '#1 {main}',
            '',
        ]);
    }
}
