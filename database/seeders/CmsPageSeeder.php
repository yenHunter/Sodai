<?php

namespace Database\Seeders;

use App\Models\CmsPage;
use Illuminate\Database\Seeder;

class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        foreach (CmsPage::SLUGS as $slug) {
            CmsPage::firstOrCreate(
                ['slug' => $slug],
                ['title' => CmsPage::defaultTitleFor($slug)]
            );
        }
    }
}
