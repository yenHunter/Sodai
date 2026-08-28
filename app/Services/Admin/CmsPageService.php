<?php

namespace App\Services\Admin;

use App\Models\CmsPage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsPageService
{
    public function findOrCreate(string $slug): CmsPage
    {
        return CmsPage::findOrCreateBySlug($slug);
    }

    public function update(CmsPage $page, array $data): CmsPage
    {
        $imagePath = $page->image;

        if (! empty($data['image']) && $data['image'] instanceof UploadedFile) {
            $this->deleteImage($page->image);
            $imagePath = $this->uploadImage($data['image']);
        } elseif (! empty($data['remove_image'])) {
            $this->deleteImage($page->image);
            $imagePath = null;
        }

        $page->update([
            'title' => $data['title'],
            'content' => $data['content'] ?? null,
            'image' => $imagePath,
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

    // ─────────────────────────────────────────────
    // IMAGE HANDLING
    // ─────────────────────────────────────────────

    private function uploadImage(UploadedFile $image): string
    {
        $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
        $path = $image->storeAs('cms-pages', $filename, 'public');

        if (! $path) {
            throw new \Exception('Failed to upload image.');
        }

        return $path;
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
