<?php

namespace App\Services;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $repository
    ) {}

    public function createProductWithVariants(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Main Product
            $product = $this->repository->create([
                'vendor_id' => $data['vendor_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            // 2. Create Variants
            if (!empty($data['variants'])) {
                foreach ($data['variants'] as $variant) {
                    $this->repository->addVariant($product, $variant);
                }
            }

            return $product;
        });
    }
}
