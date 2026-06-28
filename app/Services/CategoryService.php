<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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

            $category = Category::create([
                'name'        => $data['name'],
                'slug'        => $this->generateUniqueSlug($data['name']),
                'description' => $data['description'] ?? null,
                'parent_id'   => $data['parent_id'] ?? null,
                'image'       => $imagePath,
                'is_active'   => $data['is_active'] ?? true,
                'sort_order'  => $data['sort_order'] ?? 0,
            ]);

            return $category;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {

            $imagePath = $category->image; // Keep existing image

            if (!empty($data['image'])) {
                // Delete old image
                $this->deleteImage($category->image);
                // Upload new image
                $imagePath = $this->uploadImage($data['image']);
            }

            // Regenerate slug only if name changed
            $slug = $category->slug;
            if ($category->name !== $data['name']) {
                $slug = $this->generateUniqueSlug(
                    $data['name'],
                    $category->id
                );
            }

            $category->update([
                'name'        => $data['name'],
                'slug'        => $slug,
                'description' => $data['description'] ?? null,
                'parent_id'   => $data['parent_id'] ?? null,
                'image'       => $imagePath,
                'is_active'   => $data['is_active'] ?? true,
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

            // Check if category has products
            if ($category->products()->exists()) {
                throw new \Exception(
                    'Cannot delete category that has products assigned to it.'
                );
            }

            // If deleting a parent, also delete children
            if ($category->hasChildren()) {
                foreach ($category->children as $child) {
                    $this->deleteImage($child->image);
                    $child->delete();
                }
            }

            // Delete category image
            $this->deleteImage($category->image);

            return $category->delete();
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Category $category): Category
    {
        $category->update([
            'is_active' => !$category->is_active,
        ]);

        // If deactivating parent, deactivate all children too
        if (!$category->is_active && $category->hasChildren()) {
            $category->children()->update(['is_active' => false]);
        }

        return $category->fresh();
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING
    // ─────────────────────────────────────────────

    private function uploadImage(UploadedFile $image): string
    {
        $filename  = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path      = $image->storeAs('categories', $filename, 'public');
        return $path;
    }

    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    // ─────────────────────────────────────────────
    // SLUG GENERATION
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

            if (!$query->exists()) {
                break;
            }

            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    // Get all parent categories for dropdown
    public function getParentCategories()
    {
        return Category::parentOnly()
                       ->active()
                       ->ordered()
                       ->get();
    }

    // Get all categories with children (for listing)
    public function getCategoriesWithChildren()
    {
        return Category::with(['children' => function ($query) {
                            $query->withCount('products')
                                  ->ordered();
                        }])
                       ->parentOnly()
                       ->withCount('products')
                       ->ordered()
                       ->get();
    }

    // Get all categories flat (for dropdowns)
    public function getAllCategoriesFlat()
    {
        return Category::with('parent')
                       ->active()
                       ->ordered()
                       ->get()
                       ->map(function ($category) {
                           return [
                               'id'   => $category->id,
                               'name' => $category->full_name,
                           ];
                       });
    }
}