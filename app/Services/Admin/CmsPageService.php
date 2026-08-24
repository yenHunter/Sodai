<?php

namespace App\Services\Admin;

use App\Models\CmsPage;
use Illuminate\Support\Facades\Auth;

class CmsPageService
{
    public function findOrCreate(string $slug): CmsPage
    {
        return CmsPage::findOrCreateBySlug($slug);
    }

    public function update(CmsPage $page, array $data): CmsPage
    {
        $page->update([
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'updated_by' => Auth::guard('admin')->id(),
        ]);

        return $page->fresh();
    }

    public function getAllPages()
    {
        foreach (CmsPage::SLUGS as $slug) {
            CmsPage::findOrCreateBySlug($slug);
        }

        return CmsPage::whereIn('slug', CmsPage::SLUGS)
            ->get()
            ->sortBy(fn ($page) => array_search($page->slug, CmsPage::SLUGS))
            ->values();
    }
}
