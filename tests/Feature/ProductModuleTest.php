<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_product(): void
    {
        $admin = Admin::create([
            'name'     => 'Test Admin',
            'email'    => 'admin@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'admin']);
        $role->givePermissionTo([
            Permission::firstOrCreate(['name' => 'product.view', 'guard_name' => 'admin']),
            Permission::firstOrCreate(['name' => 'product.create', 'guard_name' => 'admin']),
            Permission::firstOrCreate(['name' => 'product.edit', 'guard_name' => 'admin']),
            Permission::firstOrCreate(['name' => 'product.delete', 'guard_name' => 'admin']),
        ]);
        $admin->assignRole($role);

        $category = Category::create([
            'name'        => 'Electronics',
            'slug'        => 'electronics',
            'description' => 'Test category',
            'parent_id'   => null,
            'is_active'   => true,
            'sort_order'  => 1,
        ]);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.ecommerce.product.store'), [
            'name' => 'Test Product',
            'sku' => 'SKU-001',
            'category_id' => $category->id,
            'price' => '199.99',
            'sale_price' => '179.99',
            'stock_quantity' => 10,
            'low_stock_threshold' => 3,
            'is_active' => 'active',
            'is_featured' => 'inactive',
            'short_description' => 'A test product',
            'description' => 'Detailed description',
            'tags' => 'test, gadget',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.ecommerce.product.index'));
        $this->assertDatabaseHas('products', ['name' => 'Test Product', 'sku' => 'SKU-001']);
        $this->assertDatabaseHas('tags', ['slug' => 'test']);
        $this->assertDatabaseHas('product_tag', ['product_id' => Product::first()->id]);
    }
}
