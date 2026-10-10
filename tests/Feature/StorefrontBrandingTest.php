<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorefrontBrandingTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────
    // DEFAULTS (fresh store, settings table empty)
    // ─────────────────────────────────────────────

    public function test_head_renders_template_default_branding_without_settings(): void
    {
        $response = $this->get(route('visitor.about'));

        $response->assertOk();
        // Company settings feed the title; about page supplies its own title text.
        $response->assertSee('<title>Sodai - Single vendor eCommerce - About Us</title>', false);
        $response->assertSee(asset('visitor/images/favicon/favicon-4.png'), false);
        // Template defaults render when nothing is configured
        $response->assertSee(asset('visitor/images/logo/logo.png'), false);
        $response->assertSee(asset('visitor/images/logo/dark-logo.png'), false);
    }

    public function test_header_and_footer_render_template_default_logos_and_contact_without_settings(): void
    {
        $response = $this->get(route('visitor.about'));

        $response->assertOk();
        $response->assertSee(asset('visitor/images/logo/logo.png'), false);
        $response->assertSee(asset('visitor/images/logo/dark-logo.png'), false);
        $response->assertSee('71 Pilgrim Avenue Chevy Chase, east california.');
        $response->assertSee('+44 0123 456 789');
        $response->assertSee('example@ec-email.com');
    }

    // ─────────────────────────────────────────────
    // ADMIN-CONFIGURED VALUES OVERRIDE DEFAULTS
    // ─────────────────────────────────────────────

    public function test_head_uses_admin_seo_and_company_settings(): void
    {
        Setting::setMany('company', [
            'name' => 'Sodai Boutique',
            'tagline' => 'Shop Smart',
        ]);
        Setting::setMany('seo', [
            'meta_title' => 'Sodai Boutique — Shop online',
            'meta_description' => 'Best fashion store in town.',
            'meta_keywords' => 'fashion, sodai, shop',
        ]);

        $response = $this->get(route('visitor.about'));

        $response->assertOk();
        $response->assertSee('<title>Sodai Boutique - About Us</title>', false);
        $response->assertSee('<meta name="author" content="Sodai Boutique">', false);
        $response->assertSee('<meta name="description" content="Best fashion store in town.">', false);
        $response->assertSee('content="fashion, sodai, shop"', false);
        $response->assertSee('<meta name="author" content="Sodai Boutique">', false);
        // Footer copyright site name comes from company name, not template default
        $response->assertSee('>Sodai Boutique<span>.</span></a>. All Rights', false);
        $response->assertDontSee('>ekka<span>.</span></a>', false);
    }

    public function test_uploaded_logo_and_favicon_override_template_defaults(): void
    {
        // Emulate what SettingService::uploadFile() stores: "settings/<group>/<uuid>.<ext>"
        Storage::fake('public');
        Storage::disk('public')->put('settings/design/test.png', 'x');
        Storage::disk('public')->put('settings/design/dark-test.png', 'x');
        Storage::disk('public')->put('settings/design/favicon-test.png', 'x');

        Setting::setMany('design', [
            'logo' => 'settings/design/test.png',
            'logo_dark' => 'settings/design/dark-test.png',
            'favicon' => 'settings/design/favicon-test.png',
        ]);

        $response = $this->get(route('visitor.about'));

        $response->assertOk();
        $response->assertSee(asset('storage/settings/design/test.png'), false);
        $response->assertSee(asset('storage/settings/design/dark-test.png'), false);
        $response->assertSee(asset('storage/settings/design/favicon-test.png'), false);
        // Template fallbacks are gone
        $response->assertDontSee(asset('visitor/images/logo/logo.png'), false);
        $response->assertDontSee(asset('visitor/images/favicon/favicon-4.png'), false);
    }

    public function test_footer_uses_admin_company_contact_details(): void
    {
        Setting::setMany('company', [
            'name' => 'Sodai Boutique',
            'address' => '12 Gulshan Avenue, Dhaka',
            'phone' => '+8801712345678',
            'email' => 'care@sodai.test',
        ]);

        $response = $this->get(route('visitor.about'));

        $response->assertOk();
        $response->assertSee('12 Gulshan Avenue, Dhaka');
        $response->assertSee('+8801712345678');
        $response->assertSee('mailto:care@sodai.test');
        $response->assertDontSee('71 Pilgrim Avenue');
        $response->assertDontSee('+44 0123 456 789');
        $response->assertDontSee('example@ec-email.com');
    }

    // ─────────────────────────────────────────────
    // SIDEBAR TOGGLE: HOME-ONLY VISIBILITY
    // ─────────────────────────────────────────────

    public function test_sidebar_toggle_button_shows_on_home_page(): void
    {
        $response = $this->get(route('visitor.index'));

        $response->assertOk();
        $response->assertSee('ec-sidebar-toggle', false);
    }

    public function test_sidebar_toggle_button_hidden_on_about_products_and_other_pages(): void
    {
        $response = $this->get(route('visitor.about'));
        $response->assertOk();
        $response->assertDontSee('ec-sidebar-toggle', false);

        $product = Product::factory()->create();
        $response = $this->get(route('visitor.products.show', $product));
        $response->assertOk();
        $response->assertDontSee('ec-sidebar-toggle', false);

        $response = $this->get(route('visitor.products.index'));
        $response->assertOk();
        $response->assertDontSee('ec-sidebar-toggle', false);

        $response = $this->get(route('visitor.contact'));
        $response->assertOk();
        $response->assertDontSee('ec-sidebar-toggle', false);

        $response = $this->get(route('visitor.offers'));
        $response->assertOk();
        $response->assertDontSee('ec-sidebar-toggle', false);
    }
}
