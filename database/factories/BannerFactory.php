<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'subtitle' => 'Sale Offer',
            'description' => $this->faker->sentence(10),
            'button_text' => 'Order Now',
            'button_url' => '/products',
            'button_target' => '_self',
            'image' => 'banners/fake-banner.jpg',
            'position' => 'home_slider',
            'text_position' => 'left',
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function position(string $position): static
    {
        return $this->state(fn () => ['position' => $position]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
