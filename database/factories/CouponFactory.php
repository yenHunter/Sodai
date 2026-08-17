<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code'                 => strtoupper($this->faker->unique()->bothify('SAVE##??')),
            'type'                 => 'percentage',
            'value'                => 10,
            'minimum_order_amount' => 0,
            'usage_per_user'       => 1,
            'used_count'           => 0,
            'is_active'            => true,
        ];
    }

    public function fixed(): static
    {
        return $this->state(fn () => ['type' => 'fixed', 'value' => 20]);
    }

    public function used(int $count = 1): static
    {
        return $this->state(fn () => ['used_count' => $count]);
    }
}