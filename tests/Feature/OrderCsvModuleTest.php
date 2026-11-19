<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Admin\OrderCsvService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class OrderCsvModuleTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    public function test_admin_can_export_orders_as_csv(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view']);
        $order = Order::factory()->create([
            'order_number' => 'ORD-EXPORT-1',
            'shipping_email' => 'buyer@example.com',
            'total_amount' => 250.75,
        ]);
        OrderItem::factory()->create(['order_id' => $order->id, 'quantity' => 2]);

        $response = $this->actingAsAdmin($admin)->get(route('admin.ecommerce.order.export'));

        $response->assertOk()->assertDownload();

        $csv = $response->streamedContent();

        $this->assertStringContainsString(implode(',', OrderCsvService::COLUMNS), $csv);
        $this->assertStringContainsString('ORD-EXPORT-1', $csv);
        $this->assertStringContainsString('buyer@example.com', $csv);
        $this->assertStringContainsString('250.75', $csv);
        $this->assertStringContainsString('x2', $csv);
    }

    public function test_export_honours_status_search_and_date_filters(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view']);

        $pending = Order::factory()->withStatus('pending')->create(['order_number' => 'ORD-FILTER-PENDING']);
        $shipped = Order::factory()->withStatus('shipped')->create(['order_number' => 'ORD-FILTER-SHIPPED']);
        $old = Order::factory()->withStatus('pending')->create(['order_number' => 'ORD-FILTER-OLD']);
        $old->forceFill(['created_at' => now()->subDays(10)])->save();

        $csv = $this->exportCsv($admin, ['status' => 'shipped']);
        $this->assertStringContainsString('ORD-FILTER-SHIPPED', $csv);
        $this->assertStringNotContainsString('ORD-FILTER-PENDING', $csv);
        $this->assertStringNotContainsString('ORD-FILTER-OLD', $csv);

        $csv = $this->exportCsv($admin, ['search' => 'ORDER-NOPE']);
        $this->assertStringNotContainsString('ORD-FILTER-SHIPPED', $csv);
        $this->assertStringNotContainsString('ORD-FILTER-PENDING', $csv);

        $csv = $this->exportCsv($admin, ['search' => 'SHIPPED']);
        $this->assertStringContainsString('ORD-FILTER-SHIPPED', $csv);
        $this->assertStringNotContainsString('ORD-FILTER-PENDING', $csv);

        $csv = $this->exportCsv($admin, ['date_from' => now()->subDays(2)->toDateString()]);
        $this->assertStringContainsString('ORD-FILTER-PENDING', $csv);
        $this->assertStringContainsString('ORD-FILTER-SHIPPED', $csv);
        $this->assertStringNotContainsString('ORD-FILTER-OLD', $csv);

        $csv = $this->exportCsv($admin, ['date_to' => now()->subDays(5)->toDateString()]);
        $this->assertStringContainsString('ORD-FILTER-OLD', $csv);
        $this->assertStringNotContainsString('ORD-FILTER-PENDING', $csv);
    }

    public function test_orders_index_export_link_keeps_the_current_filters(): void
    {
        $admin = $this->createAdminWithPermissions(['order.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.order.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee(route('admin.ecommerce.order.export', ['status' => 'pending']), false);
    }

    public function test_admin_without_order_view_permission_cannot_export(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.order.export'))
            ->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function exportCsv($admin, array $filters = []): string
    {
        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.order.export', $filters));

        $response->assertOk();

        return $response->streamedContent();
    }
}
