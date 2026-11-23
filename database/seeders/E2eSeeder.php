<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
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

    public const DELIVERED_ORDER_NUMBER = 'E2E-ORD-DELIVERED';

    public const PENDING_ORDER_NUMBER = 'E2E-ORD-PENDING';

    public const ARCHIVED_ORDER_NUMBER = 'E2E-ORD-ARCHIVED';

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
        $this->seedOrders();
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

    /**
     * Two orders with different statuses, so browser tests can prove that an
     * index filter (and the CSV export of it) narrows a real result set.
     */
    private function seedOrders(): void
    {
        $customer = User::updateOrCreate(
            ['email' => 'e2e-customer@example.com'],
            [
                'name' => 'E2E Customer',
                'phone' => '+8801700000000',
                'password' => Hash::make('Customer#123'),
                'status' => 'active',
            ],
        );

        $variant = ProductVariant::where('sku', 'E2E-SKU-001')->first();

        if (! $variant) {
            return;
        }

        $orders = [
            self::DELIVERED_ORDER_NUMBER => ['status' => 'delivered'],
            self::PENDING_ORDER_NUMBER => ['status' => 'pending'],
            // Outside the "Last 7 Days" window the orders export spec selects
            // through the date picker, so the range visibly narrows the file.
            self::ARCHIVED_ORDER_NUMBER => ['status' => 'confirmed', 'created_at' => now()->subDays(30)],
        ];

        foreach ($orders as $orderNumber => $attributes) {
            $createdAt = $attributes['created_at'] ?? null;
            unset($attributes['created_at']);

            $order = Order::updateOrCreate(
                ['order_number' => $orderNumber],
                $attributes + [
                    'user_id' => $customer->id,
                    'subtotal' => 100,
                    'discount_amount' => 0,
                    'shipping_charge' => 0,
                    'tax_amount' => 0,
                    'total_amount' => 100,
                    'payment_method' => 'cod',
                    'shipping_name' => 'E2E Customer',
                    'shipping_email' => 'e2e-customer@example.com',
                    'shipping_phone' => '+8801700000000',
                    'shipping_address' => '12 Test Street',
                    'shipping_city' => 'Dhaka',
                    'shipping_state' => 'Dhaka',
                    'shipping_zip' => '1207',
                    'shipping_country' => 'Bangladesh',
                ],
            );

            if ($createdAt) {
                // created_at is not fillable, so pin the date after the upsert.
                $order->forceFill(['created_at' => $createdAt])->save();
            }

            OrderItem::updateOrCreate(
                ['order_id' => $order->id, 'product_sku' => 'E2E-SKU-001'],
                [
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'product_name' => 'E2E Checkout Product',
                    'unit_price' => 100,
                    'quantity' => 1,
                    'total_price' => 100,
                ],
            );
        }
    }
}
