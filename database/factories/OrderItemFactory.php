<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $unitPrice = $this->faker->randomFloat(2, 10, 200);
        $quantity  = $this->faker->numberBetween(1, 3);

        return [
            'order_id'      => Order::factory(),
            'product_id'    => Product::factory(),
            'product_name'  => $this->faker->words(2, true),
            'product_sku'   => strtoupper($this->faker->bothify('SKU-####')),
            'unit_price'    => $unitPrice,
            'quantity'      => $quantity,
            'total_price'   => round($unitPrice * $quantity, 2),
        ];
    }
}