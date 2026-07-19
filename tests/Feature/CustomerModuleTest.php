<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\Admin\CustomerSetPasswordMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class CustomerModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    public function test_admin_can_create_customer_with_temporary_password_and_email_sent(): void
    {
        Mail::fake();
        $admin = $this->createAdminWithPermissions(['customer.view', 'customer.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.customer.store'), [
                'name'   => 'Jane Customer',
                'email'  => 'jane@customer.com',
                'status' => 'active',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'jane@customer.com']);
        Mail::assertSent(CustomerSetPasswordMail::class);

        $customer = User::where('email', 'jane@customer.com')->first();
        $this->assertNotEmpty($customer->password);
    }

    public function test_customer_with_orders_cannot_be_deleted(): void
    {
        $admin    = $this->createAdminWithPermissions(['customer.view', 'customer.delete']);
        $customer = User::factory()->create();
        Order::factory()->create(['user_id' => $customer->id]);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.customer.destroy', $customer))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'deleted_at' => null]);
    }

    public function test_admin_can_delete_customer_without_orders(): void
    {
        $admin    = $this->createAdminWithPermissions(['customer.view', 'customer.delete']);
        $customer = User::factory()->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.customer.destroy', $customer))
            ->assertRedirect();

        $this->assertSoftDeleted('users', ['id' => $customer->id]);
    }

    public function test_banned_customer_status_cannot_be_toggled(): void
    {
        $admin    = $this->createAdminWithPermissions(['customer.view', 'customer.edit']);
        $customer = User::factory()->create(['status' => 'banned']);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.customer.toggle-status', $customer))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $customer->id, 'status' => 'banned']);
    }

    public function test_resend_set_password_email(): void
    {
        Mail::fake();
        $admin    = $this->createAdminWithPermissions(['customer.view', 'customer.edit']);
        $customer = User::factory()->create();

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.customer.resend-set-password', $customer))
            ->assertRedirect();

        Mail::assertSent(CustomerSetPasswordMail::class);
    }

    public function test_customer_can_set_password_via_valid_token(): void
    {
        $customer = User::factory()->create(['email' => 'setpass@test.com']);

        $token = 'plain-token-value';
        DB::table('password_reset_tokens')->insert([
            'email'      => $customer->email,
            'token'      => bcrypt($token),
            'created_at' => now(),
        ]);

        $this->post(route('customer.set-password.attempt'), [
            'token'                 => $token,
            'email'                 => $customer->email,
            'password'              => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ])->assertRedirect(route('visitor.index'));

        $this->assertTrue(Hash::check('NewPass123', $customer->fresh()->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $customer->email]);
    }

    public function test_expired_set_password_token_is_rejected(): void
    {
        $customer = User::factory()->create(['email' => 'expired@test.com']);
        $token    = 'expired-token';

        DB::table('password_reset_tokens')->insert([
            'email'      => $customer->email,
            'token'      => bcrypt($token),
            'created_at' => now()->subHours(2), // beyond 60-minute expiry
        ]);

        $this->post(route('customer.set-password.attempt'), [
            'token'                 => $token,
            'email'                 => $customer->email,
            'password'              => 'NewPass123',
            'password_confirmation' => 'NewPass123',
        ])->assertSessionHasErrors('email');
    }
}