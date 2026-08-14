<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'category_id'    => Category::factory(),
            'name'           => ucfirst($name),
            'slug'           => Str::slug($name),
            'is_active'      => true,
            'is_featured'    => false,
            'total_sales'    => 0,
            'average_rating' => 0,
            'review_count'   => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            if ($product->variants()->exists()) {
                return;
            }

            ProductVariant::factory()->create([
                'product_id' => $product->id,
                'is_default' => true,
            ]);

            $product->refreshPriceAndStockCache();
        });
    }

    public function outOfStock(): static
    {
        return $this->afterCreating(function (Product $product) {
            $product->variants()->update(['stock_quantity' => 0]);
            $product->refreshPriceAndStockCache();
        });
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}