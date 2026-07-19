<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class BrandModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    public function test_admin_can_view_brand_index(): void
    {
        $admin = $this->createAdminWithPermissions(['brand.view']);
        Brand::factory()->count(2)->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.brand.index'))
            ->assertOk();
    }

    public function test_admin_can_create_brand(): void
    {
        $admin = $this->createAdminWithPermissions(['brand.view', 'brand.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.brand.store'), [
                'name'      => 'Nike',
                'is_active' => 'active',
                'sort_order' => 1,
            ])
            ->assertRedirect(route('admin.ecommerce.brand.index'));

        $this->assertDatabaseHas('brands', ['name' => 'Nike']);
    }

    public function test_duplicate_brand_name_fails_validation(): void
    {
        $admin = $this->createAdminWithPermissions(['brand.view', 'brand.create']);
        Brand::factory()->create(['name' => 'Nike']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.brand.store'), ['name' => 'Nike', 'is_active' => 'active'])
            ->assertSessionHasErrors('name');
    }

    public function test_brand_with_products_cannot_be_deleted(): void
    {
        $admin = $this->createAdminWithPermissions(['brand.view', 'brand.delete']);
        $brand = Brand::factory()->create();
        Product::factory()->create(['brand_id' => $brand->id]);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.brand.destroy', $brand))
            ->assertRedirect(route('admin.ecommerce.brand.index'));

        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'deleted_at' => null]);
    }

    public function test_admin_can_toggle_brand_status(): void
    {
        $admin = $this->createAdminWithPermissions(['brand.view', 'brand.edit']);
        $brand = Brand::factory()->create(['is_active' => true]);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.brand.toggle-status', $brand))
            ->assertRedirect(route('admin.ecommerce.brand.index'));

        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'is_active' => false]);
    }

    public function test_admin_can_bulk_delete_brands(): void
    {
        $admin  = $this->createAdminWithPermissions(['brand.view', 'brand.delete']);
        $brands = Brand::factory()->count(3)->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.brand.bulk-destroy'), [
                'ids' => $brands->pluck('id')->implode(','),
            ])
            ->assertRedirect(route('admin.ecommerce.brand.index'));

        foreach ($brands as $brand) {
            $this->assertSoftDeleted('brands', ['id' => $brand->id]);
        }
    }
}