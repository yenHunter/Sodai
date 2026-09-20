<?php

namespace Database\Factories;

use App\Models\Offer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    protected $model = Offer::class;

    public function definition(): array
    {
        return [
            'title' => 'On '.$this->faker->word(),
            'subtitle' => 'Upto '.$this->faker->numberBetween(10, 70).' % off',
            'description' => $this->faker->sentence(12),
            'button_text' => 'Shop Now!',
            'image' => 'offers/fake-offer.jpg',
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
