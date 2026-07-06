<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {

            $thumbnailPath = null;
            if (!empty($data['thumbnail'])) {
                $thumbnailPath = $this->uploadImage($data['thumbnail'], 'products/thumbnails');
            }

            $product = Product::create([
                'name'                 => $data['name'],
                'slug'                 => $this->generateUniqueSlug($data['name']),
                'sku'                  => $this->generateUniqueSku($data['category_id']),
                'short_description'    => $data['short_description'] ?? null,
                'description'          => $data['description'] ?? null,
                'category_id'          => $data['category_id'],
                'brand_id'             => $data['brand_id'] ?? null,
                'price'                => $data['price'],
                'purchase_price'       => $data['purchase_price'] ?? null,
                'discount_type'        => $data['discount_type'] ?? null,
                'discount_value'       => $data['discount_value'] ?? null,
                'stock_quantity'       => $data['stock_quantity'],
                'low_stock_threshold'  => $data['low_stock_threshold'] ?? 5,
                'thumbnail'            => $thumbnailPath,
                'weight'               => $data['weight'] ?? null,
                'weight_unit'          => $data['weight_unit'] ?? null,
                'color'                => $data['color'] ?? null,
                'size'                 => $data['size'] ?? null,
                'is_active'            => $this->resolveBoolean($data['is_active'] ?? false),
                'is_featured'          => $this->resolveBoolean($data['is_featured'] ?? false),
                'meta'                 => $data['meta'] ?? null,
            ]);

            if (!empty($data['images'])) {
                $this->storeProductImages($product, $data['images']);
            }

            if (!empty($data['tags'])) {
                $this->syncTags($product, $data['tags']);
            }

            if (!empty($data['related_products'])) {
                $this->syncRelatedProducts($product, $data['related_products']);
            }

            return $product->fresh(['images', 'tags', 'relatedProducts']);
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {

            $thumbnailPath = $product->thumbnail;
            if (!empty($data['thumbnail'])) {
                $this->deleteImage($product->thumbnail);
                $thumbnailPath = $this->uploadImage($data['thumbnail'], 'products/thumbnails');
            }

            $slug = $product->slug;
            if ($product->name !== $data['name']) {
                $slug = $this->generateUniqueSlug($data['name'], $product->id);
            }

            // ✅ SKU is immutable — never regenerated or overwritten on update
            $product->update([
                'name'                 => $data['name'],
                'slug'                 => $slug,
                'short_description'    => $data['short_description'] ?? null,
                'description'          => $data['description'] ?? null,
                'category_id'          => $data['category_id'],
                'brand_id'             => $data['brand_id'] ?? null,
                'price'                => $data['price'],
                'purchase_price'       => $data['purchase_price'] ?? null,
                'discount_type'        => $data['discount_type'] ?? null,
                'discount_value'       => $data['discount_value'] ?? null,
                'stock_quantity'       => $data['stock_quantity'],
                'low_stock_threshold'  => $data['low_stock_threshold'] ?? 5,
                'thumbnail'            => $thumbnailPath,
                'weight'               => $data['weight'] ?? null,
                'weight_unit'          => $data['weight_unit'] ?? null,
                'color'                => $data['color'] ?? null,
                'size'                 => $data['size'] ?? null,
                'is_active'            => $this->resolveBoolean($data['is_active'] ?? false),
                'is_featured'          => $this->resolveBoolean($data['is_featured'] ?? false),
                'meta'                 => $data['meta'] ?? null,
            ]);

            if (!empty($data['delete_image_ids'])) {
                $this->deleteProductImages($product, $data['delete_image_ids']);
            }

            if (!empty($data['images'])) {
                $this->storeProductImages($product, $data['images']);
            }

            $this->syncTags($product, $data['tags'] ?? []);
            $this->syncRelatedProducts($product, $data['related_products'] ?? []);

            return $product->fresh(['images', 'tags', 'relatedProducts']);
        });
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {

            if (!$product->canDelete()) {
                throw new \Exception($product->deletion_block_reason);
            }

            $this->deleteImage($product->thumbnail);

            foreach ($product->images as $image) {
                $this->deleteImage($image->image_path);
            }
            $product->images()->delete();

            $product->tags()->detach();

            // Remove bidirectional relations (both directions)
            // NOTE: This only deletes rows from product_relations table.
            // The related products themselves are NOT deleted.
            $relatedIds = $product->relatedProducts()->pluck('products.id')->toArray();
            foreach ($relatedIds as $relatedId) {
                $related = Product::find($relatedId);
                $related?->relatedProducts()->detach($product->id);
            }
            $product->relatedProducts()->detach();

            return $product->delete();
        });
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Product $product): Product
    {
        $product->update(['is_active' => !$product->is_active]);
        return $product->fresh();
    }

    public function toggleFeatured(Product $product): Product
    {
        $product->update(['is_featured' => !$product->is_featured]);
        return $product->fresh();
    }

    // ─────────────────────────────────────────────
    // STOCK MANAGEMENT
    // ─────────────────────────────────────────────

    public function updateStock(Product $product, int $quantity): Product
    {
        $product->update(['stock_quantity' => max(0, $quantity)]);
        return $product->fresh();
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING (GALLERY)
    // ─────────────────────────────────────────────

    private function storeProductImages(Product $product, array $images): void
    {
        $existingCount = $product->images()->count();
        $hasPrimary    = $product->images()->where('is_primary', true)->exists();

        foreach ($images as $index => $image) {
            if (!$image instanceof UploadedFile) {
                continue;
            }

            $path = $this->uploadImage($image, 'products/gallery');

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => !$hasPrimary && $index === 0,
                'sort_order' => $existingCount + $index,
            ]);

            if (!$hasPrimary && $index === 0) {
                $hasPrimary = true;
            }
        }
    }

    /**
     * Delete a single gallery image immediately (used by AJAX endpoint
     * in the Edit screen, separate from the bulk delete_image_ids
     * handled during full product update).
     */
    public function deleteSingleImage(Product $product, ProductImage $image): void
    {
        DB::transaction(function () use ($product, $image) {
            $this->deleteImage($image->image_path);
            $wasPrimary = $image->is_primary;
            $image->delete();

            if ($wasPrimary) {
                $next = $product->images()->orderBy('sort_order')->first();
                if ($next) {
                    $next->update(['is_primary' => true]);
                }
            }
        });
    }

    private function deleteProductImages(Product $product, array $imageIds): void
    {
        $images = $product->images()->whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $this->deleteImage($image->image_path);
            $wasPrimary = $image->is_primary;
            $image->delete();

            if ($wasPrimary) {
                $next = $product->images()->orderBy('sort_order')->first();
                if ($next) {
                    $next->update(['is_primary' => true]);
                }
            }
        }
    }

    public function setPrimaryImage(Product $product, int $imageId): void
    {
        DB::transaction(function () use ($product, $imageId) {
            $product->images()->update(['is_primary' => false]);
            $product->images()->where('id', $imageId)->update(['is_primary' => true]);
        });
    }

    public function reorderImages(Product $product, array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $imageId) {
                ProductImage::where('id', $imageId)->update(['sort_order' => $index]);
            }
        });
    }

    // ─────────────────────────────────────────────
    // TAGS
    // ─────────────────────────────────────────────

    private function syncTags(Product $product, array $tagNames): void
    {
        $tagIds = collect($tagNames)
            ->filter(fn($name) => !empty(trim($name)))
            ->map(fn($name) => Tag::findOrCreateByName(trim($name))->id)
            ->unique()
            ->values()
            ->toArray();

        $product->tags()->sync($tagIds);
    }

    // ─────────────────────────────────────────────
    // RELATED PRODUCTS (BIDIRECTIONAL)
    // ─────────────────────────────────────────────

    /**
     * Sync related products in both directions.
     * If Product A relates to B, then B automatically relates to A.
     * Handles additions AND removals symmetrically.
     */
    private function syncRelatedProducts(Product $product, array $relatedIds): void
    {
        $relatedIds = collect($relatedIds)
            ->filter(fn($id) => $id !== $product->id)
            ->unique()
            ->values()
            ->toArray();

        // Capture existing relations before sync (for removal detection)
        $previousRelatedIds = $product->relatedProducts()->pluck('products.id')->toArray();

        // Sync forward direction (product_id -> related_product_id)
        $product->relatedProducts()->sync($relatedIds);

        // Ensure reverse direction exists for all current relations
        foreach ($relatedIds as $relatedId) {
            $relatedProduct = Product::find($relatedId);
            $relatedProduct?->relatedProducts()->syncWithoutDetaching([$product->id]);
        }

        // Remove reverse direction for relations that were dropped
        $removedIds = array_diff($previousRelatedIds, $relatedIds);
        foreach ($removedIds as $removedId) {
            $removedProduct = Product::find($removedId);
            $removedProduct?->relatedProducts()->detach($product->id);
        }
    }

    // ─────────────────────────────────────────────
    // IMAGE UPLOAD / DELETE HELPERS
    // ─────────────────────────────────────────────

    private function uploadImage(UploadedFile $image, string $directory): string
    {
        try {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $path     = $image->storeAs($directory, $filename, 'public');

            if (!$path) {
                throw new \Exception('Failed to upload image.');
            }

            return $path;
        } catch (\Exception $e) {
            throw new \Exception('Image upload failed: ' . $e->getMessage());
        }
    }

    private function deleteImage(?string $imagePath): void
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    // ─────────────────────────────────────────────
    // RESOLVE BOOLEAN
    // ─────────────────────────────────────────────

    private function resolveBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['active', '1', 'true', 'on', 'yes']);
        }

        return false;
    }

    // ─────────────────────────────────────────────
    // SLUG
    // ─────────────────────────────────────────────

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug     = Str::slug($name);
        $original = $slug;
        $count    = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if (!$query->exists()) break;
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    // ─────────────────────────────────────────────
    // SKU AUTO-GENERATION
    // Format: {CATEGORY_PREFIX}-{5-DIGIT-SEQUENCE}
    // Example: ELE-00001, CLO-00042
    // ─────────────────────────────────────────────

    private function generateUniqueSku(int $categoryId): string
    {
        $category = Category::find($categoryId);
        $prefix   = $this->generateSkuPrefix($category?->name ?? 'PRD');

        // Lock rows matching this prefix to prevent race conditions
        // when multiple products are created simultaneously
        $lastSku = Product::where('sku', 'like', $prefix . '-%')
            ->lockForUpdate()
            ->orderByRaw('CAST(SUBSTRING_INDEX(sku, "-", -1) AS UNSIGNED) DESC')
            ->value('sku');

        $nextNumber = 1;
        if ($lastSku) {
            $lastNumber = (int) substr($lastSku, strrpos($lastSku, '-') + 1);
            $nextNumber = $lastNumber + 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    private function generateSkuPrefix(string $categoryName): string
    {
        $clean  = preg_replace('/[^A-Za-z]/', '', $categoryName);
        $prefix = strtoupper(substr($clean, 0, 3));

        return $prefix ?: 'PRD';
    }

    // ─────────────────────────────────────────────
    // QUERY HELPERS
    // ─────────────────────────────────────────────

    public function getProductsList(array $filters = [])
    {
        $query = Product::with(['category', 'brand'])
            ->withCount(['images', 'tags']);

        if (!empty($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        if (!empty($filters['brand_id'])) {
            $query->byBrand($filters['brand_id']);
        }

        if (!empty($filters['status'])) {
            $filters['status'] === 'active'
                ? $query->active()
                : $query->inactive();
        }

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        return $query->newest()->get();
    }

    public function getProductWithRelations(Product $product): Product
    {
        return $product->load([
            'category',
            'brand',
            'images' => fn($q) => $q->ordered(),
            'tags',
            'relatedProducts' => fn($q) => $q->select(
                'products.id',
                'products.name',
                'products.thumbnail',
                'products.price'
            ),
        ]);
    }

    /**
     * Only leaf-level categories (no children) are selectable for products.
     */
    public function getAssignableCategories()
    {
        return Category::with('parent')
            ->whereDoesntHave('children')
            ->active()
            ->ordered()
            ->get();
    }

    public function getActiveBrands()
    {
        return Brand::active()->ordered()->get();
    }

    public function getProductsForRelation(?string $excludeId = null)
    {
        $query = Product::active()->select('id', 'name', 'sku', 'thumbnail');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get();
    }
}
