<?php

namespace Database\Factories;

use App\Models\Refund;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefundFactory extends Factory
{
    protected $model = Refund::class;

    public function definition(): array
    {
        return [
            'refund_number' => 'REF-' . str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'order_id'      => Order::factory(),
            'amount'        => 50,
            'reason'        => $this->faker->sentence(),
            'status'        => 'pending',
        ];
    }
}