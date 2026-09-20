<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Offer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class OfferModuleTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_view_offer_index(): void
    {
        $admin = $this->createAdminWithPermissions(['offer.view']);
        Offer::factory()->count(2)->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.cms.offer.index'))
            ->assertOk();
    }

    public function test_admin_can_create_offer_with_image(): void
    {
        $admin = $this->createAdminWithPermissions(['offer.view', 'offer.create']);
        $category = Category::factory()->create();

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.offer.store'), [
                'title' => 'On Furniture',
                'subtitle' => 'Upto 40% off',
                'button_text' => 'Shop Now!',
                'category_id' => $category->id,
                'is_active' => 'active',
                'image' => UploadedFile::fake()->image('offer.jpg'),
            ])
            ->assertRedirect(route('admin.cms.offer.index'));

        $this->assertDatabaseHas('offers', ['title' => 'On Furniture', 'subtitle' => 'Upto 40% off']);

        $offer = Offer::where('title', 'On Furniture')->first();
        Storage::disk('public')->assertExists($offer->image);
    }

    public function test_offer_creation_requires_image(): void
    {
        $admin = $this->createAdminWithPermissions(['offer.view', 'offer.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.offer.store'), [
                'title' => 'No Image Offer',
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('image');
    }

    public function test_expires_at_must_be_after_or_equal_starts_at(): void
    {
        $admin = $this->createAdminWithPermissions(['offer.view', 'offer.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.offer.store'), [
                'is_active' => 'active',
                'image' => UploadedFile::fake()->image('offer.jpg'),
                'starts_at' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'expires_at' => now()->addDays(1)->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors('expires_at');
    }

    public function test_admin_can_update_offer_without_replacing_image(): void
    {
        $admin = $this->createAdminWithPermissions(['offer.view', 'offer.edit']);
        $offer = Offer::factory()->create(['title' => 'Old Title', 'image' => 'offers/existing.jpg']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.offer.update', $offer), [
                'title' => 'Updated Title',
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.cms.offer.index'));

        $this->assertDatabaseHas('offers', [
            'id' => $offer->id,
            'title' => 'Updated Title',
            'image' => 'offers/existing.jpg',
        ]);
    }

    public function test_admin_can_toggle_offer_status(): void
    {
        $admin = $this->createAdminWithPermissions(['offer.view', 'offer.edit']);
        $offer = Offer::factory()->create(['is_active' => true]);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.cms.offer.toggle-status', $offer))
            ->assertRedirect(route('admin.cms.offer.index'));

        $this->assertDatabaseHas('offers', ['id' => $offer->id, 'is_active' => false]);
    }

    public function test_admin_can_delete_offer(): void
    {
        $admin = $this->createAdminWithPermissions(['offer.view', 'offer.delete']);
        $offer = Offer::factory()->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.cms.offer.destroy', $offer))
            ->assertRedirect(route('admin.cms.offer.index'));

        $this->assertSoftDeleted('offers', ['id' => $offer->id]);
    }

    public function test_admin_can_bulk_delete_offers(): void
    {
        $admin = $this->createAdminWithPermissions(['offer.view', 'offer.delete']);
        $offers = Offer::factory()->count(3)->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.cms.offer.bulk-destroy'), [
                'ids' => $offers->pluck('id')->implode(','),
            ])
            ->assertRedirect(route('admin.cms.offer.index'));

        foreach ($offers as $offer) {
            $this->assertSoftDeleted('offers', ['id' => $offer->id]);
        }
    }

    public function test_button_url_override_wins_over_category_link(): void
    {
        $category = Category::factory()->create(['slug' => 'furniture']);
        $offer = Offer::factory()->create([
            'category_id' => $category->id,
            'button_url' => 'https://external.example.com/sale',
        ]);

        $this->assertEquals('https://external.example.com/sale', $offer->resolved_button_url);
    }

    public function test_only_currently_valid_scope_excludes_expired_and_scheduled(): void
    {
        Offer::factory()->create(['is_active' => true, 'expires_at' => now()->subDay()]);  // expired
        Offer::factory()->create(['is_active' => true, 'starts_at' => now()->addDay()]);   // scheduled
        Offer::factory()->create(['is_active' => true]);                                    // valid

        $this->assertEquals(1, Offer::currentlyValid()->count());
    }

    public function test_unauthenticated_user_is_redirected_from_offer_routes(): void
    {
        $this->get(route('admin.cms.offer.index'))
            ->assertRedirect(route('admin.login.view'));
    }
}
