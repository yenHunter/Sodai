<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Support\DemoEnvironment;
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
        // A public demo runs with APP_ENV=production, so production alone cannot
        // be the blocker. DEMO_MODE=true is the deliberate opt-in; the seeder
        // still refuses to create accounts without SEED_DEMO_PASSWORD.
        if (app()->environment('production') && ! DemoEnvironment::enabled()) {
            $this->components->error('demo:seed is disabled in production unless DEMO_MODE=true (see docs/demo-mode.md).');

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

        // The seeder reports its skip reasons with a warning and a normal
        // return, which `db:seed` turns into exit 0. A deploy script must not
        // read "skipped" as success, so require the catalog to actually exist.
        if (! Product::whereIn('slug', DemoCatalogSeeder::productSlugs())->exists()) {
            $this->components->error('The demo catalog was not seeded. See the seeder message above; in production it needs DEMO_MODE=true and SEED_DEMO_PASSWORD.');

            return self::FAILURE;
        }

        $this->components->info('Run `php artisan demo:clear --dry-run` to preview the undo, or `demo:clear` to apply it.');

        return self::SUCCESS;
    }
}
