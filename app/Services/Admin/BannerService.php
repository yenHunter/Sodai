<?php

namespace App\Services\Admin;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerService
{
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Banner
    {
        return DB::transaction(function () use ($data) {

            $imagePath = $this->uploadImage($data['image']);

            $mobileImagePath = null;
            if (! empty($data['mobile_image'])) {
                $mobileImagePath = $this->uploadImage($data['mobile_image']);
            }

            return Banner::create([
                'title' => $data['title'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'description' => $data['description'] ?? null,
                'button_text' => $data['button_text'] ?? null,
                'button_url' => $data['button_url'] ?? null,
                'button_target' => $data['button_target'] ?? '_self',
                'image' => $imagePath,
                'mobile_image' => $mobileImagePath,
                'position' => $data['position'],
                'text_position' => $data['text_position'] ?? 'left',
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
                'sort_order' => $data['sort_order'] ?? 0,
                'starts_at' => $data['starts_at'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Banner $banner, array $data): Banner
    {
        return DB::transaction(function () use ($banner, $data) {

            $imagePath = $banner->image;
            if (! empty($data['image'])) {
                $this->deleteImage($banner->image);
                $imagePath = $this->uploadImage($data['image']);
            }

            $mobileImagePath = $banner->mobile_image;
            if (! empty($data['mobile_image'])) {
                $this->deleteImage($banner->mobile_image);
                $mobileImagePath = $this->uploadImage($data['mobile_image']);
            }

            $banner->update([
                'title' => $data['title'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'description' => $data['description'] ?? null,
                'button_text' => $data['button_text'] ?? null,
                'button_url' => $data['button_url'] ?? null,
                'button_target' => $data['button_target'] ?? '_self',
                'image' => $imagePath,
                'mobile_image' => $mobileImagePath,
                'position' => $data['position'],
                'text_position' => $data['text_position'] ?? 'left',
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
                'sort_order' => $data['sort_order'] ?? 0,
                'starts_at' => $data['starts_at'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            return $banner->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Banner $banner): bool
    {
        return DB::transaction(function () use ($banner) {
            $this->deleteImage($banner->image);
            $this->deleteImage($banner->mobile_image);

            return $banner->delete();
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Banner $banner): Banner
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        return $banner->fresh();
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING
    // ─────────────────────────────────────────────

    private function uploadImage(UploadedFile $image): string
    {
        try {
            $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('banners', $filename, 'public');

            if (! $path) {
                throw new \Exception('Failed to upload image.');
            }

            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Image upload failed: '.$e->getMessage());
        }
    }

    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
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
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getBannersList()
    {
        return Banner::ordered()->get();
    }
}
