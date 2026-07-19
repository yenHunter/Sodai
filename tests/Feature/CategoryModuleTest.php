<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class CategoryModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    public function test_admin_with_permission_can_view_category_index(): void
    {
        $admin = $this->createAdminWithPermissions(['category.view']);
        Category::factory()->count(3)->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.category.index'))
            ->assertOk()
            ->assertViewIs('admin.ecommerce.category.index');
    }

    public function test_admin_without_permission_cannot_view_category_index(): void
    {
        $admin = $this->createAdminWithPermissions([]);

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.category.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = $this->createAdminWithPermissions(['category.view', 'category.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.category.store'), [
                'name'      => 'Electronics',
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.ecommerce.category.index'));

        $this->assertDatabaseHas('categories', ['name' => 'Electronics', 'slug' => 'electronics']);
    }

    public function test_category_creation_requires_unique_name(): void
    {
        $admin = $this->createAdminWithPermissions(['category.view', 'category.create']);
        Category::factory()->create(['name' => 'Electronics']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.category.store'), [
                'name'      => 'Electronics',
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_category(): void
    {
        $admin    = $this->createAdminWithPermissions(['category.view', 'category.edit']);
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.category.update', $category), [
                'name'      => 'New Name',
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.ecommerce.category.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    public function test_category_with_empty_child_categories_cascades_delete(): void
    {
        $admin  = $this->createAdminWithPermissions(['category.view', 'category.delete']);
        $parent = Category::factory()->create();
        $child  = Category::factory()->create(['parent_id' => $parent->id]);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.category.destroy', $parent))
            ->assertRedirect(route('admin.ecommerce.category.index'));

        $this->assertSoftDeleted('categories', ['id' => $parent->id]);
        $this->assertSoftDeleted('categories', ['id' => $child->id]);
    }

    public function test_category_cannot_be_deleted_if_child_has_products(): void
    {
        $admin  = $this->createAdminWithPermissions(['category.view', 'category.delete']);
        $parent = Category::factory()->create();
        $child  = Category::factory()->create(['parent_id' => $parent->id]);
        Product::factory()->create(['category_id' => $child->id]);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.category.destroy', $parent))
            ->assertRedirect(route('admin.ecommerce.category.index'));

        $this->assertDatabaseHas('categories', ['id' => $parent->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('categories', ['id' => $child->id, 'deleted_at' => null]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $admin    = $this->createAdminWithPermissions(['category.view', 'category.delete']);
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id]);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.category.destroy', $category))
            ->assertRedirect(route('admin.ecommerce.category.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'deleted_at' => null]);
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $admin    = $this->createAdminWithPermissions(['category.view', 'category.delete']);
        $category = Category::factory()->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.category.destroy', $category))
            ->assertRedirect(route('admin.ecommerce.category.index'));

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_unauthenticated_user_is_redirected_from_category_routes(): void
    {
        $this->get(route('admin.ecommerce.category.index'))
            ->assertRedirect(route('admin.login.view'));
    }
}
