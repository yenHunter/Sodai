<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandService
{
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Brand
    {
        return DB::transaction(function () use ($data) {

            $logoPath = null;
            if (!empty($data['logo'])) {
                $logoPath = $this->uploadImage($data['logo']);
            }

            return Brand::create([
                'name'        => $data['name'],
                'slug'        => $this->generateUniqueSlug($data['name']),
                'description' => $data['description'] ?? null,
                'website'     => $data['website'] ?? null,
                'logo'        => $logoPath,
                // ✅ Convert any format to boolean
                'is_active'   => $this->resolveIsActive($data['is_active'] ?? false),
                'sort_order'  => $data['sort_order'] ?? 0,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Brand $brand, array $data): Brand
    {
        return DB::transaction(function () use ($brand, $data) {

            $logoPath = $brand->logo;

            if (!empty($data['logo'])) {
                $this->deleteImage($brand->logo);
                $logoPath = $this->uploadImage($data['logo']);
            }

            $slug = $brand->slug;
            if ($brand->name !== $data['name']) {
                $slug = $this->generateUniqueSlug($data['name'], $brand->id);
            }

            $brand->update([
                'name'        => $data['name'],
                'slug'        => $slug,
                'description' => $data['description'] ?? null,
                'website'     => $data['website'] ?? null,
                'logo'        => $logoPath,
                // ✅ Convert any format to boolean
                'is_active'   => $this->resolveIsActive($data['is_active'] ?? false),
                'sort_order'  => $data['sort_order'] ?? 0,
            ]);

            return $brand->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Brand $brand): bool
    {
        return DB::transaction(function () use ($brand) {

            if ($brand->products()->exists()) {
                throw new \Exception(
                    'Cannot delete a brand that has products assigned to it.'
                );
            }

            $this->deleteImage($brand->logo);
            return $brand->delete();
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Brand $brand): Brand
    {
        $brand->update(['is_active' => !$brand->is_active]);

        return $brand->fresh();
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING
    // ─────────────────────────────────────────────

    private function uploadImage(UploadedFile $image): string
    {
        try {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path     = $image->storeAs('brands', $filename, 'public');

            if (!$path) {
                throw new \Exception('Failed to upload logo.');
            }

            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Logo upload failed: ' . $e->getMessage());
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
    // Handles: 'active', 'inactive', true, false, 1, 0
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

    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $slug     = Str::slug($name);
        $original = $slug;
        $count    = 1;

        while (true) {
            $query = Brand::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if (!$query->exists()) break;
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getBrandsList()
    {
        return Brand::withCount('products')
            ->ordered()
            ->get();
    }
}