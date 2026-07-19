<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Refund;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class RefundModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    public function test_admin_can_create_refund_within_order_total(): void
    {
        $admin = $this->createAdminWithPermissions(['refund.view', 'refund.create']);
        $order = Order::factory()->create(['total_amount' => 100]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.refund.store'), [
                'order_id' => $order->id,
                'amount'   => 50,
                'reason'   => 'Damaged item',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('refunds', ['order_id' => $order->id, 'amount' => 50]);
    }

    public function test_refund_amount_cannot_exceed_order_total(): void
    {
        $admin = $this->createAdminWithPermissions(['refund.view', 'refund.create']);
        $order = Order::factory()->create(['total_amount' => 100]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.refund.store'), [
                'order_id' => $order->id,
                'amount'   => 150,
                'reason'   => 'Too much',
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_multiple_pending_refunds_cannot_collectively_exceed_order_total(): void
    {
        $admin = $this->createAdminWithPermissions(['refund.view', 'refund.create']);
        $order = Order::factory()->create(['total_amount' => 100]);

        Refund::factory()->create(['order_id' => $order->id, 'amount' => 70, 'status' => 'pending']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.refund.store'), [
                'order_id' => $order->id,
                'amount'   => 40, // 70 + 40 = 110 > 100
                'reason'   => 'Second refund attempt',
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_rejected_refunds_do_not_count_against_remaining_balance(): void
    {
        $admin = $this->createAdminWithPermissions(['refund.view', 'refund.create']);
        $order = Order::factory()->create(['total_amount' => 100]);

        Refund::factory()->create(['order_id' => $order->id, 'amount' => 70, 'status' => 'rejected']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.refund.store'), [
                'order_id' => $order->id,
                'amount'   => 80, // rejected refund shouldn't block this
                'reason'   => 'New refund',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('refunds', ['order_id' => $order->id, 'amount' => 80, 'status' => 'pending']);
    }

    public function test_approving_refund_marks_order_as_refunded(): void
    {
        $admin  = $this->createAdminWithPermissions(['refund.view', 'refund.approve']);
        $order  = Order::factory()->withStatus('delivered')->create();
        $refund = Refund::factory()->create(['order_id' => $order->id, 'status' => 'pending']);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.refund.approve', $refund))
            ->assertRedirect();

        $this->assertDatabaseHas('refunds', ['id' => $refund->id, 'status' => 'approved']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'refunded']);
    }

    public function test_rejecting_refund_does_not_change_order_status(): void
    {
        $admin  = $this->createAdminWithPermissions(['refund.view', 'refund.approve']);
        $order  = Order::factory()->withStatus('delivered')->create();
        $refund = Refund::factory()->create(['order_id' => $order->id, 'status' => 'pending']);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.refund.reject', $refund))
            ->assertRedirect();

        $this->assertDatabaseHas('refunds', ['id' => $refund->id, 'status' => 'rejected']);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'delivered']);
    }

    public function test_approved_refund_cannot_be_deleted(): void
    {
        $admin  = $this->createAdminWithPermissions(['refund.view', 'refund.delete']);
        $refund = Refund::factory()->create(['status' => 'approved']);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.refund.destroy', $refund))
            ->assertRedirect();

        $this->assertDatabaseHas('refunds', ['id' => $refund->id, 'deleted_at' => null]);
    }

    public function test_only_pending_refunds_can_be_edited(): void
    {
        $admin  = $this->createAdminWithPermissions(['refund.view', 'refund.edit']);
        $refund = Refund::factory()->create(['status' => 'approved']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.refund.update', $refund), [
                'order_id' => $refund->order_id,
                'amount'   => 10,
                'reason'   => 'Edited',
            ])
            ->assertForbidden();
    }
}