<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CmsPage;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Services\Admin\SettingService;
use App\Support\HtmlSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class SecurityHardeningTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    // ─── XSS: rich-text sanitization ───

    public function test_sanitizer_strips_scripting_content_but_keeps_formatting(): void
    {
        $sanitizer = new HtmlSanitizer;

        $clean = $sanitizer->clean(
            '<p style="text-align:center">Keep <strong>bold</strong> and <a href="https://example.com" onclick="steal()">link</a></p>'
            .'<script>alert(1)</script>'
            .'<iframe src="https://evil.test"></iframe>'
            .'<img src="jaVaScRiPt:alert(1)">'
            .'<a href="javascript:alert(1)">bad link</a>',
        );

        $this->assertStringContainsString('<p', $clean);
        $this->assertStringContainsString('<strong>bold</strong>', $clean);
        $this->assertStringContainsString('https://example.com', $clean);
        $this->assertStringNotContainsStringIgnoringCase('<script', $clean);
        $this->assertStringNotContainsStringIgnoringCase('<iframe', $clean);
        $this->assertStringNotContainsStringIgnoringCase('onclick', $clean);
        $this->assertStringNotContainsStringIgnoringCase('javascript:', $clean);
    }

    public function test_cms_page_content_is_stored_sanitized(): void
    {
        $admin = $this->createAdminWithPermissions(['cms.view', 'cms.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.cms.pages.update', 'terms-conditions'), [
                'title' => 'Terms & Conditions',
                'content' => '<p>Fine print.</p><script>alert(document.cookie)</script><div onclick="x()">Trigger</div>',
            ])
            ->assertRedirect(route('admin.cms.pages.edit', 'terms-conditions'));

        $page = CmsPage::where('slug', 'terms-conditions')->first();
        $this->assertStringContainsString('<p>Fine print.</p>', $page->content);
        $this->assertStringNotContainsStringIgnoringCase('<script', $page->content);
        $this->assertStringNotContainsStringIgnoringCase('onclick', $page->content);
    }

    public function test_product_description_is_stored_sanitized(): void
    {
        $admin = $this->createAdminWithPermissions(['product.view', 'product.create']);

        $category = Category::factory()->create();

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.product.store'), [
                'name' => 'XSS Sweatshirt',
                'category_id' => $category->id,
                'is_active' => 'active',
                'variants' => [
                    ['price' => 1000, 'stock_quantity' => 10, 'is_active' => 'active'],
                ],
                'description' => '<p>Comfy.</p><script>alert(1)</script>',
                'short_description' => '<em>Soft</em><img src="x" onerror="alert(1)">',
            ]);

        $product = Product::where('name', 'XSS Sweatshirt')->firstOrFail();
        $this->assertStringContainsString('<p>Comfy.</p>', $product->description);
        $this->assertStringNotContainsStringIgnoringCase('<script', $product->description);
        $this->assertStringContainsString('<em>Soft</em>', $product->short_description);
        $this->assertStringNotContainsStringIgnoringCase('onerror', $product->short_description);
    }

    // ─── File uploads: SVG hardening ───

    public function test_svg_logo_upload_is_stored_without_active_content(): void
    {
        $malicious = <<<'SVG'
            <?xml version="1.0"?>
            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">
              <script>alert(document.cookie)</script>
              <circle cx="50" cy="50" r="40" fill="red" onclick="alert(1)"/>
              <a href="javascript:alert(2)"><rect width="10" height="10"/></a>
            </svg>
            SVG;

        $upload = UploadedFile::fake()->createWithContent('logo.svg', $malicious);

        // Direct service call: the actual production upload path (the HTTP
        // request validation itself is already covered for PNGs in
        // SettingModuleTest; here we verify the content stripping).
        app(SettingService::class)->updateGroup(
            'design',
            ['logo' => $upload],
            ['logo'],
            [],
        );

        $logoPath = Setting::get('design', 'logo');
        $this->assertNotNull($logoPath);

        $stored = Storage::disk('public')->get($logoPath);
        $this->assertStringContainsString('<circle', $stored);
        $this->assertStringContainsString('<rect', $stored);
        $this->assertStringNotContainsStringIgnoringCase('<script', $stored);
        $this->assertStringNotContainsStringIgnoringCase('onclick', $stored);
        $this->assertStringNotContainsStringIgnoringCase('javascript:', $stored);
    }

    // ─── Rate limiting: coupon / checkout endpoints ───

    public function test_apply_coupon_endpoint_is_rate_limited(): void
    {
        Coupon::factory()->create(['code' => 'VALIDCODE', 'is_active' => true]);

        // The shared web rate limiter keyed on IP allows 20 attempts per minute.
        $response = null;

        for ($i = 0; $i < 21; $i++) {
            $response = $this->post(route('visitor.checkout.apply-coupon'), ['code' => 'WRONG-CODE-'.$i]);
        }

        $response->assertStatus(429);
    }
}
