<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Str;

class ProductRepository implements ProductRepositoryInterface
{
    public function search(string $query = '', int $perPage = 20)
    {
        // If query is empty, return latest, else use Scout
        return empty($query) 
            ? Product::with('variants')->latest()->paginate($perPage)
            : Product::search($query)->query(fn ($q) => $q->with('variants'))->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return Product::create([
            'vendor_id' => $data['vendor_id'],
            'name' => $data['name'],
            'slug' => Str::slug($data['name']) . '-' . uniqid(),
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);
    }

    public function addVariant(Product $product, array $variantData)
    {
        return $product->variants()->create($variantData);
    }
    
    public function findBySku(string $sku)
    {
        return ProductVariant::where('sku', $sku)->first();
    }
}
