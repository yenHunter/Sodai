<?php

namespace Database\Factories;

use App\Models\CmsPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class CmsPageFactory extends Factory
{
    protected $model = CmsPage::class;

    public function definition(): array
    {
        $slug = $this->faker->unique()->randomElement(CmsPage::SLUGS);

        return [
            'slug' => $slug,
            'title' => CmsPage::defaultTitleFor($slug),
            'content' => '<p>'.$this->faker->paragraphs(3, true).'</p>',
        ];
    }

    public function privacyPolicy(): static
    {
        return $this->state(fn () => [
            'slug' => 'privacy-policy',
            'title' => CmsPage::defaultTitleFor('privacy-policy'),
        ]);
    }

    public function empty(): static
    {
        return $this->state(fn () => ['content' => null]);
    }
}
