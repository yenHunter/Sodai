<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
            CustomerSeeder::class,
            AttributeSeeder::class,
            CmsPageSeeder::class,
            FaqCategorySeeder::class,
        ]);

        // Playwright-only account. Requires an explicit opt-in so normal local
        // and shared environments never receive a known-credential admin.
        if (filter_var(env('E2E_SEED', false), FILTER_VALIDATE_BOOL)) {
            $this->call(E2eSeeder::class);
        }
    }
}
