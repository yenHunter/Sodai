<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class ReviewModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    public function test_admin_can_view_review_index_with_stats(): void
    {
        $admin = $this->createAdminWithPermissions(['review.view']);
        Review::factory()->count(2)->create();

        $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.review.index'))
            ->assertOk()
            ->assertViewHas('stats');
    }

    public function test_approving_review_recalculates_product_rating(): void
    {
        $admin   = $this->createAdminWithPermissions(['review.view', 'review.approve']);
        $product = Product::factory()->create(['average_rating' => 0, 'review_count' => 0]);
        $review  = Review::factory()->create(['product_id' => $product->id, 'rating' => 5, 'status' => 'pending']);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.review.approve', $review))
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'status' => 'approved']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'average_rating' => 5, 'review_count' => 1]);
    }

    public function test_rejecting_review_excludes_it_from_product_rating(): void
    {
        $admin   = $this->createAdminWithPermissions(['review.view', 'review.approve']);
        $product = Product::factory()->create();
        Review::factory()->create(['product_id' => $product->id, 'rating' => 5, 'status' => 'approved']);
        $pendingReview = Review::factory()->create(['product_id' => $product->id, 'rating' => 1, 'status' => 'pending']);

        $this->actingAsAdmin($admin)
            ->patch(route('admin.ecommerce.review.reject', $pendingReview))
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'average_rating' => 5, 'review_count' => 1]);
    }

    public function test_deleting_review_recalculates_product_rating(): void
    {
        $admin   = $this->createAdminWithPermissions(['review.view', 'review.delete']);
        $product = Product::factory()->create();
        $review1 = Review::factory()->create(['product_id' => $product->id, 'rating' => 4, 'status' => 'approved']);
        Review::factory()->create(['product_id' => $product->id, 'rating' => 2, 'status' => 'approved']);

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.review.destroy', $review1))
            ->assertRedirect();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'average_rating' => 2, 'review_count' => 1]);
    }

    public function test_status_filter_returns_only_matching_reviews(): void
    {
        $admin = $this->createAdminWithPermissions(['review.view']);
        Review::factory()->create(['status' => 'pending']);
        Review::factory()->create(['status' => 'approved']);

        $response = $this->actingAsAdmin($admin)
            ->get(route('admin.ecommerce.review.index', ['status' => 'approved']));

        $response->assertViewHas('reviews', fn ($reviews) => $reviews->total() === 1);
    }

    public function test_admin_can_bulk_delete_reviews(): void
    {
        $admin   = $this->createAdminWithPermissions(['review.view', 'review.delete']);
        $reviews = Review::factory()->count(3)->create();

        $this->actingAsAdmin($admin)
            ->delete(route('admin.ecommerce.review.bulk-destroy'), [
                'ids' => $reviews->pluck('id')->implode(','),
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('reviews', 0);
    }
}