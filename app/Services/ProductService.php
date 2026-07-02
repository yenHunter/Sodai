<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function getProductsForIndex()
    {
        return Product::with(['category'])
            ->latest()
            ->get();
    }

    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $thumbnailPath = null;
            if (!empty($data['thumbnail'])) {
                $thumbnailPath = $this->uploadThumbnail($data['thumbnail']);
            }

            $product = Product::create([
                'name' => $data['name'],
                'slug' => $this->generateUniqueSlug($data['name']),
                'sku' => $this->generateUniqueSku($data['sku'] ?? null),
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'price' => $data['price'] ?? 0,
                'sale_price' => $data['sale_price'] ?? null,
                'stock_quantity' => $data['stock_quantity'] ?? 0,
                'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
                'thumbnail' => $thumbnailPath,
                'weight' => $data['weight'] ?? null,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? true),
                'is_featured' => $this->resolveIsActive($data['is_featured'] ?? false),
                'meta' => $data['meta'] ?? null,
            ]);

            $this->syncTags($product, $data['tags'] ?? null);

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $thumbnailPath = $product->thumbnail;
            if (!empty($data['thumbnail'])) {
                $this->deleteThumbnail($product->thumbnail);
                $thumbnailPath = $this->uploadThumbnail($data['thumbnail']);
            }

            $slug = $product->slug;
            if ($product->name !== ($data['name'] ?? $product->name)) {
                $slug = $this->generateUniqueSlug($data['name'], $product->id);
            }

            $sku = $product->sku;
            if (($data['sku'] ?? null) && $product->sku !== $data['sku']) {
                $sku = $this->generateUniqueSku($data['sku'], $product->id);
            }

            $product->update([
                'name' => $data['name'] ?? $product->name,
                'slug' => $slug,
                'sku' => $sku,
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'price' => $data['price'] ?? 0,
                'sale_price' => $data['sale_price'] ?? null,
                'stock_quantity' => $data['stock_quantity'] ?? 0,
                'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
                'thumbnail' => $thumbnailPath,
                'weight' => $data['weight'] ?? null,
                'is_active' => $this->resolveIsActive($data['is_active'] ?? $product->is_active),
                'is_featured' => $this->resolveIsActive($data['is_featured'] ?? $product->is_featured),
                'meta' => $data['meta'] ?? null,
            ]);

            $this->syncTags($product, $data['tags'] ?? null);

            return $product->fresh();
        });
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            $this->deleteThumbnail($product->thumbnail);
            $product->tags()->detach();
            $product->relatedProducts()->detach();
            $product->images()->delete();

            return $product->delete();
        });
    }

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

    private function uploadThumbnail(UploadedFile $image): string
    {
        $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('products', $filename, 'public');

        if (!$path) {
            throw new \Exception('Failed to upload product thumbnail.');
        }

        return $path;
    }

    private function deleteThumbnail(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function syncTags(Product $product, mixed $tags): void
    {
        if (is_array($tags)) {
            $tagNames = array_filter(array_map('trim', $tags));
        } else {
            $tagNames = array_filter(array_map('trim', preg_split('/[\s,]+/', (string) $tags ?? '')));
        }

        $tagIds = [];
        foreach ($tagNames as $tagName) {
            $slug = Str::slug($tagName);
            $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $tagName, 'slug' => $slug]);
            $tagIds[] = $tag->id;
        }

        $product->tags()->sync($tagIds);
    }

    private function resolveIsActive(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value)) {
            return $value === 1;
        }

        if (is_string($value)) {
            return strtolower($value) === 'active' || strtolower($value) === 'true' || strtolower($value) === '1';
        }

        return false;
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'product';
        $original = $slug;
        $count = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $slug = $original . '-' . $count++;
        }
    }

    private function generateUniqueSku(?string $sku, ?int $ignoreId = null): string
    {
        $baseSku = strtoupper(trim($sku ?? 'SKU-' . now()->timestamp));
        $skuValue = $baseSku;
        $count = 1;

        while (true) {
            $query = Product::where('sku', $skuValue);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $skuValue;
            }

            $skuValue = $baseSku . '-' . $count++;
        }
    }

    public function getCategoriesForForm()
    {
        return Category::active()->ordered()->get();
    }
}
