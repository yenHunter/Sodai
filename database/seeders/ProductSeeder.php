<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get the Vendor we created earlier
        $vendor = User::where('email', 'vendor@nike.com')->first();

        if (!$vendor) return;

        // REALISTIC DATASET
        $products = [
            [
                'name' => 'Nike Air Force 1 \'07',
                'description' => 'The radiance lives on in the Nike Air Force 1 \'07, the basketball original that puts a fresh spin on what you know best: durably stitched overlays, clean finishes and the perfect amount of flash to make you shine.',
                'variants' => [
                    ['sku' => 'AF1-WHT-08', 'size' => 'US 8', 'color' => 'White', 'price' => 110.00, 'stock' => 50],
                    ['sku' => 'AF1-WHT-09', 'size' => 'US 9', 'color' => 'White', 'price' => 110.00, 'stock' => 40],
                    ['sku' => 'AF1-WHT-10', 'size' => 'US 10', 'color' => 'White', 'price' => 110.00, 'stock' => 25],
                    ['sku' => 'AF1-BLK-09', 'size' => 'US 9', 'color' => 'Black', 'price' => 110.00, 'stock' => 60],
                ]
            ],
            [
                'name' => 'Nike Air Max 90',
                'description' => 'Nothing as fly, nothing as comfortable, nothing as proven. The Nike Air Max 90 stays true to its OG running roots with the iconic Waffle sole, stitched overlays and classic TPU accents.',
                'variants' => [
                    ['sku' => 'AM90-GRY-09', 'size' => 'US 9', 'color' => 'Wolf Grey', 'price' => 130.00, 'stock' => 15],
                    ['sku' => 'AM90-RED-10', 'size' => 'US 10', 'color' => 'University Red', 'price' => 130.00, 'stock' => 10],
                ]
            ],
            [
                'name' => 'Nike Tech Fleece Hoodie',
                'description' => 'Smooth on both sides, Tech Fleece offers premium warmth and an elevated look without adding weight or bulk.',
                'variants' => [
                    ['sku' => 'TF-HOOD-M', 'size' => 'M', 'color' => 'Heather Grey', 'price' => 120.00, 'stock' => 100],
                    ['sku' => 'TF-HOOD-L', 'size' => 'L', 'color' => 'Heather Grey', 'price' => 120.00, 'stock' => 80],
                    ['sku' => 'TF-HOOD-XL', 'size' => 'XL', 'color' => 'Heather Grey', 'price' => 125.00, 'stock' => 50],
                ]
            ]
        ];

        foreach ($products as $p) {
            // Create Product
            $product = Product::create([
                'vendor_id' => $vendor->id,
                'name' => $p['name'],
                'slug' => Str::slug($p['name']),
                'description' => $p['description'],
                'status' => 'active',
            ]);

            // Create Variants
            foreach ($p['variants'] as $v) {
                $product->variants()->create([
                    'sku' => $v['sku'],
                    'size' => $v['size'],
                    'color' => $v['color'],
                    'price' => $v['price'],
                    'stock_quantity' => $v['stock'],
                ]);
            }
        }
    }
}
