<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'category_id'          => Category::factory(),
            'name'                 => ucfirst($name),
            'slug'                 => Str::slug($name),
            'sku'                  => strtoupper(Str::random(8)),
            'price'                => $this->faker->randomFloat(2, 10, 500),
            'stock_quantity'       => $this->faker->numberBetween(10, 100),
            'low_stock_threshold'  => 5,
            'is_active'            => true,
            'is_featured'          => false,
            'total_sales'          => 0,
            'average_rating'       => 0,
            'review_count'         => 0,
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => 0]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}