<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FaqCategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaultCategories = [
            'Orders',
            'Shipping',
            'Payment',
            'Returns & Refunds',
            'Account',
            'Products',
        ];

        foreach ($defaultCategories as $index => $name) {
            // firstOrCreate — safe to re-run on a populated production DB,
            // consistent with the RolePermissionSeeder fix and the
            // CmsPageSeeder pattern already used in this project.
            FaqCategory::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }
}
