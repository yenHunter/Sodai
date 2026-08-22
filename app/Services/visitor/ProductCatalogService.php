<?php

namespace App\Services\Visitor;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductOptionValue;
use App\Services\Admin\AttributeService;
use Illuminate\Support\Str;

class ProductCatalogService
{
    private const PER_PAGE = 8;

    public function __construct(
        private AttributeService $attributeService
    ) {}

    public function getActiveAttributeKeys(): array
    {
        return $this->attributeService->getActiveKeys();
    }

    /**
     * @param  array  $filters  ['category' => [], 'color' => [], 'size' => [], 'price_min' => ?, 'price_max' => ?, 'sort' => ?]
     */
    public function getFilteredProducts(array $filters, ?Category $scopeCategory = null)
    {
        $query = Product::query()
            ->active()
            ->inStock() // uses the total_stock cache — see Product::scopeInStock
            ->with([
                'category',
                'brand',
                'primaryImage',
                'defaultVariant',
                'variants' => fn ($v) => $v->active(),
            ]);

        if ($scopeCategory) {
            $query->whereIn('category_id', $this->scopedCategoryIds($scopeCategory));
        }

        if (! empty($filters['category'])) {
            $query->whereIn('category_id', (array) $filters['category']);
        }

        if (! empty($filters['color'])) {
            $this->filterByOptionValue($query, 'color', (array) $filters['color']);
        }

        if (! empty($filters['size'])) {
            $this->filterByOptionValue($query, 'size', (array) $filters['size']);
        }

        if (! empty($filters['price_min']) || ! empty($filters['price_max'])) {
            $bounds = $this->getPriceBounds($scopeCategory);
            $min = $filters['price_min'] ?? $bounds['min'];
            $max = $filters['price_max'] ?? $bounds['max'];
            $query->priceRange($min, $max);
        }

        $this->applySort($query, $filters['sort'] ?? 'relevance');

        return $query->paginate(self::PER_PAGE)->withQueryString();
    }

    private function filterByOptionValue($query, string $optionSlug, array $values): void
    {
        $slugs = array_map(fn ($v) => Str::slug($v), $values);

        $query->whereHas('variants', function ($v) use ($optionSlug, $slugs) {
            $v->active()->whereHas('optionValues', function ($ov) use ($optionSlug, $slugs) {
                $ov->whereIn('slug', $slugs)
                    ->whereHas('option', fn ($o) => $o->where('slug', $optionSlug));
            });
        });
    }

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'price_asc' => $query->orderBy('min_price', 'asc'),
            'price_desc' => $query->orderBy('max_price', 'desc'),
            default => $query->latest(),
        };
    }

    /**
     * Leaf categories only — parent categories never hold products directly.
     */
    public function getFilterableCategories()
    {
        return Category::active()
            ->childOnly()
            ->ordered()
            ->withCount(['products' => fn ($q) => $q->active()->inStock()])
            ->get()
            ->filter(fn ($category) => $category->products_count > 0)
            ->values();
    }

    public function getAvailableColors(?Category $scopeCategory = null): array
    {
        return $this->availableOptionValues('color', $scopeCategory);
    }

    public function getAvailableSizes(?Category $scopeCategory = null): array
    {
        return $this->availableOptionValues('size', $scopeCategory);
    }

    /**
     * Returns distinct option values (e.g. distinct colors) actually in use
     * by active, in-stock variants within the given scope.
     */
    private function availableOptionValues(string $optionSlug, ?Category $scopeCategory = null): array
    {
        $productIds = $this->baseAttributeQuery($scopeCategory)->pluck('id');

        return ProductOptionValue::whereHas('option', fn ($o) => $o->where('slug', $optionSlug))
            ->whereHas('variants', fn ($v) => $v->active()->inStock()->whereIn('product_id', $productIds))
            ->orderBy('sort_order')
            ->pluck('value')
            ->unique()
            ->values()
            ->toArray();
    }

    public function getPriceBounds(?Category $scopeCategory = null): array
    {
        $query = $this->baseAttributeQuery($scopeCategory);

        return [
            'min' => (float) ($query->clone()->min('min_price') ?? 0),
            'max' => (float) ($query->clone()->max('max_price') ?? 1000),
        ];
    }

    private function baseAttributeQuery(?Category $scopeCategory = null)
    {
        $query = Product::query()->active()->inStock();

        if ($scopeCategory) {
            $query->whereIn('category_id', $this->scopedCategoryIds($scopeCategory));
        }

        return $query;
    }

    private function scopedCategoryIds(Category $scopeCategory)
    {
        return $scopeCategory->hasChildren()
            ? $scopeCategory->children()->pluck('id')
            : collect([$scopeCategory->id]);
    }

    /**
     * Single product for the PDP, with everything the variant selector needs.
     */
    public function getProductForDetail(Product $product): Product
    {
        return $product->load([
            'category.parent',
            'brand',
            'variants' => fn ($v) => $v->active()->with('optionValues.option'),
            'variants.images' => fn ($q) => $q->orderBy('sort_order'),
            'images' => fn ($q) => $q->whereNull('product_variant_id')->orderBy('sort_order'),
            'tags',
            'relatedProducts' => fn ($q) => $q->select('products.id', 'products.name', 'products.thumbnail', 'products.min_price'),
            'reviews' => fn ($q) => $q->approved()->latest()->with('user:id,name,avatar'),
        ]);
    }

    /**
     * Builds the JSON payload the frontend variant-picker JS consumes:
     * distinct option groups (Color, Size...) + a flat map of
     * "value_id|value_id" combo -> variant data (price/stock/sku/image).
     * This lets the PDP resolve a selection to one variant client-side
     * without extra requests.
     */
    public function buildVariantMatrix(Product $product): array
    {
        $variants = $product->variants;

        $optionGroups = $variants
            ->flatMap(fn ($v) => $v->optionValues)
            ->groupBy(fn ($ov) => $ov->option->name)
            ->map(function ($values, $optionName) {
                return [
                    'name' => $optionName,
                    'values' => $values->unique('id')->values()->map(fn ($ov) => [
                        'id' => $ov->id,
                        'value' => $ov->value,
                        'swatch' => $ov->swatch,
                    ]),
                ];
            })
            ->values();

        $combinations = $variants->mapWithKeys(function ($variant) {
            $key = $variant->optionValues->pluck('id')->sort()->implode('-');

            return [$key => [
                'variant_id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'final_price' => (float) $variant->final_price,
                'stock_quantity' => $variant->stock_quantity,
                'is_in_stock' => $variant->is_in_stock,
                'is_low_stock' => $variant->is_low_stock,
                'thumbnail_url' => $variant->thumbnail ? asset('storage/'.$variant->thumbnail) : null,
                'is_default' => $variant->is_default,
            ]];
        });

        return [
            'option_groups' => $optionGroups,
            'combinations' => $combinations,
            'default_key' => optional($variants->firstWhere('is_default', true))
                ?->optionValues->pluck('id')->sort()->implode('-') ?? '',
        ];
    }
}
