<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorFaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_renders_published_categories_and_answers(): void
    {
        $category = FaqCategory::factory()->create([
            'name' => 'Shipping & Delivery',
            'is_active' => true,
        ]);
        $faq = Faq::factory()->create([
            'faq_category_id' => $category->id,
            'question' => 'How long does delivery take?',
            'answer' => "Orders ship within 24 hours.\nDelivery takes 3-5 business days.",
            'is_active' => true,
        ]);

        $response = $this->get(route('visitor.faq'));

        $response->assertOk();
        $response->assertViewIs('visitor.pages.faq');
        $response->assertSee('Shipping &amp; Delivery', false);
        $response->assertSee($faq->question);
        $response->assertSee('Orders ship within 24 hours.');
        $response->assertSee('Delivery takes 3-5 business days.');
        // Breadcrumb now links to the real homepage route, not index.html
        $response->assertSee('href="'.route('visitor.index').'"', false);
        $response->assertDontSee('href="index.html"', false);
    }

    public function test_faq_page_hides_inactive_categories(): void
    {
        FaqCategory::factory()->create([
            'name' => 'Internal Notes',
            'is_active' => false,
        ])->faqs()->save(Faq::factory()->create([
            'question' => 'Draft question that should not be public?',
            'is_active' => true,
        ]));

        $response = $this->get(route('visitor.faq'));

        $response->assertOk();
        $response->assertDontSee('Internal Notes');
        $response->assertDontSee('Draft question that should not be public?');
    }

    public function test_faq_page_hides_inactive_answers_and_empty_categories(): void
    {
        $onlyInactiveFaqs = FaqCategory::factory()->create([
            'name' => 'Archived Category',
            'is_active' => true,
        ]);
        Faq::factory()->create([
            'faq_category_id' => $onlyInactiveFaqs->id,
            'question' => 'Archived question?',
            'is_active' => false,
        ]);

        FaqCategory::factory()->create([
            'name' => 'Empty Category',
            'is_active' => true,
        ]);

        $response = $this->get(route('visitor.faq'));

        $response->assertOk();
        $response->assertDontSee('Archived Category');
        $response->assertDontSee('Archived question?');
        $response->assertDontSee('Empty Category');
    }

    public function test_faq_page_orders_categories_and_questions_by_sort_order(): void
    {
        $second = FaqCategory::factory()->create([
            'name' => 'Second Category',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        $first = FaqCategory::factory()->create([
            'name' => 'First Category',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Faq::factory()->create([
            'faq_category_id' => $second->id,
            'question' => 'Third question?',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Faq::factory()->create([
            'faq_category_id' => $first->id,
            'question' => 'Second question?',
            'sort_order' => 2,
            'is_active' => true,
        ]);
        Faq::factory()->create([
            'faq_category_id' => $first->id,
            'question' => 'First question?',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get(route('visitor.faq'));

        $response->assertOk();
        $response->assertSeeInOrder([
            'First Category',
            'First question?',
            'Second question?',
            'Second Category',
            'Third question?',
        ]);
    }

    public function test_faq_page_escapes_answers_submitted_by_admins(): void
    {
        $category = FaqCategory::factory()->create(['is_active' => true]);
        Faq::factory()->create([
            'faq_category_id' => $category->id,
            'question' => 'Is markup allowed?',
            'answer' => '<script>alert("xss")</script>',
            'is_active' => true,
        ]);

        $response = $this->get(route('visitor.faq'));

        $response->assertOk();
        $response->assertDontSee('<script>alert("xss")</script>', false);
        $response->assertSee('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', false);
    }

    public function test_faq_page_shows_empty_state_when_nothing_is_published(): void
    {
        $response = $this->get(route('visitor.faq'));

        $response->assertOk();
        $response->assertSee('No FAQs are available right now.');
    }
}
