<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class BannerModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_view_banner_index(): void
    {
        $admin = $this->createAdminWithPermissions(['banner.view']);
        Banner::factory()->count(2)->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.banner.index'))
            ->assertOk();
    }

    public function test_admin_can_create_banner_with_image(): void
    {
        $admin = $this->createAdminWithPermissions(['banner.view', 'banner.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.banner.store'), [
                'title'       => 'New Fashion Collection',
                'subtitle'    => 'Sale Offer',
                'button_text' => 'Order Now',
                'button_url'  => '/products',
                'position'    => 'home_slider',
                'is_active'   => 'active',
                'image'       => UploadedFile::fake()->image('slide.jpg'),
            ])
            ->assertRedirect(route('admin.ecommerce.banner.index'));

        $this->assertDatabaseHas('banners', ['title' => 'New Fashion Collection']);

        $banner = Banner::where('title', 'New Fashion Collection')->first();
        Storage::disk('public')->assertExists($banner->image);
    }

    public function test_banner_creation_requires_image(): void
    {
        $admin = $this->createAdminWithPermissions(['banner.view', 'banner.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.banner.store'), [
                'position'  => 'home_slider',
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('image');
    }

    public function test_banner_creation_requires_valid_position(): void
    {
        $admin = $this->createAdminWithPermissions(['banner.view', 'banner.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.banner.store'), [
                'position'  => 'invalid_position',
                'is_active' => 'active',
                'image'     => UploadedFile::fake()->image('slide.jpg'),
            ])
            ->assertSessionHasErrors('position');
    }

    public function test_expires_at_must_be_after_or_equal_starts_at(): void
    {
        $admin = $this->createAdminWithPermissions(['banner.view', 'banner.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.banner.store'), [
                'position'   => 'home_slider',
                'is_active'  => 'active',
                'image'      => UploadedFile::fake()->image('slide.jpg'),
                'starts_at'  => now()->addDays(5)->format('Y-m-d H:i:s'),
                'expires_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors('expires_at');
    }

    public function test_admin_can_update_banner_without_replacing_image(): void
    {
        $admin  = $this->createAdminWithPermissions(['banner.view', 'banner.edit']);
        $banner = Banner::factory()->create(['title' => 'Old Title', 'image' => 'banners/existing.jpg']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.banner.update', $banner), [
                'title'     => 'Updated Title',
                'position'  => 'home_slider',
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.ecommerce.banner.index'));

        $this->assertDatabaseHas('banners', [
            'id'    => $banner->id,
            'title' => 'Updated Title',
            'image' => 'banners/existing.jpg',
        ]);
    }

    public function test_admin_can_toggle_banner_status(): void
    {
        $admin  = $this->createAdminWithPermissions(['banner.view', 'banner.edit']);
        $banner = Banner::factory()->create(['is_active' => true]);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.banner.toggle-status', $banner))
            ->assertRedirect(route('admin.ecommerce.banner.index'));

        $this->assertDatabaseHas('banners', ['id' => $banner->id, 'is_active' => false]);
    }

    public function test_admin_can_delete_banner(): void
    {
        $admin  = $this->createAdminWithPermissions(['banner.view', 'banner.delete']);
        $banner = Banner::factory()->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.banner.destroy', $banner))
            ->assertRedirect(route('admin.ecommerce.banner.index'));

        $this->assertSoftDeleted('banners', ['id' => $banner->id]);
    }

    public function test_admin_can_bulk_delete_banners(): void
    {
        $admin   = $this->createAdminWithPermissions(['banner.view', 'banner.delete']);
        $banners = Banner::factory()->count(3)->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.banner.bulk-destroy'), [
                'ids' => $banners->pluck('id')->implode(','),
            ])
            ->assertRedirect(route('admin.ecommerce.banner.index'));

        foreach ($banners as $banner) {
            $this->assertSoftDeleted('banners', ['id' => $banner->id]);
        }
    }

    public function test_only_currently_valid_scope_excludes_expired_and_scheduled(): void
    {
        Banner::factory()->create(['is_active' => true, 'expires_at' => now()->subDay()]); // expired
        Banner::factory()->create(['is_active' => true, 'starts_at' => now()->addDay()]);  // scheduled
        Banner::factory()->create(['is_active' => true]); // valid

        $this->assertEquals(1, Banner::currentlyValid()->count());
    }

    public function test_unauthenticated_user_is_redirected_from_banner_routes(): void
    {
        $this->get(route('admin.ecommerce.banner.index'))
            ->assertRedirect(route('admin.login.view'));
    }
}