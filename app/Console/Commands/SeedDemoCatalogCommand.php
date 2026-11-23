<?php

namespace App\Console\Commands;

use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Console\Command;

/**
 * The friendly front door to DemoCatalogSeeder, so the demo is seeded from
 * `php artisan demo:seed` rather than a fully qualified class name and has the
 * same guard and confirmation as its undo, `demo:clear`.
 */
class SeedDemoCatalogCommand extends Command
{
    protected $signature = 'demo:seed
        {--force : Seed without asking for confirmation}';

    protected $description = 'Seed the demo catalog: products, orders, reviews, banners and accounts';

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->components->error('demo:seed is disabled in production, same as the demo seeder.');

            return self::FAILURE;
        }

        $database = (string) config('database.connections.'.config('database.default').'.database');

        if (! $this->option('force') && ! $this->confirm("Seed the demo catalog into \"{$database}\"?", false)) {
            $this->components->info('Nothing seeded.');

            return self::SUCCESS;
        }

        // The seeder owns its own environment and password guards and reports
        // them; this command only relays its output.
        $this->call('db:seed', [
            '--class' => DemoCatalogSeeder::class,
            '--force' => true,
        ]);

        $this->components->info('Run `php artisan demo:clear --dry-run` to preview the undo, or `demo:clear` to apply it.');

        return self::SUCCESS;
    }
}
