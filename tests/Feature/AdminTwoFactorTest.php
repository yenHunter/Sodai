<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Services\Admin\TwoFactorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class AdminTwoFactorTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'admin_login',
            ], 200),
        ]);
    }

    public function test_admin_can_start_two_factor_setup(): void
    {
        $admin = $this->createAdminWithPermissions([]);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.two-factor.show'));

        $response->assertOk()
            ->assertViewHas('secret')
            ->assertViewHas('otpauthUrl')
            ->assertSee('Set up two-factor authentication');

        $this->assertNotEmpty(session('two_factor_setup_secret'));
    }

    public function test_admin_can_enable_two_factor_with_a_valid_code(): void
    {
        $admin = $this->createAdminWithPermissions([]);
        $this->actingAsAdmin($admin)->get(route('admin.two-factor.show'));

        $secret = session('two_factor_setup_secret');
        $code = app(TwoFactorService::class)->currentCode($secret);

        $this->actingAsAdmin($admin)
            ->post(route('admin.two-factor.enable'), ['code' => $code])
            ->assertRedirect(route('admin.two-factor.show'))
            ->assertSessionHas('recovery_codes');

        $admin->refresh();

        $this->assertTrue($admin->hasTwoFactorEnabled());
        $this->assertCount(8, $admin->two_factor_recovery_codes);
        $this->assertSame($secret, $admin->two_factor_secret);
    }

    public function test_invalid_code_does_not_enable_two_factor(): void
    {
        $admin = $this->createAdminWithPermissions([]);
        $this->actingAsAdmin($admin)->get(route('admin.two-factor.show'));

        $this->actingAsAdmin($admin)
            ->post(route('admin.two-factor.enable'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertFalse($admin->fresh()->hasTwoFactorEnabled());
    }

    public function test_login_with_two_factor_enabled_requires_the_challenge(): void
    {
        [$admin, $secret] = $this->enabledAdmin();

        $this->login($admin)->assertRedirect(route('admin.two-factor.challenge'));

        $this->assertGuest('admin');

        $this->get(route('admin.two-factor.challenge'))->assertOk();

        $this->post(route('admin.two-factor.verify'), [
            'code' => app(TwoFactorService::class)->currentCode($secret),
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_challenge_rejects_an_invalid_code(): void
    {
        [$admin] = $this->enabledAdmin();

        $this->login($admin)->assertRedirect(route('admin.two-factor.challenge'));

        $this->post(route('admin.two-factor.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertGuest('admin');
    }

    public function test_recovery_code_can_be_used_once(): void
    {
        [$admin] = $this->enabledAdmin();

        $this->login($admin)->assertRedirect(route('admin.two-factor.challenge'));

        $this->post(route('admin.two-factor.verify'), ['code' => 'AAAAA-BBBBB'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->assertSame(['CCCCC-DDDDD'], array_values($admin->fresh()->two_factor_recovery_codes));
    }

    public function test_challenge_requires_a_pending_login(): void
    {
        $this->get(route('admin.two-factor.challenge'))
            ->assertRedirect(route('admin.login.view'));
    }

    public function test_disabling_two_factor_requires_the_password(): void
    {
        [$admin] = $this->enabledAdmin();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.two-factor.disable'), ['password' => 'wrong-password'])
            ->assertSessionHasErrors('password');

        $this->assertTrue($admin->fresh()->hasTwoFactorEnabled());

        $this->actingAsAdmin($admin)
            ->delete(route('admin.two-factor.disable'), ['password' => 'password123'])
            ->assertRedirect(route('admin.two-factor.show'));

        $this->assertFalse($admin->fresh()->hasTwoFactorEnabled());
    }

    /**
     * @return array{0: Admin, 1: string}
     */
    private function enabledAdmin(): array
    {
        $secret = app(TwoFactorService::class)->generateSecret();

        $admin = Admin::factory()->create([
            'email' => 'mfa-admin@example.com',
            'password' => 'password123',
            'is_active' => true,
        ]);

        $admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => ['AAAAA-BBBBB', 'CCCCC-DDDDD'],
            'two_factor_confirmed_at' => now(),
        ])->save();

        return [$admin, $secret];
    }

    private function login(Admin $admin)
    {
        return $this->post(route('admin.login.attempt'), [
            'email' => $admin->email,
            'password' => 'password123',
            'g-recaptcha-response' => 'test-token',
        ]);
    }
}
