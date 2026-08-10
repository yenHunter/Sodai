<?php

namespace App\Services\Visitor;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductOptionValue;
use App\Services\AttributeService;

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
     * @param array         $filters       ['category' => [], 'color' => [], 'size' => [], 'price_min' => ?, 'price_max' => ?, 'sort' => ?]
     * @param Category|null $scopeCategory
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

        if (!empty($filters['category'])) {
            $query->whereIn('category_id', (array) $filters['category']);
        }

        if (!empty($filters['color'])) {
            $this->filterByOptionValue($query, 'color', (array) $filters['color']);
        }

        if (!empty($filters['size'])) {
            $this->filterByOptionValue($query, 'size', (array) $filters['size']);
        }

        if (!empty($filters['price_min']) || !empty($filters['price_max'])) {
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
            'name_asc'   => $query->orderBy('name', 'asc'),
            'name_desc'  => $query->orderBy('name', 'desc'),
            'price_asc'  => $query->orderBy('min_price', 'asc'),
            'price_desc' => $query->orderBy('max_price', 'desc'),
            default      => $query->latest(),
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
        return $scopeCategory->isParent()
            ? $scopeCategory->children()->pluck('id')
            : collect([$scopeCategory->id]);
    }
}