<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\CustomerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_admins_are_not_seeded_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('db:seed', ['--class' => AdminSeeder::class, '--force' => true])
            ->assertSuccessful();

        $this->assertSame(0, Admin::count());
    }

    public function test_demo_customer_is_not_seeded_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('db:seed', ['--class' => CustomerSeeder::class, '--force' => true])
            ->assertSuccessful();

        $this->assertSame(0, User::count());
    }
}
