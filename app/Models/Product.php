<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'category_id',
        'brand_id',
        'thumbnail',
        'is_active',
        'is_featured',
        'min_price',
        'max_price',
        'total_stock',
        'average_rating',
        'review_count',
        'total_sales',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'min_price'            => 'decimal:2',
            'max_price'            => 'decimal:2',
            'total_stock'          => 'integer',
            'is_active'            => 'boolean',
            'is_featured'          => 'boolean',
            'average_rating'       => 'decimal:2',
            'review_count'         => 'integer',
            'total_sales'          => 'integer',
            'meta'                 => 'array',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag')
            ->withTimestamps();
    }

    public function relatedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_relations',
            'product_id',
            'related_product_id'
        )->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function defaultVariant()
    {
        return $this->hasOne(ProductVariant::class)->where('is_default', true);
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    public function getFinalPriceAttribute(): float
    {
        return (float) ($this->defaultVariant?->final_price ?? $this->min_price);
    }

    public function getDiscountAmountAttribute(): float
    {
        return (float) ($this->price - $this->final_price);
    }

    public function getDiscountPercentageAttribute(): float
    {
        if ($this->price <= 0) return 0;
        return (float) (($this->discount_amount / $this->price) * 100);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->total_stock > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity > 0
            && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->total_stock <= 0;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->discount_amount > 0;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }

        if ($this->primaryImage) {
            return asset('storage/' . $this->primaryImage->image_path);
        }

        return null;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->is_out_of_stock) return 'Out of Stock';
        if ($this->is_low_stock) return 'Low Stock';
        return 'In Stock';
    }

    public function getHasVariantsAttribute(): bool
    {
        // "Real" variants = more than just the auto-created default one,
        // i.e. the product actually has option values attached.
        return $this->variants()->whereHas('optionValues')->exists();
    }

    public function getPriceRangeLabelAttribute(): string
    {
        if ((float) $this->min_price === (float) $this->max_price) {
            return '$' . number_format((float) $this->min_price, 2);
        }

        return '$' . number_format((float) $this->min_price, 2)
            . ' – $' . number_format((float) $this->max_price, 2);
    }

    /**
     * Human-readable reason why this product cannot be deleted.
     * Returns null if the product is safe to delete.
     * Used by controller to display specific error messages.
     */
    public function getDeletionBlockReasonAttribute(): ?string
    {
        if (
            Schema::hasTable('order_items') &&
            DB::table('order_items')->where('product_id', $this->id)->exists()
        ) {
            return 'This product has existing order history and cannot be deleted.';
        }

        if (
            Schema::hasTable('cart_items') &&
            DB::table('cart_items')->where('product_variant_id', $this->id)->exists()
        ) {
            return 'This product is currently in customer carts and cannot be deleted.';
        }

        return null;
    }

    /**
     * Recalculates min_price/max_price/total_stock from active variants.
     * Call this after any variant create/update/delete.
     */
    public function refreshPriceAndStockCache(): void
    {
        $variants = $this->variants()->active()->get();

        $this->update([
            'min_price'   => $variants->min('price') ?? 0,
            'max_price'   => $variants->max('price') ?? 0,
            'total_stock' => $variants->sum('stock_quantity'),
        ]);
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    public function hasTag(string $tagName): bool
    {
        return $this->tags()->where('name', $tagName)->exists();
    }

    /**
     * Determine if product is safe to delete.
     * Uses Schema::hasTable() checks so this remains
     * forward-compatible as Order/Cart modules are built later
     * without requiring code changes here.
     */
    public function canDelete(): bool
    {
        return is_null($this->deletion_block_reason);
    }

    public function decrementStock(int $quantity): bool
    {
        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        return true;
    }

    public function incrementStock(int $quantity): void
    {
        $this->increment('stock_quantity', $quantity);
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)
            ->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('total_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('total_stock', '<=', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->where('stock_quantity', '>', 0);
    }

    public function scopeWithDiscount($query)
    {
        return $query->whereNotNull('discount_type')
            ->whereNotNull('discount_value')
            ->where('discount_value', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('short_description', 'like', "%{$search}%");
        });
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->where(function ($q) use ($min, $max) {
            $q->whereBetween('min_price', [$min, $max])
                ->orWhereBetween('max_price', [$min, $max])
                ->orWhere(function ($q2) use ($min, $max) {
                    $q2->where('min_price', '<=', $min)->where('max_price', '>=', $max);
                });
        });
    }

    public function scopeOfColor($query, $colors)
    {
        return $query->whereIn('color', (array) $colors);
    }

    public function scopeOfSize($query, $sizes)
    {
        return $query->whereIn('size', (array) $sizes);
    }

    public function scopeNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('total_sales', 'desc');
    }

    public function scopeTopRated($query)
    {
        return $query->where('review_count', '>', 0)
            ->orderBy('average_rating', 'desc');
    }
}
