<?php

namespace App\Services\Admin;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FaqService
{
    // ─────────────────────────────────────────────
    // CATEGORY — CREATE / UPDATE / DELETE
    // ─────────────────────────────────────────────

    public function storeCategory(array $data): FaqCategory
    {
        return DB::transaction(function () use ($data) {
            return FaqCategory::create([
                'name' => $data['name'],
                'slug' => $this->generateUniqueCategorySlug($data['name']),
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
            ]);
        });
    }

    public function updateCategory(FaqCategory $category, array $data): FaqCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $slug = $category->slug;
            if ($category->name !== $data['name']) {
                $slug = $this->generateUniqueCategorySlug($data['name'], $category->id);
            }

            $category->update([
                'name' => $data['name'],
                'slug' => $slug,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
            ]);

            return $category->fresh();
        });
    }

    /**
     * Blocks deletion if the category still has FAQs — mirrors the
     * canDelete() guard pattern used on Product/Category models, so an
     * admin can't silently orphan or cascade-delete FAQ content.
     */
    public function deleteCategory(FaqCategory $category): bool
    {
        $faqCount = $category->faqs()->count();

        if ($faqCount > 0) {
            throw new \Exception(
                "Cannot delete \"{$category->name}\": it still has {$faqCount} FAQ(s). Move or delete them first."
            );
        }

        return $category->delete();
    }

    public function toggleCategoryStatus(FaqCategory $category): FaqCategory
    {
        $category->update(['is_active' => ! $category->is_active]);

        return $category->fresh();
    }

    // ─────────────────────────────────────────────
    // FAQ — CREATE / UPDATE / DELETE
    // ─────────────────────────────────────────────

    public function storeFaq(array $data): Faq
    {
        return DB::transaction(function () use ($data) {
            return Faq::create([
                'faq_category_id' => $data['faq_category_id'],
                'question' => $data['question'],
                'answer' => $data['answer'],
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
            ]);
        });
    }

    public function updateFaq(Faq $faq, array $data): Faq
    {
        return DB::transaction(function () use ($faq, $data) {
            $faq->update([
                'faq_category_id' => $data['faq_category_id'],
                'question' => $data['question'],
                'answer' => $data['answer'],
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
            ]);

            return $faq->fresh();
        });
    }

    public function deleteFaq(Faq $faq): bool
    {
        return $faq->delete();
    }

    public function bulkDeleteFaqs(array $faqIds): int
    {
        $faqs = Faq::whereIn('id', $faqIds)->get();
        $count = 0;

        foreach ($faqs as $faq) {
            $faq->delete();
            $count++;
        }

        return $count;
    }

    public function toggleFaqStatus(Faq $faq): Faq
    {
        $faq->update(['is_active' => ! $faq->is_active]);

        return $faq->fresh();
    }

    // ─────────────────────────────────────────────
    // RESOLVE IS_ACTIVE
    // ─────────────────────────────────────────────

    private function resolveIsActive(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1;
        }
        if (is_string($value)) {
            return strtolower($value) === 'active';
        }

        return false;
    }

    // ─────────────────────────────────────────────
    // SLUG
    // ─────────────────────────────────────────────

    private function generateUniqueCategorySlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (true) {
            $query = FaqCategory::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if (! $query->exists()) {
                break;
            }
            $slug = $original.'-'.$count++;
        }

        return $slug;
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getCategoriesWithFaqs()
    {
        return FaqCategory::withCount('faqs')
            ->with(['faqs' => fn ($q) => $q->ordered()])
            ->ordered()
            ->get();
    }

    public function getActiveCategoriesForSelect()
    {
        return FaqCategory::active()->ordered()->get();
    }
}
