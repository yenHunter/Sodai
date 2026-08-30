<?php

namespace App\Services\Visitor;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;

class HomeService
{
    public function getBanners(string $position, int $limit = 5)
    {
        return Banner::currentlyValid()
            ->ofPosition($position)
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function getFeaturedProducts(int $limit = 8)
    {
        return Product::featured()
            ->inStock()
            ->with(['category', 'brand', 'primaryImage', 'defaultVariant'])
            ->limit($limit)
            ->get();
    }

    public function getNewArrivals(int $limit = 8)
    {
        return Product::active()
            ->inStock()
            ->newest()
            ->with(['category', 'brand', 'primaryImage', 'defaultVariant'])
            ->limit($limit)
            ->get();
    }

    public function getTopCategories(int $limit = 4)
    {
        return Category::active()
            ->parentOnly()
            ->ordered()
            ->withCount(['products' => fn ($q) => $q->active()->inStock()])
            ->limit($limit)
            ->get();
    }

    public function getTopRatedProducts(int $limit = 6)
    {
        return Product::active()
            ->inStock()
            ->topRated()
            ->with(['category', 'brand', 'primaryImage', 'defaultVariant'])
            ->limit($limit)
            ->get();
    }

    public function getFeatureItems(int $limit = 2)
    {
        return Product::featured()
            ->inStock()
            ->with(['category', 'brand', 'primaryImage', 'defaultVariant'])
            ->limit($limit)
            ->get();
    }

    public function getLimitedTimeOffers(int $limit = 2)
    {
        return Product::active()
            ->inStock()
            // ->withDiscount()
            ->with(['category', 'brand', 'primaryImage', 'defaultVariant'])
            ->limit($limit)
            ->get();
    }
}
