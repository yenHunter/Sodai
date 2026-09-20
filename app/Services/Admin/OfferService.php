<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Offer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfferService
{
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Offer
    {
        return DB::transaction(function () use ($data) {

            $imagePath = $this->uploadImage($data['image']);

            return Offer::create([
                'title' => $data['title'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'description' => $data['description'] ?? null,
                'button_text' => $data['button_text'] ?? null,
                'button_url' => $data['button_url'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'image' => $imagePath,
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

    public function update(Offer $offer, array $data): Offer
    {
        return DB::transaction(function () use ($offer, $data) {

            $imagePath = $offer->image;
            if (! empty($data['image'])) {
                $this->deleteImage($offer->image);
                $imagePath = $this->uploadImage($data['image']);
            }

            $offer->update([
                'title' => $data['title'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'description' => $data['description'] ?? null,
                'button_text' => $data['button_text'] ?? null,
                'button_url' => $data['button_url'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'image' => $imagePath,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? false),
                'sort_order' => $data['sort_order'] ?? 0,
                'starts_at' => $data['starts_at'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
            ]);

            return $offer->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Offer $offer): bool
    {
        return DB::transaction(function () use ($offer) {
            $this->deleteImage($offer->image);

            return $offer->delete();
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Offer $offer): Offer
    {
        $offer->update(['is_active' => ! $offer->is_active]);

        return $offer->fresh();
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING
    // ─────────────────────────────────────────────

    private function uploadImage(UploadedFile $image): string
    {
        try {
            $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
            $path = $image->storeAs('offers', $filename, 'public');

            if (! $path) {
                throw new \Exception('Failed to upload image.');
            }

            return $path;
        } catch (\Throwable $e) {
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

    public function getOffersList()
    {
        return Offer::with('category')->ordered()->get();
    }

    public function getCategoriesForSelect()
    {
        return Category::active()->ordered()->get();
    }
}
