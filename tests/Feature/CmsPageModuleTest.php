<?php

namespace Tests\Feature;

use App\Models\CmsPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class CmsPageModuleTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    public function test_admin_with_permission_can_view_cms_pages_index(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.cms.pages.index'))
            ->assertOk()
            ->assertViewIs('admin.cms.pages.index');
    }

    public function test_index_lists_all_four_canonical_pages_even_if_never_edited(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view']);

        $response = $this->actingAsAdmin($admin)->get(route('admin.cms.pages.index'));

        $response->assertViewHas('pages', function ($pages) {
            return $pages->count() === 4
                && $pages->pluck('slug')->diff(CmsPage::SLUGS)->isEmpty();
        });

        $this->assertDatabaseCount('cms_pages', 4);
    }

    public function test_admin_without_permission_cannot_view_cms_pages(): void
    {
        $admin = $this->createAdminWithPermissions([]);

        $this->actingAsAdmin($admin)
            ->get(route('admin.cms.pages.index'))
            ->assertForbidden();
    }

    public function test_edit_view_creates_page_on_first_visit_if_missing(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view']);

        $this->assertDatabaseMissing('cms_pages', ['slug' => 'privacy-policy']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.cms.pages.edit', 'privacy-policy'))
            ->assertOk()
            ->assertViewHas('page', fn ($page) => $page->slug === 'privacy-policy');

        $this->assertDatabaseHas('cms_pages', ['slug' => 'privacy-policy']);
    }

    public function test_edit_view_rejects_unknown_slug(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.cms.pages.edit', 'not-a-real-page'))
            ->assertNotFound();
    }

    public function test_admin_with_permission_can_update_page_content(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view', 'cms.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.pages.update', 'terms-conditions'), [
                'title' => 'Terms & Conditions',
                'content' => '<p>Updated terms content.</p>',
            ])
            ->assertRedirect(route('admin.cms.pages.edit', 'terms-conditions'));

        $this->assertDatabaseHas('cms_pages', [
            'slug' => 'terms-conditions',
            'content' => '<p>Updated terms content.</p>',
        ]);
    }

    public function test_update_records_which_admin_made_the_change(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view', 'cms.edit']);

        $this->actingAsAdmin($admin)->post(route('admin.cms.pages.update', 'shipping-policy'), [
            'title' => 'Shipping Policy',
            'content' => '<p>Ships within 3 days.</p>',
        ]);

        $page = CmsPage::where('slug', 'shipping-policy')->first();

        $this->assertEquals($admin->id, $page->updated_by);
    }

    public function test_admin_without_edit_permission_cannot_update_page(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view']); // view only, no edit

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.pages.update', 'return-refund-policy'), [
                'title' => 'Return & Refund Policy',
                'content' => '<p>Attempted update.</p>',
            ])
            ->assertForbidden();
    }

    public function test_update_requires_title(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view', 'cms.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.pages.update', 'privacy-policy'), [
                'content' => '<p>No title provided.</p>',
            ])
            ->assertSessionHasErrors('title');
    }

    public function test_update_rejects_unknown_slug(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view', 'cms.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.pages.update', 'not-a-real-page'), [
                'title' => 'Fake',
                'content' => '<p>x</p>',
            ])
            ->assertNotFound();
    }

    public function test_unauthenticated_user_is_redirected_from_cms_routes(): void
    {
        $this->get(route('admin.cms.pages.index'))
            ->assertRedirect(route('admin.login.view'));
    }
}
