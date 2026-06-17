<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'sku', 'short_description',
        'description', 'category_id', 'price', 'sale_price',
        'stock_quantity', 'low_stock_threshold', 'thumbnail',
        'weight', 'is_active', 'is_featured', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'price'          => 'decimal:2',
            'sale_price'     => 'decimal:2',
            'average_rating' => 'decimal:2',
            'is_active'      => 'boolean',
            'is_featured'    => 'boolean',
            'meta'           => 'array',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
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
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)
                    ->where('status', 'approved');
    }

    // Manually selected related products (bidirectional)
    public function relatedProducts()
    {
        return $this->belongsToMany(
            Product::class,
            'product_relations',
            'product_id',
            'related_product_id'
        );
    }

    // ─────────────────────────────────────────────
    // SMART RELATED PRODUCTS METHOD
    // ─────────────────────────────────────────────

    /**
     * Get related products using hybrid approach:
     * 1. First use manually selected (admin picked)
     * 2. Fill remaining slots with same category + shared tags
     */
    public function getSmartRelatedProducts(int $limit = 8)
    {
        // Step 1: Get manually selected related products
        $manualRelated = $this->relatedProducts()
                              ->active()
                              ->inStock()
                              ->limit($limit)
                              ->get();

        if ($manualRelated->count() >= $limit) {
            return $manualRelated;
        }

        // Step 2: Fill remaining with automatic suggestions
        $remaining   = $limit - $manualRelated->count();
        $excludeIds  = $manualRelated->pluck('id')->push($this->id);
        $tagIds      = $this->tags->pluck('id');

        // By shared tags (most relevant first)
        $autoRelated = Product::active()
            ->inStock()
            ->whereNotIn('id', $excludeIds)
            ->where(function ($query) use ($tagIds) {
                // Same category
                $query->where('category_id', $this->category_id);

                // OR shared tags (if product has tags)
                if ($tagIds->isNotEmpty()) {
                    $query->orWhereHas('tags', function ($q) use ($tagIds) {
                        $q->whereIn('tags.id', $tagIds);
                    });
                }
            })
            ->limit($remaining)
            ->get();

        return $manualRelated->merge($autoRelated);
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    public function getCurrentPriceAttribute(): float
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return !is_null($this->sale_price)
               && $this->sale_price < $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->is_on_sale) return 0;

        return (int)(
            (($this->price - $this->sale_price) / $this->price) * 100
        );
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->low_stock_threshold
               && $this->stock_quantity > 0;
    }

    // ─────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}