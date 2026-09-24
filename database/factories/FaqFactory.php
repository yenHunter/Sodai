<?php

namespace Database\Factories;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class FaqFactory extends Factory
{
    protected $model = Faq::class;

    public function definition(): array
    {
        return [
            'faq_category_id' => FaqCategory::factory(),
            'question' => $this->faker->sentence(8).'?',
            'answer' => $this->faker->paragraph(3),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
