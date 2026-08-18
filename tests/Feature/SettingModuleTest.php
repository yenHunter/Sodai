<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminTestHelpers;

class SettingModuleTest extends TestCase
{
    use RefreshDatabase, AdminTestHelpers;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_admin_can_view_configuration_hub(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view']);

        $this->actingAsAdmin($admin)
            ->get(route('admin.settings.index'))
            ->assertOk();
    }

    public function test_admin_can_view_and_update_company_settings(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.company.update'), [
                'name'                     => 'Sodai',
                'email'                    => 'hello@sodai.com',
                'phone'                    => '+8801700000000',
                'address'                  => 'Dhaka, Bangladesh',
                'currency'                 => 'BDT',
                'currency_symbol_position' => 'before',
                'timezone'                 => 'Asia/Dhaka',
            ])
            ->assertRedirect(route('admin.settings.company'));

        $this->assertEquals('Sodai', Setting::get('company', 'name'));
        $this->assertEquals('BDT', Setting::get('company', 'currency'));
    }

    public function test_company_settings_require_name_and_email(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.company.update'), [])
            ->assertSessionHasErrors(['name', 'email', 'phone', 'address', 'currency', 'timezone']);
    }

    public function test_admin_can_upload_logo_via_design_settings(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.design.update'), [
                'logo' => UploadedFile::fake()->image('logo.png'),
            ])
            ->assertRedirect(route('admin.settings.design'));

        $logoPath = Setting::get('design', 'logo');
        $this->assertNotNull($logoPath);
        Storage::disk('public')->assertExists($logoPath);
    }

    public function test_updating_design_without_new_file_keeps_existing_logo(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);
        Setting::setMany('design', ['logo' => 'settings/design/existing-logo.png'], ['logo' => 'image']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.design.update'), [
                'primary_color' => '#123456',
            ])
            ->assertRedirect(route('admin.settings.design'));

        $this->assertEquals('settings/design/existing-logo.png', Setting::get('design', 'logo'));
        $this->assertEquals('#123456', Setting::get('design', 'primary_color'));
    }

    public function test_removing_logo_clears_it(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        Storage::disk('public')->put('settings/design/old-logo.png', 'fake-content');
        Setting::setMany('design', ['logo' => 'settings/design/old-logo.png'], ['logo' => 'image']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.design.update'), [
                'remove_logo' => '1',
            ])
            ->assertRedirect(route('admin.settings.design'));

        $this->assertNull(Setting::get('design', 'logo'));
        Storage::disk('public')->assertMissing('settings/design/old-logo.png');
    }

    public function test_settings_are_cached_between_reads(): void
    {
        Setting::setMany('company', ['name' => 'Cached Co']);

        // First read populates the cache.
        $this->assertEquals('Cached Co', Setting::get('company', 'name'));

        // Directly mutate the DB row bypassing setMany() -> cache should still return old value.
        \DB::table('settings')->where('group', 'company')->where('key', 'name')->update(['value' => 'Bypassed']);
        $this->assertEquals('Cached Co', Setting::get('company', 'name'));

        // After setMany() busts the cache, the new value is visible.
        Setting::setMany('company', ['name' => 'Updated Co']);
        $this->assertEquals('Updated Co', Setting::get('company', 'name'));
    }

    public function test_unauthenticated_user_is_redirected_from_settings_routes(): void
    {
        $this->get(route('admin.settings.index'))
            ->assertRedirect(route('admin.login.view'));
    }

        public function test_admin_can_update_shipping_settings(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.shipping.update'), [
                'flat_rate'               => 5.99,
                'enable_free_shipping'    => '1',
                'free_shipping_threshold' => 50,
            ])
            ->assertRedirect(route('admin.settings.shipping'));

        $this->assertEquals('5.99', Setting::get('shipping', 'flat_rate'));
        $this->assertEquals('1', Setting::get('shipping', 'enable_free_shipping'));
    }

    public function test_admin_can_update_payment_settings(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.payment.update'), [
                'cod_enabled'           => '1',
                'bank_transfer_enabled' => '0',
            ])
            ->assertRedirect(route('admin.settings.payment'));

        $this->assertEquals('1', Setting::get('payment', 'cod_enabled'));
        $this->assertEquals('0', Setting::get('payment', 'bank_transfer_enabled'));
    }

    public function test_unchecked_boolean_settings_persist_as_false(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);
        Setting::setMany('inventory', ['hide_out_of_stock_products' => '1'], ['hide_out_of_stock_products' => 'boolean']);

        // Submitting the form WITHOUT the checkbox key present (browser behavior when unchecked)
        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.inventory.update'), [
                'default_low_stock_threshold' => 10,
            ])
            ->assertRedirect(route('admin.settings.inventory'));

        $this->assertEquals('0', Setting::get('inventory', 'hide_out_of_stock_products'));
    }

    public function test_admin_can_update_invoice_settings(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.invoice.update'), [
                'invoice_prefix'          => 'INV-2026-',
                'invoice_starting_number' => 5000,
            ])
            ->assertRedirect(route('admin.settings.invoice'));

        $this->assertEquals('INV-2026-', Setting::get('invoice', 'invoice_prefix'));
    }

    public function test_admin_can_update_order_settings(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.order.update'), [
                'order_number_prefix'      => 'ORD-',
                'auto_cancel_unpaid_hours' => 24,
            ])
            ->assertRedirect(route('admin.settings.order'));

        $this->assertEquals('24', Setting::get('order', 'auto_cancel_unpaid_hours'));
    }

    public function test_admin_can_update_tax_settings(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.tax.update'), [
                'tax_enabled' => '1',
                'tax_label'   => 'VAT',
                'tax_rate'    => 15,
            ])
            ->assertRedirect(route('admin.settings.tax'));

        $this->assertEquals('15', Setting::get('tax', 'tax_rate'));
    }

    public function test_tax_rate_cannot_exceed_100(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.tax.update'), [
                'tax_rate' => 150,
            ])
            ->assertSessionHasErrors('tax_rate');
    }

    public function test_admin_can_update_notification_settings(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.notification.update'), [
                'admin_alert_email' => 'alerts@sodai.com',
                'notify_new_order'  => '1',
            ])
            ->assertRedirect(route('admin.settings.notification'));

        $this->assertEquals('alerts@sodai.com', Setting::get('notification', 'admin_alert_email'));
    }

    public function test_notification_requires_valid_email(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.notification.update'), [
                'admin_alert_email' => 'not-an-email',
            ])
            ->assertSessionHasErrors('admin_alert_email');
    }

    public function test_admin_can_update_marketing_settings_across_two_groups(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.marketing.update'), [
                'meta_title'   => 'Sodai — Shop Online',
                'facebook_url' => 'https://facebook.com/sodai',
            ])
            ->assertRedirect(route('admin.settings.marketing'));

        $this->assertEquals('Sodai — Shop Online', Setting::get('seo', 'meta_title'));
        $this->assertEquals('https://facebook.com/sodai', Setting::get('social', 'facebook_url'));
    }

    public function test_marketing_social_urls_must_be_valid(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.marketing.update'), [
                'facebook_url' => 'not-a-url',
            ])
            ->assertSessionHasErrors('facebook_url');
    }

    public function test_admin_can_upload_and_remove_og_image(): void
    {
        $admin = $this->createAdminWithPermissions(['setting.view', 'setting.edit']);

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.marketing.update'), [
                'og_image' => UploadedFile::fake()->image('og.jpg'),
            ])
            ->assertRedirect(route('admin.settings.marketing'));

        $this->assertNotNull(Setting::get('seo', 'og_image'));

        $this->actingAsAdmin($admin)
            ->post(route('admin.settings.marketing.update'), [
                'remove_og_image' => '1',
            ])
            ->assertRedirect(route('admin.settings.marketing'));

        $this->assertNull(Setting::get('seo', 'og_image'));
    }
}