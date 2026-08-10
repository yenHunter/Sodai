<?php

namespace App\Services;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use Illuminate\Http\UploadedFile;
use App\Models\ProductOptionValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Schema;
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

    /**
     * Expected $data shape:
     * [
     *   'name', 'short_description', 'description', 'category_id', 'brand_id',
     *   'thumbnail' => UploadedFile|null,
     *   'is_active', 'is_featured', 'meta' => [...],
     *   'tags' => [...], 'related_products' => [...],
     *   'images' => UploadedFile[],           // product-level gallery
     *   'variants' => [
     *       [
     *           'price', 'purchase_price', 'discount_type', 'discount_value',
     *           'stock_quantity', 'low_stock_threshold', 'weight', 'weight_unit',
     *           'thumbnail' => UploadedFile|null,
     *           'is_active', 'is_default',
     *           'option_values' => [['option' => 'Color', 'value' => 'Red'], ...],
     *           'images' => UploadedFile[],    // variant-specific gallery
     *       ],
     *       ...
     *   ],
     *   // If 'variants' is omitted entirely, a single default variant is
     *   // built from top-level price/stock_quantity/etc. (simple product path).
     * ]
     */
    public function store(array $data): Product
    {
        try {
            return DB::transaction(function () use ($data) {

                $thumbnailPath = null;
                if (!empty($data['thumbnail'])) {
                    $thumbnailPath = $this->uploadImage($data['thumbnail'], 'products/thumbnails');
                }

                $product = Product::create([
                    'name'              => $data['name'],
                    'slug'              => $this->generateUniqueSlug($data['name']),
                    'short_description' => $data['short_description'] ?? null,
                    'description'       => $data['description'] ?? null,
                    'category_id'       => $data['category_id'],
                    'brand_id'          => $data['brand_id'] ?? null,
                    'thumbnail'         => $thumbnailPath,
                    'is_active'         => $this->resolveBoolean($data['is_active'] ?? false),
                    'is_featured'       => $this->resolveBoolean($data['is_featured'] ?? false),
                    'meta'              => $data['meta'] ?? null,
                ]);

                $variantsInput = $data['variants'] ?? [];
                if (empty($variantsInput)) {
                    $variantsInput = [$this->defaultVariantPayload($data)];
                }

                foreach ($variantsInput as $index => $variantData) {
                    $isSimpleDefault = $index === 0 && empty($variantData['option_values']);
                    $this->createVariant($product, $variantData, isDefault: $isSimpleDefault);
                }

                $this->ensureExactlyOneDefaultVariant($product);

                if (!empty($data['images'])) {
                    $this->storeProductImages($product, $data['images']);
                }

                if (!empty($data['tags'])) {
                    $this->syncTags($product, $data['tags']);
                }

                if (!empty($data['related_products'])) {
                    $this->syncRelatedProducts($product, $data['related_products']);
                }

                $product->refreshPriceAndStockCache();

                Log::info('Product created successfully.', [
                    'product_id'    => $product->id,
                    'name'          => $product->name,
                    'variant_count' => $product->variants()->count(),
                ]);

                return $product->fresh([
                    'variants.optionValues.option',
                    'images',
                    'tags',
                    'relatedProducts',
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Product creation failed.', [
                'exception' => $e,
                'data'      => collect($data)->except(['thumbnail', 'images', 'variants'])->toArray(),
            ]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────

    /**
     * Same shape as store(), plus per-variant 'id' (existing variant to update
     * vs. omitted = new variant), and top-level 'delete_variant_ids',
     * 'delete_image_ids', and per-variant 'delete_image_ids'.
     */
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
                    'name'              => $data['name'],
                    'slug'              => $slug,
                    'short_description' => $data['short_description'] ?? null,
                    'description'       => $data['description'] ?? null,
                    'category_id'       => $data['category_id'],
                    'brand_id'          => $data['brand_id'] ?? null,
                    'thumbnail'         => $thumbnailPath,
                    'is_active'         => $this->resolveBoolean($data['is_active'] ?? false),
                    'is_featured'       => $this->resolveBoolean($data['is_featured'] ?? false),
                    'meta'              => $data['meta'] ?? null,
                ]);

                if (!empty($data['delete_variant_ids'])) {
                    $this->deleteVariants($product, $data['delete_variant_ids']);
                }

                $variantsInput = $data['variants'] ?? [];
                foreach ($variantsInput as $index => $variantData) {
                    if (!empty($variantData['id'])) {
                        $this->updateVariant($product, (int) $variantData['id'], $variantData);
                    } else {
                        $this->createVariant($product, $variantData, isDefault: false);
                    }
                }

                if ($product->variants()->count() === 0) {
                    throw new \Exception('A product must have at least one purchasable variant.');
                }

                $this->ensureExactlyOneDefaultVariant($product);

                if (!empty($data['delete_image_ids'])) {
                    $this->deleteProductImages($product, $data['delete_image_ids']);
                }

                if (!empty($data['images'])) {
                    $this->storeProductImages($product, $data['images']);
                }

                $this->syncTags($product, $data['tags'] ?? []);
                $this->syncRelatedProducts($product, $data['related_products'] ?? []);

                $product->refreshPriceAndStockCache();

                Log::info('Product updated successfully.', [
                    'product_id' => $product->id,
                    'name'       => $product->name,
                ]);

                return $product->fresh([
                    'variants.optionValues.option',
                    'images',
                    'tags',
                    'relatedProducts',
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Product update failed.', [
                'exception'  => $e,
                'product_id' => $product->id,
                'data'       => collect($data)->except(['thumbnail', 'images', 'variants'])->toArray(),
            ]);
            throw $e;
        }
    }

    // ─────────────────────────────────────────────
    // DELETE (product)
    // ─────────────────────────────────────────────

    public function delete(Product $product): bool
    {
        if (!$product->canDelete()) {
            Log::warning('Product deletion blocked.', [
                'product_id' => $product->id,
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

                foreach ($product->variants as $variant) {
                    $this->deleteImage($variant->thumbnail);
                    $variant->optionValues()->detach();
                    $variant->delete();
                }

                $product->tags()->detach();

                $relatedIds = $product->relatedProducts()->pluck('products.id')->toArray();
                foreach ($relatedIds as $relatedId) {
                    $related = Product::find($relatedId);
                    $related?->relatedProducts()->detach($product->id);
                }
                $product->relatedProducts()->detach();

                $productId = $product->id;
                $result    = $product->delete();

                Log::info('Product deleted successfully.', ['product_id' => $productId]);

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
    // TOGGLE STATUS / FEATURED (product-level, unchanged in meaning)
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

        return $product->fresh();
    }

    // ─────────────────────────────────────────────
    // VARIANT MANAGEMENT
    // ─────────────────────────────────────────────

    private function defaultVariantPayload(array $data): array
    {
        return [
            'price'               => $data['price'] ?? 0,
            'purchase_price'      => $data['purchase_price'] ?? null,
            'discount_type'       => $data['discount_type'] ?? null,
            'discount_value'      => $data['discount_value'] ?? null,
            'stock_quantity'      => $data['stock_quantity'] ?? 0,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'weight'              => $data['weight'] ?? null,
            'weight_unit'         => $data['weight_unit'] ?? null,
            'thumbnail'           => null,
            'is_active'           => true,
            'option_values'       => [],
        ];
    }

    private function createVariant(Product $product, array $variantData, bool $isDefault = false): ProductVariant
    {
        $thumbnailPath = null;
        if (!empty($variantData['thumbnail'])) {
            $thumbnailPath = $this->uploadImage($variantData['thumbnail'], 'products/variants');
        }

        $variant = ProductVariant::create([
            'product_id'          => $product->id,
            'sku'                 => $variantData['sku'] ?? $this->generateUniqueVariantSku($product->category_id),
            'price'               => $variantData['price'] ?? 0,
            'purchase_price'      => $variantData['purchase_price'] ?? null,
            'discount_type'       => $variantData['discount_type'] ?? null,
            'discount_value'      => $variantData['discount_value'] ?? null,
            'stock_quantity'      => $variantData['stock_quantity'] ?? 0,
            'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
            'weight'              => $variantData['weight'] ?? null,
            'weight_unit'         => $variantData['weight_unit'] ?? null,
            'thumbnail'           => $thumbnailPath,
            'is_active'           => $this->resolveBoolean($variantData['is_active'] ?? true),
            'is_default'          => $isDefault || $this->resolveBoolean($variantData['is_default'] ?? false),
        ]);

        $this->syncVariantOptionValues($variant, $variantData['option_values'] ?? []);

        if (!empty($variantData['images'])) {
            $this->storeProductImages($product, $variantData['images'], $variant->id);
        }

        return $variant;
    }

    private function updateVariant(Product $product, int $variantId, array $variantData): ProductVariant
    {
        $variant = $product->variants()->findOrFail($variantId);

        $thumbnailPath = $variant->thumbnail;
        if (!empty($variantData['thumbnail'])) {
            $this->deleteImage($variant->thumbnail);
            $thumbnailPath = $this->uploadImage($variantData['thumbnail'], 'products/variants');
        }

        $variant->update([
            'sku'                 => $variantData['sku'] ?? $variant->sku,
            'price'               => $variantData['price'] ?? $variant->price,
            'purchase_price'      => $variantData['purchase_price'] ?? null,
            'discount_type'       => $variantData['discount_type'] ?? null,
            'discount_value'      => $variantData['discount_value'] ?? null,
            'stock_quantity'      => $variantData['stock_quantity'] ?? $variant->stock_quantity,
            'low_stock_threshold' => $variantData['low_stock_threshold'] ?? $variant->low_stock_threshold,
            'weight'              => $variantData['weight'] ?? null,
            'weight_unit'         => $variantData['weight_unit'] ?? null,
            'thumbnail'           => $thumbnailPath,
            'is_active'           => $this->resolveBoolean($variantData['is_active'] ?? $variant->is_active),
            'is_default'          => $this->resolveBoolean($variantData['is_default'] ?? $variant->is_default),
        ]);

        if (array_key_exists('option_values', $variantData)) {
            $this->syncVariantOptionValues($variant, $variantData['option_values']);
        }

        if (!empty($variantData['delete_image_ids'])) {
            $this->deleteProductImages($product, $variantData['delete_image_ids']);
        }

        if (!empty($variantData['images'])) {
            $this->storeProductImages($product, $variantData['images'], $variant->id);
        }

        return $variant->fresh();
    }

    private function deleteVariants(Product $product, array $variantIds): void
    {
        $variants = $product->variants()->whereIn('id', $variantIds)->get();

        foreach ($variants as $variant) {
            if (!$this->canDeleteVariant($variant)) {
                throw new \Exception("Cannot delete variant \"{$variant->sku}\": it has existing order or cart history.");
            }
        }

        foreach ($variants as $variant) {
            foreach ($variant->images as $image) {
                $this->deleteImage($image->image_path);
            }
            $variant->images()->delete();
            $this->deleteImage($variant->thumbnail);
            $variant->optionValues()->detach();
            $variant->delete();
        }
    }

    private function canDeleteVariant(ProductVariant $variant): bool
    {
        if (
            Schema::hasTable('order_items') &&
            DB::table('order_items')->where('product_variant_id', $variant->id)->exists()
        ) {
            return false;
        }

        if (
            Schema::hasTable('cart_items') &&
            DB::table('cart_items')->where('product_variant_id', $variant->id)->exists()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Guarantees exactly one variant is flagged is_default, so cart/order
     * "add to cart" without an explicit selection always has something to fall back to.
     */
    private function ensureExactlyOneDefaultVariant(Product $product): void
    {
        $defaults = $product->variants()->where('is_default', true)->get();

        if ($defaults->count() === 1) {
            return;
        }

        if ($defaults->count() > 1) {
            $product->variants()
                ->where('is_default', true)
                ->where('id', '!=', $defaults->first()->id)
                ->update(['is_default' => false]);
            return;
        }

        $fallback = $product->variants()->active()->orderBy('id')->first()
            ?? $product->variants()->orderBy('id')->first();

        $fallback?->update(['is_default' => true]);
    }

    // ─────────────────────────────────────────────
    // VARIANT OPTIONS (Color, Size, Weight, or anything admin defines)
    // ─────────────────────────────────────────────

    private function syncVariantOptionValues(ProductVariant $variant, array $optionValuePairs): void
    {
        $valueIds = [];

        foreach ($optionValuePairs as $pair) {
            $optionName = trim($pair['option'] ?? '');
            $value      = trim($pair['value'] ?? '');

            if ($optionName === '' || $value === '') {
                continue;
            }

            $valueIds[] = $this->resolveOptionValue($optionName, $value, $pair['swatch'] ?? null)->id;
        }

        $variant->optionValues()->sync($valueIds);
    }

    private function resolveOptionValue(string $optionName, string $value, ?string $swatch = null): ProductOptionValue
    {
        $option = ProductOption::firstOrCreate(
            ['slug' => Str::slug($optionName)],
            ['name' => $optionName]
        );

        return ProductOptionValue::firstOrCreate(
            [
                'product_option_id' => $option->id,
                'slug'               => Str::slug($value),
            ],
            [
                'value'   => $value,
                'swatch'  => $swatch,
            ]
        );
    }

    // ─────────────────────────────────────────────
    // STOCK MANAGEMENT (now variant-scoped)
    // ─────────────────────────────────────────────

    public function updateVariantStock(ProductVariant $variant, int $quantity): ProductVariant
    {
        $oldQuantity = $variant->stock_quantity;

        $variant->update(['stock_quantity' => max(0, $quantity)]);
        $variant->product->refreshPriceAndStockCache();

        Log::info('Product variant stock updated.', [
            'variant_id'   => $variant->id,
            'product_id'   => $variant->product_id,
            'old_quantity' => $oldQuantity,
            'new_quantity' => $variant->stock_quantity,
        ]);

        if ($variant->is_low_stock) {
            Log::warning('Product variant stock is low.', ['variant_id' => $variant->id, 'sku' => $variant->sku]);
        }

        if ($variant->is_out_of_stock) {
            Log::warning('Product variant is out of stock.', ['variant_id' => $variant->id, 'sku' => $variant->sku]);
        }

        return $variant->fresh();
    }

    // ─────────────────────────────────────────────
    // IMAGE HANDLING (GALLERY — product-level or variant-scoped)
    // ─────────────────────────────────────────────

    private function storeProductImages(Product $product, array $images, ?int $variantId = null): void
    {
        $existingQuery = $product->images()->when(
            $variantId,
            fn($q) => $q->where('product_variant_id', $variantId),
            fn($q) => $q->whereNull('product_variant_id')
        );

        $existingCount = $existingQuery->count();
        $hasPrimary    = (clone $existingQuery)->where('is_primary', true)->exists();

        foreach ($images as $index => $image) {
            if (!$image instanceof UploadedFile) {
                continue;
            }

            $path = $this->uploadImage($image, 'products/gallery');

            ProductImage::create([
                'product_id'         => $product->id,
                'product_variant_id' => $variantId,
                'image_path'         => $path,
                'is_primary'         => !$hasPrimary && $index === 0,
                'sort_order'         => $existingCount + $index,
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
            $variantId  = $image->product_variant_id;
            $image->delete();

            if ($wasPrimary) {
                $next = $product->images()
                    ->when(
                        $variantId,
                        fn($q) => $q->where('product_variant_id', $variantId),
                        fn($q) => $q->whereNull('product_variant_id')
                    )
                    ->orderBy('sort_order')
                    ->first();

                $next?->update(['is_primary' => true]);
            }
        }

        Log::info('Product gallery images deleted.', [
            'product_id' => $product->id,
            'image_ids'  => $imageIds,
        ]);
    }

    public function deleteSingleImage(Product $product, ProductImage $image): void
    {
        try {
            DB::transaction(function () use ($product, $image) {
                $this->deleteImage($image->image_path);
                $wasPrimary = $image->is_primary;
                $variantId  = $image->product_variant_id;
                $imageId    = $image->id;
                $image->delete();

                if ($wasPrimary) {
                    $next = $product->images()
                        ->when(
                            $variantId,
                            fn($q) => $q->where('product_variant_id', $variantId),
                            fn($q) => $q->whereNull('product_variant_id')
                        )
                        ->orderBy('sort_order')
                        ->first();

                    $next?->update(['is_primary' => true]);
                }

                Log::info('Single product image deleted.', ['product_id' => $product->id, 'image_id' => $imageId]);
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
                $image = $product->images()->findOrFail($imageId);

                $product->images()
                    ->when(
                        $image->product_variant_id,
                        fn($q) => $q->where('product_variant_id', $image->product_variant_id),
                        fn($q) => $q->whereNull('product_variant_id')
                    )
                    ->update(['is_primary' => false]);

                $image->update(['is_primary' => true]);
            });

            Log::info('Product primary image updated.', ['product_id' => $product->id, 'image_id' => $imageId]);
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
            Log::error('Reordering product images failed.', ['exception' => $e, 'product_id' => $product->id]);
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
    // RELATED PRODUCTS (BIDIRECTIONAL) — unchanged
    // ─────────────────────────────────────────────

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
    // IMAGE UPLOAD / DELETE HELPERS — unchanged
    // ─────────────────────────────────────────────

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
    // RESOLVE BOOLEAN — unchanged
    // ─────────────────────────────────────────────

    private function resolveBoolean(mixed $value): bool
    {
        if (is_bool($value)) return $value;
        if (is_int($value)) return $value === 1;
        if (is_string($value)) return in_array(strtolower($value), ['active', '1', 'true', 'on', 'yes']);
        return false;
    }

    // ─────────────────────────────────────────────
    // SLUG — unchanged
    // ─────────────────────────────────────────────

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug     = Str::slug($name);
        $original = $slug;
        $count    = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            if ($ignoreId) $query->where('id', '!=', $ignoreId);
            if (!$query->exists()) break;
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    // ─────────────────────────────────────────────
    // SKU AUTO-GENERATION — now per variant
    // ─────────────────────────────────────────────

    private function generateUniqueVariantSku(int $categoryId): string
    {
        $category = Category::find($categoryId);

        if (!$category) {
            Log::warning('SKU generation: category not found, using fallback prefix.', ['category_id' => $categoryId]);
        }

        $prefix = $this->generateSkuPrefix($category?->name ?? 'PRD');

        $lastSku = ProductVariant::where('sku', 'like', $prefix . '-%')
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
        $query = Product::with(['category', 'brand', 'defaultVariant'])
            ->withCount(['images', 'tags', 'variants']);

        if (!empty($filters['category_id'])) $query->byCategory($filters['category_id']);
        if (!empty($filters['brand_id']))    $query->byBrand($filters['brand_id']);
        if (!empty($filters['status'])) {
            $filters['status'] === 'active' ? $query->active() : $query->inactive();
        }
        if (!empty($filters['search'])) $query->search($filters['search']);

        return $query->newest()->get();
    }

    public function getProductWithRelations(Product $product): Product
    {
        return $product->load([
            'category',
            'brand',
            'variants.optionValues.option',
            'variants.images' => fn($q) => $q->orderBy('sort_order'),
            'images' => fn($q) => $q->whereNull('product_variant_id')->orderBy('sort_order'),
            'tags',
            'relatedProducts' => fn($q) => $q->select('products.id', 'products.name', 'products.thumbnail', 'products.min_price'),
        ]);
    }

    public function getProductForDetails(Product $product): Product
    {
        return $product->load([
            'category.parent',
            'brand',
            'variants.optionValues.option',
            'variants.images' => fn($q) => $q->orderBy('sort_order'),
            'images' => fn($q) => $q->whereNull('product_variant_id')->orderBy('sort_order'),
            'tags',
            'relatedProducts' => fn($q) => $q->select('products.id', 'products.name', 'products.thumbnail', 'products.min_price'),
            'reviews' => fn($q) => $q->latest()->with('user:id,name,avatar'),
        ]);
    }

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
        $query = Product::active()->select('id', 'name', 'min_price', 'thumbnail');

        if ($excludeId) $query->where('id', '!=', $excludeId);

        return $query->get();
    }

    public function getProductOptions()
    {
        return ProductOption::with('values')->orderBy('sort_order')->get();
    }
}
