<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class ProductService
{
    public function __construct(
        private AttributeService $attributeService
    ) {}

    public function getActiveAttributeKeys(): array
    {
        return $this->attributeService->getActiveKeys();
    }

    // ─────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────

    public function store(array $data): Product
    {
        try {
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

                Log::info('Product created successfully.', [
                    'product_id' => $product->id,
                    'sku'        => $product->sku,
                    'name'       => $product->name,
                ]);

                return $product->fresh(['images', 'tags', 'relatedProducts']);
            });
        } catch (\Exception $e) {
            Log::error('Product creation failed.', [
                'exception' => $e,
                'data'      => collect($data)->except(['thumbnail', 'images'])->toArray(),
            ]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    public function update(Product $product, array $data): Product
    {
        try {
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

                Log::info('Product updated successfully.', [
                    'product_id' => $product->id,
                    'sku'        => $product->sku,
                    'name'       => $product->name,
                ]);

                return $product->fresh(['images', 'tags', 'relatedProducts']);
            });
        } catch (\Exception $e) {
            Log::error('Product update failed.', [
                'exception'  => $e,
                'product_id' => $product->id,
                'data'       => collect($data)->except(['thumbnail', 'images'])->toArray(),
            ]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────

    public function delete(Product $product): bool
    {
        if (!$product->canDelete()) {
            Log::warning('Product deletion blocked.', [
                'product_id' => $product->id,
                'sku'        => $product->sku,
                'reason'     => $product->deletion_block_reason,
            ]);

            throw new \Exception($product->deletion_block_reason);
        }

        try {
            return DB::transaction(function () use ($product) {

                $this->deleteImage($product->thumbnail);

                foreach ($product->images as $image) {
                    $this->deleteImage($image->image_path);
                }
                $product->images()->delete();

                $product->tags()->detach();

                // Remove bidirectional relations (both directions).
                // Only rows in product_relations are removed — related
                // products themselves are never deleted.
                $relatedIds = $product->relatedProducts()->pluck('products.id')->toArray();
                foreach ($relatedIds as $relatedId) {
                    $related = Product::find($relatedId);
                    $related?->relatedProducts()->detach($product->id);
                }
                $product->relatedProducts()->detach();

                $productId = $product->id;
                $sku       = $product->sku;

                $result = $product->delete();

                Log::info('Product deleted successfully.', [
                    'product_id' => $productId,
                    'sku'        => $sku,
                ]);

                return $result;
            });
        } catch (\Exception $e) {
            Log::error('Product deletion failed.', [
                'exception'  => $e,
                'product_id' => $product->id,
            ]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // TOGGLE STATUS
    // ─────────────────────────────────────────────

    public function toggleStatus(Product $product): Product
    {
        $product->update(['is_active' => !$product->is_active]);

        Log::info('Product status toggled.', [
            'product_id' => $product->id,
            'is_active'  => $product->is_active,
        ]);

        return $product->fresh();
    }

    public function toggleFeatured(Product $product): Product
    {
        $product->update(['is_featured' => !$product->is_featured]);

        Log::info('Product featured status toggled.', [
            'product_id'  => $product->id,
            'is_featured' => $product->is_featured,
        ]);

        return $product->fresh();
    }

    // ─────────────────────────────────────────────
    // STOCK MANAGEMENT
    // ─────────────────────────────────────────────

    public function updateStock(Product $product, int $quantity): Product
    {
        $oldQuantity = $product->stock_quantity;

        $product->update(['stock_quantity' => max(0, $quantity)]);

        Log::info('Product stock updated.', [
            'product_id'   => $product->id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $product->stock_quantity,
        ]);

        if ($product->is_low_stock) {
            Log::warning('Product stock is low.', [
                'product_id'    => $product->id,
                'sku'           => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'threshold'     => $product->low_stock_threshold,
            ]);
        }

        if ($product->is_out_of_stock) {
            Log::warning('Product is out of stock.', [
                'product_id' => $product->id,
                'sku'        => $product->sku,
            ]);
        }

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

        Log::info('Product gallery images deleted.', [
            'product_id' => $product->id,
            'image_ids'  => $imageIds,
        ]);
    }

    /**
     * Delete a single gallery image immediately (used by AJAX endpoint
     * in the Edit screen, separate from the bulk delete_image_ids
     * handled during full product update).
     */
    public function deleteSingleImage(Product $product, ProductImage $image): void
    {
        try {
            DB::transaction(function () use ($product, $image) {
                $this->deleteImage($image->image_path);
                $wasPrimary = $image->is_primary;
                $imageId    = $image->id;
                $image->delete();

                if ($wasPrimary) {
                    $next = $product->images()->orderBy('sort_order')->first();
                    if ($next) {
                        $next->update(['is_primary' => true]);
                    }
                }

                Log::info('Single product image deleted.', [
                    'product_id' => $product->id,
                    'image_id'   => $imageId,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Single product image deletion failed.', [
                'exception'  => $e,
                'product_id' => $product->id,
                'image_id'   => $image->id,
            ]);
            throw $e;
        }
    }

    public function setPrimaryImage(Product $product, int $imageId): void
    {
        try {
            DB::transaction(function () use ($product, $imageId) {
                $product->images()->update(['is_primary' => false]);
                $product->images()->where('id', $imageId)->update(['is_primary' => true]);
            });

            Log::info('Product primary image updated.', [
                'product_id' => $product->id,
                'image_id'   => $imageId,
            ]);
        } catch (\Exception $e) {
            Log::error('Setting primary image failed.', [
                'exception'  => $e,
                'product_id' => $product->id,
                'image_id'   => $imageId,
            ]);
            throw $e;
        }
    }

    public function reorderImages(Product $product, array $orderedIds): void
    {
        try {
            DB::transaction(function () use ($orderedIds) {
                foreach ($orderedIds as $index => $imageId) {
                    ProductImage::where('id', $imageId)->update(['sort_order' => $index]);
                }
            });

            Log::info('Product gallery images reordered.', [
                'product_id'  => $product->id,
                'ordered_ids' => $orderedIds,
            ]);
        } catch (\Exception $e) {
            Log::error('Reordering product images failed.', [
                'exception'  => $e,
                'product_id' => $product->id,
            ]);
            throw $e;
        }
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

        $previousRelatedIds = $product->relatedProducts()->pluck('products.id')->toArray();

        $product->relatedProducts()->sync($relatedIds);

        foreach ($relatedIds as $relatedId) {
            $relatedProduct = Product::find($relatedId);
            $relatedProduct?->relatedProducts()->syncWithoutDetaching([$product->id]);
        }

        $removedIds = array_diff($previousRelatedIds, $relatedIds);
        foreach ($removedIds as $removedId) {
            $removedProduct = Product::find($removedId);
            $removedProduct?->relatedProducts()->detach($product->id);
        }
    }

    // ─────────────────────────────────────────────
    // IMAGE UPLOAD / DELETE HELPERS
    // ─────────────────────────────────────────────

    /**
     * Upload and convert image to WebP format at 70% quality.
     * Regardless of input format (jpeg/png/webp), output is always
     * saved as .webp for consistent, optimized storage.
     */
    private function uploadImage(UploadedFile $image, string $directory): string
    {
        try {
            $filename = Str::uuid() . '.webp';
            $path     = $directory . '/' . $filename;

            $manager = new ImageManager(new Driver());

            $encodedImage = $manager->decode($image->getRealPath())
                ->encode(new WebpEncoder(quality: 70));

            $stored = Storage::disk('public')->put($path, (string) $encodedImage);

            if (!$stored) {
                throw new \Exception('Failed to upload image.');
            }

            return $path;
        } catch (\Exception $e) {
            Log::error('Image upload failed.', [
                'exception'         => $e,
                'directory'         => $directory,
                'original_filename' => $image->getClientOriginalName(),
            ]);
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

        if (!$category) {
            Log::warning('SKU generation: category not found, using fallback prefix.', [
                'category_id' => $categoryId,
            ]);
        }

        $prefix = $this->generateSkuPrefix($category?->name ?? 'PRD');

        // Database-agnostic ordering (works on MySQL, SQLite, PostgreSQL).
        // Safe because SKU numbers are zero-padded to a fixed 5-digit width,
        // so string sort order matches numeric order.
        $lastSku = Product::where('sku', 'like', $prefix . '-%')
            ->lockForUpdate()
            ->orderBy('sku', 'desc')
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
     * Load relations required for the read-only product details page.
     * Kept separate from getProductWithRelations() so the edit screen
     * (which reuses that method) doesn't pay for the reviews query.
     */
    public function getProductForDetails(Product $product): Product
    {
        return $product->load([
            'category.parent',
            'brand',
            'images' => fn($q) => $q->ordered(),
            'tags',
            'relatedProducts' => fn($q) => $q->select(
                'products.id',
                'products.name',
                'products.thumbnail',
                'products.price'
            ),
            'reviews' => fn($q) => $q->latest()->with('user:id,name,avatar'),
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
