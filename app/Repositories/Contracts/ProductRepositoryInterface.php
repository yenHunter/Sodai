<?php

namespace App\Repositories\Contracts;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function search(string $query, int $perPage);
    public function create(array $data): Product;
    public function addVariant(Product $product, array $variantData);
    public function findBySku(string $sku);
}
