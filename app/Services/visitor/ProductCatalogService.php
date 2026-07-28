<?php

namespace App\Services\Visitor;

use App\Models\Category;
use App\Models\Product;
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
     * @param Category|null $scopeCategory When set, restricts results to this category (and its children, if it's a parent).
     */
    public function getFilteredProducts(array $filters, ?Category $scopeCategory = null)
    {
        $query = Product::query()
            ->active()
            ->inStock()
            ->with(['category', 'brand', 'primaryImage']);

        if ($scopeCategory) {
            $categoryIds = $scopeCategory->isParent()
                ? $scopeCategory->children()->pluck('id')
                : collect([$scopeCategory->id]);

            $query->whereIn('category_id', $categoryIds);
        }

        if (!empty($filters['category'])) {
            $query->whereIn('category_id', (array) $filters['category']);
        }

        if (!empty($filters['color'])) {
            $query->ofColor($filters['color']);
        }

        if (!empty($filters['size'])) {
            $query->ofSize($filters['size']);
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

    private function applySort($query, string $sort): void
    {
        match ($sort) {
            'name_asc'   => $query->orderBy('name', 'asc'),
            'name_desc'  => $query->orderBy('name', 'desc'),
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->latest(), // relevance / position placeholder
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
        return $this->baseAttributeQuery($scopeCategory)
            ->whereNotNull('color')
            ->distinct()
            ->orderBy('color')
            ->pluck('color')
            ->toArray();
    }

    public function getAvailableSizes(?Category $scopeCategory = null): array
    {
        return $this->baseAttributeQuery($scopeCategory)
            ->whereNotNull('size')
            ->distinct()
            ->orderBy('size')
            ->pluck('size')
            ->toArray();
    }

    public function getPriceBounds(?Category $scopeCategory = null): array
    {
        $query = $this->baseAttributeQuery($scopeCategory);

        return [
            'min' => (float) ($query->clone()->min('price') ?? 0),
            'max' => (float) ($query->clone()->max('price') ?? 1000),
        ];
    }

    private function baseAttributeQuery(?Category $scopeCategory = null)
    {
        $query = Product::query()->active()->inStock();

        if ($scopeCategory) {
            $categoryIds = $scopeCategory->isParent()
                ? $scopeCategory->children()->pluck('id')
                : collect([$scopeCategory->id]);

            $query->whereIn('category_id', $categoryIds);
        }

        return $query;
    }
}