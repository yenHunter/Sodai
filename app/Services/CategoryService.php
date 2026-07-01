<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryService
{
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Category
    {
        return DB::transaction(function () use ($data) {

            $imagePath = null;
            if (!empty($data['image'])) {
                $imagePath = $this->uploadImage($data['image']);
            }

            return Category::create([
                'name'        => $data['name'],
                'slug'        => $this->generateUniqueSlug($data['name']),
                'description' => $data['description'] ?? null,
                'parent_id'   => $data['parent_id'] ?? null,
                'image'       => $imagePath,
                // ✅ Convert any format to boolean
                'is_active'   => $this->resolveIsActive($data['is_active'] ?? false),
                'sort_order'  => $data['sort_order'] ?? 0,
            ]);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {

            $imagePath = $category->image;

            if (!empty($data['image'])) {
                $this->deleteImage($category->image);
                $imagePath = $this->uploadImage($data['image']);
            }

            $slug = $category->slug;
            if ($category->name !== $data['name']) {
                $slug = $this->generateUniqueSlug($data['name'], $category->id);
            }

            $category->update([
                'name'        => $data['name'],
                'slug'        => $slug,
                'description' => $data['description'] ?? null,
                'parent_id'   => $data['parent_id'] ?? null,
                'image'       => $imagePath,
                // ✅ Convert any format to boolean
                'is_active'   => $this->resolveIsActive($data['is_active'] ?? false),
                'sort_order'  => $data['sort_order'] ?? 0,
            ]);

            return $category->fresh();
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Category $category): bool
    {
        return DB::transaction(function () use ($category) {

            if ($category->products()->exists()) {
                throw new \Exception(
                    'Cannot delete a category that has products assigned to it.'
                );
            }

            if ($category->hasChildren()) {
                foreach ($category->children as $child) {
                    if ($child->products()->exists()) {
                        throw new \Exception(
                            "Cannot delete: sub-category \"{$child->name}\" has products."
                        );
                    }
                    $this->deleteImage($child->image);
                    $child->delete();
                }
            }

            $this->deleteImage($category->image);
            return $category->delete();
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Category $category): Category
    {
        $newStatus = !$category->is_active;
        $category->update(['is_active' => $newStatus]);

        if (!$newStatus && $category->hasChildren()) {
            $category->children()->update(['is_active' => false]);
        }

        return $category->fresh();
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING
    // ─────────────────────────────────────────────

    private function uploadImage(UploadedFile $image): string
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        return $image->storeAs('categories', $filename, 'public');
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
            $query = Category::where('slug', $slug);
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

    public function getParentCategories()
    {
        return Category::parentOnly()
                       ->active()
                       ->ordered()
                       ->get();
    }

    public function getCategoriesWithChildren()
    {
        return Category::with(['children' => function ($query) {
                            $query->withCount('products')->ordered();
                        }])
                       ->parentOnly()
                       ->withCount('products')
                       ->ordered()
                       ->get();
    }
}