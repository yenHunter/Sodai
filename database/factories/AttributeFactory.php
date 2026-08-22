<?php

namespace Database\Factories;

use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    public function definition(): array
    {
        return [
            'key' => $this->faker->unique()->word(),
            'label' => ucfirst($this->faker->word()),
            'status' => 'active',
        ];
    }
}
