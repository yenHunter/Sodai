<?php

namespace Tests\Feature;

use App\Models\Coupon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class CouponModuleTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    public function test_admin_can_view_coupon_index(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view']);
        Coupon::factory()->count(2)->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.coupon.index'))
            ->assertOk();
    }

    public function test_admin_can_create_percentage_coupon(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.coupon.store'), [
                'code' => 'summer25',
                'type' => 'percentage',
                'value' => 25,
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.ecommerce.coupon.index'));

        $this->assertDatabaseHas('coupons', ['code' => 'SUMMER25', 'value' => 25]);
    }

    public function test_percentage_value_cannot_exceed_100(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.coupon.store'), [
                'code' => 'TOOMUCH',
                'type' => 'percentage',
                'value' => 150,
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('value');
    }

    public function test_duplicate_coupon_code_fails_validation(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.create']);
        Coupon::factory()->create(['code' => 'FIXED10']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.coupon.store'), [
                'code' => 'FIXED10',
                'type' => 'fixed',
                'value' => 10,
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('code');
    }

    public function test_expires_at_must_be_after_or_equal_starts_at(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.coupon.store'), [
                'code' => 'BADDATES',
                'type' => 'fixed',
                'value' => 10,
                'is_active' => 'active',
                'starts_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'expires_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors('expires_at');
    }

    public function test_admin_can_update_coupon(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.edit']);
        $coupon = Coupon::factory()->create(['value' => 10]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.coupon.update', $coupon), [
                'code' => $coupon->code,
                'type' => 'percentage',
                'value' => 15,
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.ecommerce.coupon.index'));

        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'value' => 15]);
    }

    public function test_admin_can_toggle_coupon_status(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.edit']);
        $coupon = Coupon::factory()->create(['is_active' => true]);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.coupon.toggle-status', $coupon))
            ->assertRedirect(route('admin.ecommerce.coupon.index'));

        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'is_active' => false]);
    }

    public function test_used_coupon_cannot_be_deleted(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.delete']);
        $coupon = Coupon::factory()->used(3)->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.coupon.destroy', $coupon))
            ->assertRedirect(route('admin.ecommerce.coupon.index'));

        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'deleted_at' => null]);
    }

    public function test_admin_can_delete_unused_coupon(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.delete']);
        $coupon = Coupon::factory()->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.coupon.destroy', $coupon))
            ->assertRedirect(route('admin.ecommerce.coupon.index'));

        $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
    }

    public function test_admin_can_bulk_delete_coupons(): void
    {
        $admin = $this->createAdminWithPermissions(['coupon.view', 'coupon.delete']);
        $coupons = Coupon::factory()->count(3)->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.coupon.bulk-destroy'), [
                'ids' => $coupons->pluck('id')->implode(','),
            ])
            ->assertRedirect(route('admin.ecommerce.coupon.index'));

        foreach ($coupons as $coupon) {
            $this->assertSoftDeleted('coupons', ['id' => $coupon->id]);
        }
    }

    public function test_unauthenticated_user_is_redirected_from_coupon_routes(): void
    {
        $this->get(route('admin.ecommerce.coupon.index'))
            ->assertRedirect(route('admin.login.view'));
    }
}
