<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Attribute;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class AttributeModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    public function test_admin_can_view_attribute_list(): void
    {
        $admin = $this->createAdminWithPermissions(['attribute.view']);
        Attribute::factory()->create(['key' => 'color']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.attribute.index'))
            ->assertOk();
    }

    public function test_admin_can_update_attribute_label_and_status(): void
    {
        $admin     = $this->createAdminWithPermissions(['attribute.view', 'attribute.edit']);
        $attribute = Attribute::factory()->create(['key' => 'color', 'label' => 'Color']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.attribute.update', $attribute), [
                'label'  => 'Colour',
                'status' => 'inactive',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('attributes', [
            'id' => $attribute->id, 'label' => 'Colour', 'status' => 'inactive',
        ]);
    }

    public function test_toggling_status_updates_cached_active_keys(): void
    {
        $admin     = $this->createAdminWithPermissions(['attribute.view', 'attribute.edit']);
        $attribute = Attribute::factory()->create(['key' => 'size', 'status' => 'active']);

        $service = app(\App\Services\Admin\AttributeService::class);
        $this->assertContains('size', $service->getActiveKeys());

        $this->actingAsAdmin($admin)->patch(route('admin.ecommerce.attribute.toggle-status', $attribute));

        $this->assertNotContains('size', $service->getActiveKeys());
    }

    public function test_inactive_attribute_hides_field_on_product_form(): void
    {
        Attribute::factory()->create(['key' => 'weight', 'status' => 'inactive']);
        Attribute::factory()->create(['key' => 'color', 'status' => 'active']);
        Attribute::factory()->create(['key' => 'size', 'status' => 'active']);

        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);

        $response = $this->actingAsAdmin($admin)->get(route('admin.ecommerce.product.create'));

        $response->assertOk();
        $response->assertViewHas('activeAttrs', function ($attrs) {
            return in_array('color', $attrs) && !in_array('weight', $attrs);
        });
    }
}