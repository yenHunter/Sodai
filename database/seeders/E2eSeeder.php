<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Deterministic fixtures for the Playwright tests: an MFA-enabled admin plus a
 * purchasable product, guest checkout enabled and a known coupon.
 *
 * Only runs when DatabaseSeeder is invoked with E2E_SEED=true (the CI e2e job
 * and the documented local command), and never in production. The TOTP secret
 * and recovery codes are fixed so browser tests can calculate valid codes.
 */
class E2eSeeder extends Seeder
{
    public const EMAIL = 'e2e-admin@example.com';

    public const PASSWORD = 'E2ePassword#123';

    public const TOTP_SECRET = 'JBSWY3DPEHPK3PXPJBSWY3DPEHPK3PXP';

    /** @var array<int, string> */
    public const RECOVERY_CODES = ['E2EAA-BBBBB', 'E2ECCC-DDDDD'];

    public const CHECKOUT_PRODUCT_SLUG = 'e2e-checkout-product';

    public const COUPON_CODE = 'E2ECHECKOUT';

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('E2eSeeder skipped: test accounts are disabled in production.');

            return;
        }

        $admin = Admin::updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'E2E Admin',
                'password' => Hash::make(self::PASSWORD),
                'is_active' => true,
            ],
        );

        $admin->syncRoles(['super-admin']);

        $admin->forceFill([
            'two_factor_secret' => self::TOTP_SECRET,
            'two_factor_recovery_codes' => self::RECOVERY_CODES,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->seedCheckoutCatalog();
    }

    /**
     * A simple in-stock product, an active coupon and guest checkout enabled,
     * so the browser suite can exercise cart → checkout → order placement.
     */
    private function seedCheckoutCatalog(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'e2e-checkout'],
            ['name' => 'E2E Checkout', 'is_active' => true, 'sort_order' => 0],
        );

        $product = Product::updateOrCreate(
            ['slug' => self::CHECKOUT_PRODUCT_SLUG],
            [
                'name' => 'E2E Checkout Product',
                'category_id' => $category->id,
                'is_active' => true,
                'is_featured' => false,
            ],
        );

        ProductVariant::updateOrCreate(
            ['product_id' => $product->id, 'sku' => 'E2E-SKU-001'],
            [
                'price' => 100,
                'stock_quantity' => 50,
                'low_stock_threshold' => 5,
                'is_default' => true,
                'is_active' => true,
            ],
        );

        $product->refreshPriceAndStockCache();

        Coupon::updateOrCreate(
            ['code' => self::COUPON_CODE],
            [
                'type' => 'percentage',
                'value' => 10,
                'minimum_order_amount' => 0,
                'usage_limit' => null,
                'usage_per_user' => 5,
                'used_count' => 0,
                'is_active' => true,
                'starts_at' => null,
                'expires_at' => null,
            ],
        );

        Setting::setMany(
            'order',
            ['allow_guest_checkout' => '1'],
            ['allow_guest_checkout' => 'boolean'],
        );
    }
}
