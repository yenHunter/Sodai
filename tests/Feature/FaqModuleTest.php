<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class FaqModuleTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    // ─────────────────────────────────────────────
    // INDEX / PERMISSIONS
    // ─────────────────────────────────────────────

    public function test_admin_with_permission_can_view_faq_index(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view']);
        FaqCategory::factory()->has(Faq::factory()->count(2))->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.cms.faq.index'))
            ->assertOk()
            ->assertViewIs('admin.cms.faq.index');
    }

    public function test_admin_without_permission_cannot_view_faq_index(): void
    {
        $admin = $this->createAdminWithPermissions([]);

        $this->actingAsAdmin($admin)
            ->get(route('admin.cms.faq.index'))
            ->assertForbidden();
    }

    public function test_unauthenticated_user_is_redirected_from_faq_routes(): void
    {
        $this->get(route('admin.cms.faq.index'))
            ->assertRedirect(route('admin.login.view'));
    }

    // ─────────────────────────────────────────────
    // CATEGORY: CREATE
    // ─────────────────────────────────────────────

    public function test_admin_can_create_faq_category(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.categories.store'), [
                'name' => 'Shipping',
                'sort_order' => 1,
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.cms.faq.index'));

        $this->assertDatabaseHas('faq_categories', ['name' => 'Shipping', 'slug' => 'shipping']);
    }

    public function test_faq_category_name_must_be_unique(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.create']);
        FaqCategory::factory()->create(['name' => 'Shipping']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.categories.store'), [
                'name' => 'Shipping',
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_admin_without_create_permission_cannot_create_category(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.categories.store'), [
                'name' => 'Payment',
                'is_active' => 'active',
            ])
            ->assertForbidden();
    }

    // ─────────────────────────────────────────────
    // CATEGORY: UPDATE
    // ─────────────────────────────────────────────

    public function test_admin_can_update_faq_category(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.edit']);
        $category = FaqCategory::factory()->create(['name' => 'Orders']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.categories.update', $category), [
                'name' => 'Order Management',
                'sort_order' => 2,
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.cms.faq.index'));

        $this->assertDatabaseHas('faq_categories', [
            'id' => $category->id,
            'name' => 'Order Management',
            'slug' => 'order-management',
        ]);
    }

    public function test_updating_category_name_uniqueness_ignores_itself(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.edit']);
        $category = FaqCategory::factory()->create(['name' => 'Payment']);

        // Submitting the same name back should NOT trigger a uniqueness error
        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.categories.update', $category), [
                'name' => 'Payment',
                'sort_order' => 0,
                'is_active' => 'active',
            ])
            ->assertSessionHasNoErrors();
    }

    // ─────────────────────────────────────────────
    // CATEGORY: DELETE (blocked when FAQs exist)
    // ─────────────────────────────────────────────

    public function test_admin_can_delete_empty_faq_category(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.delete']);
        $category = FaqCategory::factory()->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.cms.faq.categories.destroy', $category))
            ->assertRedirect(route('admin.cms.faq.index'));

        $this->assertSoftDeleted('faq_categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_existing_faqs(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.delete']);
        $category = FaqCategory::factory()->has(Faq::factory()->count(3))->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.cms.faq.categories.destroy', $category))
            ->assertRedirect(route('admin.cms.faq.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('faq_categories', ['id' => $category->id, 'deleted_at' => null]);
    }

    // ─────────────────────────────────────────────
    // CATEGORY: TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function test_admin_can_toggle_category_status(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.edit']);
        $category = FaqCategory::factory()->create(['is_active' => true]);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.cms.faq.categories.toggle-status', $category))
            ->assertRedirect(route('admin.cms.faq.index'));

        $this->assertDatabaseHas('faq_categories', ['id' => $category->id, 'is_active' => false]);
    }

    // ─────────────────────────────────────────────
    // FAQ: CREATE
    // ─────────────────────────────────────────────

    public function test_admin_can_create_faq_under_a_category(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.create']);
        $category = FaqCategory::factory()->create();

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.items.store'), [
                'faq_category_id' => $category->id,
                'question' => 'How long does shipping take?',
                'answer' => 'Typically 3-5 business days within the country.',
                'sort_order' => 0,
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.cms.faq.index'));

        $this->assertDatabaseHas('faqs', [
            'faq_category_id' => $category->id,
            'question' => 'How long does shipping take?',
        ]);
    }

    public function test_faq_creation_requires_a_valid_category(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.create']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.items.store'), [
                'faq_category_id' => 99999,
                'question' => 'Is this valid?',
                'answer' => 'No.',
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors('faq_category_id');
    }

    public function test_faq_creation_requires_question_and_answer(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.create']);
        $category = FaqCategory::factory()->create();

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.items.store'), [
                'faq_category_id' => $category->id,
                'is_active' => 'active',
            ])
            ->assertSessionHasErrors(['question', 'answer']);
    }

    // ─────────────────────────────────────────────
    // FAQ: UPDATE
    // ─────────────────────────────────────────────

    public function test_admin_can_update_faq_and_move_it_to_another_category(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.edit']);
        $originalCategory = FaqCategory::factory()->create();
        $newCategory = FaqCategory::factory()->create();
        $faq = Faq::factory()->create(['faq_category_id' => $originalCategory->id]);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.faq.items.update', $faq), [
                'faq_category_id' => $newCategory->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'sort_order' => 0,
                'is_active' => 'active',
            ])
            ->assertRedirect(route('admin.cms.faq.index'));

        $this->assertDatabaseHas('faqs', ['id' => $faq->id, 'faq_category_id' => $newCategory->id]);
    }

    // ─────────────────────────────────────────────
    // FAQ: DELETE (single + bulk)
    // ─────────────────────────────────────────────

    public function test_admin_can_delete_single_faq(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.delete']);
        $faq = Faq::factory()->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.cms.faq.items.destroy', $faq))
            ->assertRedirect(route('admin.cms.faq.index'));

        $this->assertSoftDeleted('faqs', ['id' => $faq->id]);
    }

    public function test_admin_can_bulk_delete_faqs(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.delete']);
        $faqs = Faq::factory()->count(3)->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.cms.faq.items.bulk-destroy'), [
                'ids' => $faqs->pluck('id')->implode(','),
            ])
            ->assertRedirect(route('admin.cms.faq.index'));

        foreach ($faqs as $faq) {
            $this->assertSoftDeleted('faqs', ['id' => $faq->id]);
        }
    }

    // ─────────────────────────────────────────────
    // FAQ: TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function test_admin_can_toggle_faq_status(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.edit']);
        $faq = Faq::factory()->create(['is_active' => true]);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.cms.faq.items.toggle-status', $faq))
            ->assertRedirect(route('admin.cms.faq.index'));

        $this->assertDatabaseHas('faqs', ['id' => $faq->id, 'is_active' => false]);
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIP INTEGRITY
    // ─────────────────────────────────────────────

    public function test_deleting_a_faq_does_not_affect_its_category(): void
    {
        $admin = $this->createAdminWithPermissions(['faq.view', 'faq.delete']);
        $category = FaqCategory::factory()->create();
        $faq = Faq::factory()->create(['faq_category_id' => $category->id]);

        $this->actingAsAdmin($admin)->delete(route('admin.cms.faq.items.destroy', $faq));

        $this->assertDatabaseHas('faq_categories', ['id' => $category->id, 'deleted_at' => null]);
    }

    public function test_active_faqs_only_scope_excludes_inactive_ones(): void
    {
        $category = FaqCategory::factory()->create();
        Faq::factory()->create(['faq_category_id' => $category->id, 'is_active' => true]);
        Faq::factory()->create(['faq_category_id' => $category->id, 'is_active' => false]);

        $this->assertEquals(1, $category->activeFaqs()->count());
        $this->assertEquals(2, $category->faqs()->count());
    }
}
