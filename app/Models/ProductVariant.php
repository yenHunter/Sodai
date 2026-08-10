<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'purchase_price',
        'discount_type',
        'discount_value',
        'stock_quantity',
        'low_stock_threshold',
        'weight',
        'weight_unit',
        'thumbnail',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'                => 'decimal:2',
            'purchase_price'       => 'decimal:2',
            'discount_value'       => 'decimal:2',
            'stock_quantity'       => 'integer',
            'low_stock_threshold'  => 'integer',
            'weight'               => 'decimal:2',
            'is_default'           => 'boolean',
            'is_active'            => 'boolean',
        ];
    }

    // ─────────────────────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues()
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_option_values'
        )->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    // ─────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────

    public function getFinalPriceAttribute(): float
    {
        if (!$this->discount_type || !$this->discount_value) {
            return (float) $this->price;
        }

        if ($this->discount_type === 'percentage') {
            return (float) ($this->price - ($this->price * $this->discount_value / 100));
        }

        return (float) max(0, $this->price - $this->discount_value);
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->stock_quantity > 0;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity > 0 && $this->stock_quantity <= $this->low_stock_threshold;
    }

    public function getIsOutOfStockAttribute(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * "Color: Red, Size: M" — used for order snapshots and cart display.
     */
    public function getOptionsLabelAttribute(): string
    {
        return $this->optionValues
            ->load('option')
            ->map(fn ($v) => "{$v->option->name}: {$v->value}")
            ->implode(', ');
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

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

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }
}