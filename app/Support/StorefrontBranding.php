<?php

namespace App\Support;

/**
 * Resolves the storefront's branding from admin-managed settings, falling
 * back to the template assets currently used when a setting is unset.
 * Kept as static methods so Blade can embed the deep-merged arrays without
 * any controller-layer plumbing.
 */
class StorefrontBranding
{
    /**
     * Common site settings for every visitor page (title, social, brand name).
     *
     * @return array{name: string, logo: string, logo_dark: string, favicon: string, description: string, keywords: string, author: string, copyright_name: string}
     */
    public static function common(): array
    {
        $defaults = [
            'name' => 'Sodai',
            'description' => 'Best ecommerce html template for single and multi vendor store.',
            'keywords' => 'apparel, catalog, clean, ecommerce, ecommerce HTML, electronics, fashion, html eCommerce, html store, minimal, multipurpose, multipurpose ecommerce, online store, responsive ecommerce template, shops',
            'author' => 'Sodai',
            'logo' => asset('visitor/images/logo/logo.png'),
            'logo_dark' => asset('visitor/images/logo/dark-logo.png'),
            'favicon' => asset('visitor/images/favicon/favicon-4.png'),
            'copyright_name' => 'ekka',
        ];

        $name = setting('company', 'name', config('app.name', 'Sodai'));

        return array_merge($defaults, [
            'name' => $name,
            // Storefront page author = the store itself (admin-managed via company name).
            'description' => setting('seo', 'meta_description', $defaults['description']),
            'keywords' => setting('seo', 'meta_keywords', $defaults['keywords']),
            'author' => $name,
            'logo' => self::imageUrl('design', 'logo', $defaults['logo']),
            'logo_dark' => self::imageUrl('design', 'logo_dark', $defaults['logo_dark']),
            'favicon' => self::imageUrl('design', 'favicon', $defaults['favicon']),
            'copyright_name' => $name,
        ]);
    }

    /**
     * Header/footer logo URLs, reusing the common() resolution.
     *
     * @return array{logo: string, logo_dark: string}
     */
    public static function logos(): array
    {
        $common = self::common();

        return [
            'logo' => $common['logo'],
            'logo_dark' => $common['logo_dark'],
        ];
    }

    /**
     * Resolve a settings-image key to a public asset URL.
     */
    private static function imageUrl(string $group, string $key, string $fallback): string
    {
        return self::imagePath($group, $key) !== null
            ? asset('storage/'.self::imagePath($group, $key))
            : $fallback;
    }

    /**
     * Raw storage path for a settings image — null when unset.
     */
    public static function imagePath(string $group, string $key): ?string
    {
        $path = setting($group, $key);

        return is_string($path) && $path !== '' ? $path : null;
    }
}
