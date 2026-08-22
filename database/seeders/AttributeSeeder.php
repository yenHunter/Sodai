<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            ['key' => 'color',  'label' => 'Color',  'sort_order' => 1],
            ['key' => 'size',   'label' => 'Size',   'sort_order' => 2],
            ['key' => 'weight', 'label' => 'Weight',  'sort_order' => 3],
        ];

        foreach ($attributes as $attribute) {
            Attribute::firstOrCreate(['key' => $attribute['key']], $attribute + ['status' => 'active']);
        }
    }
}
