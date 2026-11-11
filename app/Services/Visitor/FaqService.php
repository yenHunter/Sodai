<?php

namespace App\Services\Visitor;

use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Collection;

class FaqService
{
    /**
     * Published FAQ categories for the public FAQ page: active categories that
     * still have at least one active FAQ, ordered, with only their active FAQs
     * eager-loaded so hidden content can never render.
     *
     * @return Collection<int, FaqCategory>
     */
    public function getPublishedCategories(): Collection
    {
        return FaqCategory::active()
            ->whereHas('faqs', fn ($query) => $query->where('is_active', true))
            ->with(['faqs' => fn ($query) => $query->active()->ordered()])
            ->ordered()
            ->get();
    }
}
