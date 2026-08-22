<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number' => 'ORD-'.str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'status' => 'pending',
            'subtotal' => 100,
            'discount_amount' => 0,
            'shipping_charge' => 0,
            'tax_amount' => 0,
            'total_amount' => 100,
            'shipping_name' => $this->faker->name(),
            'shipping_email' => $this->faker->safeEmail(),
            'shipping_phone' => $this->faker->phoneNumber(),
            'shipping_address' => $this->faker->streetAddress(),
            'shipping_city' => $this->faker->city(),
            'shipping_state' => $this->faker->state(),
            'shipping_zip' => $this->faker->postcode(),
            'shipping_country' => 'Bangladesh',
        ];
    }

    public function withStatus(string $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
