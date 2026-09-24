<?php

namespace Database\Factories;

use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FaqCategoryFactory extends Factory
{
    protected $model = FaqCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Orders', 'Shipping', 'Payment', 'Returns & Refunds', 'Account', 'Products',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 9999),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
