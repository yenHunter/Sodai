<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class VisitorCheckoutTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────
    // SETTINGS SEEDING
    // ─────────────────────────────────────────────

    private function seedStoreSettings(): void
    {
        Setting::setMany('order', ['allow_guest_checkout' => '1']);
        Setting::setMany('shipping', [
            'enable_free_shipping' => '0',
            'free_shipping_threshold' => '0',
            'inside_area_charge' => '60',
            'outside_area_charge' => '120',
            'operation_areas' => json_encode(['Dhaka']),
        ]);
    }

    /**
     * A simple product whose default variant carries deterministic
     * pricing/stock, plus its refreshed denormalized cache.
     *
     * @return array{0: Product, 1: ProductVariant}
     */
    private function makeProduct(float $price = 100, int $stock = 10): array
    {
        $product = Product::factory()->for(Category::factory()->create())->create();

        $variant = $product->variants()->first();
        if ($variant) {
            $variant->update([
                'price' => $price,
                'discount_type' => null,
                'discount_value' => null,
                'stock_quantity' => $stock,
            ]);
        } else {
            $variant = ProductVariant::factory()->create([
                'product_id' => $product->id,
                'is_default' => true,
                'price' => $price,
                'discount_type' => null,
                'discount_value' => null,
                'stock_quantity' => $stock,
            ]);
        }

        $product->refreshPriceAndStockCache();

        return [$product->refresh(), $variant->refresh()];
    }

    /**
    * Start a real guest session by rendering the public cart page, then
    * carry its session cookie into all subsequent requests. Without this,
    * every request without a cookie gets a freshly generated session ID
    * and the session-keyed guest cart is never found again.
    */
    private function startGuestSession(): void
    {
        $response = $this->get('/cart')->assertOk();

        $cookie = $response->getCookie(config('session.cookie'), false); // raw (still encrypted)

        $this->withUnencryptedCookie(config('session.cookie'), $cookie->getValue());
    }

    /**
     * The customer cart, bound to the exact session/auth context the
     * subsequent requests will use.
     */
    private function cartForContext(?User $customer): Cart
    {
        if ($customer) {
            $this->actingAs($customer, 'customer')->get('/cart');
        } else {
            $this->startGuestSession();
        }

        return Cart::firstOrCreate(
            $customer ? ['user_id' => $customer->id] : ['session_id' => session()->getId()]
        );
    }

    private function fillCart(Cart $cart, ProductVariant $variant, int $quantity): CartItem
    {
        return CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
        ]);
    }

    /**
     * Minimal valid checkout payload.
     */
    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'shipping_name' => 'Jane Guest',
            'shipping_email' => 'guest@example.com',
            'shipping_phone' => '01712345678',
            'shipping_address' => 'House 12, Road 5',
            'shipping_city' => 'Dhaka',
            'shipping_state' => 'Dhaka',
            'shipping_zip' => '1200',
            'shipping_country' => 'Bangladesh',
            'payment_method' => 'cod',
        ], $overrides);
    }

    // ─────────────────────────────────────────────
    // 1. SUCCESSFUL ORDER PLACEMENT (authenticated)
    // ─────────────────────────────────────────────

    public function test_authenticated_customer_can_place_order_and_stock_decrements(): void
    {
        $this->seedStoreSettings();
        Mail::fake();

        $customer = User::factory()->create();
        [$product, $variant] = $this->makeProduct(price: 100, stock: 10);
        $cart = $this->cartForContext($customer);
        $this->fillCart($cart, $variant, quantity: 3);

        $response = $this->actingAs($customer, 'customer')
            ->post(route('visitor.checkout.store'), $this->checkoutPayload());

        $response->assertRedirect(route('visitor.order-confirm'));
        $response->assertSessionHas('success');

        $order = Order::where('user_id', $customer->id)->first();
        $this->assertNotNull($order);
        $this->assertSame('pending', $order->status);
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame('300.00', (string) $order->subtotal); // 3 × 100
        $this->assertSame('60.00', (string) $order->shipping_charge); // inside Dhaka area
        $this->assertSame('360.00', (string) $order->total_amount);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(3, (int) $order->items()->first()->quantity);
        $this->assertSame(100.0, (float) $order->items()->first()->unit_price);

        // Stock decremented, product cache refreshed.
        $this->assertSame(7, (int) $variant->refresh()->stock_quantity);
        $this->assertSame(7, (int) $product->refresh()->total_stock);
        $this->assertSame(3, (int) $product->refresh()->total_sales);

        // Cart cleared, status history written.
        $this->assertSame(0, CartItem::where('cart_id', $cart->id)->count());
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'to_status' => 'pending',
        ]);
    }

    // ─────────────────────────────────────────────
    // 2. GUEST AUTO-ACCOUNT CREATION
    // ─────────────────────────────────────────────

    public function test_guest_checkout_auto_creates_account_and_sends_set_password_email(): void
    {
        $this->seedStoreSettings();
        Mail::fake();

        [, $variant] = $this->makeProduct(price: 50, stock: 5);
        $cart = $this->cartForContext(null);
        $this->fillCart($cart, $variant, quantity: 1);        $response = $this->post(route('visitor.checkout.store'), $this->checkoutPayload([
            'shipping_email' => 'newguest@example.com',
        ]));

        $response->assertRedirect(route('visitor.order-confirm'));

        // Auto-created account with verified email (identity proven via set-password link).
        $customer = User::where('email', 'newguest@example.com')->first();
        $this->assertNotNull($customer);
        $this->assertNotNull($customer->email_verified_at);

        // Order attached to the auto-created account.
        $this->assertDatabaseHas('orders', [
            'user_id' => $customer->id,
            'shipping_email' => 'newguest@example.com',
        ]);

        // Set-password mail sent for account bootstrap.
        Mail::assertSent(\App\Mail\Admin\CustomerSetPasswordMail::class, 1);
    }

    public function test_guest_checkout_with_existing_email_reuses_account_instead_of_duplicating(): void
    {
        $this->seedStoreSettings();
        Mail::fake();

        $existing = User::factory()->create(['email' => 'returning@example.com']);

        [, $variant] = $this->makeProduct(price: 50, stock: 5);
        $cart = $this->cartForContext(null);
        $this->fillCart($cart, $variant, quantity: 1);

        $this->post(route('visitor.checkout.store'), $this->checkoutPayload([
            'shipping_email' => 'returning@example.com',
        ]));

        $this->assertSame(1, User::where('email', 'returning@example.com')->count());
        $this->assertDatabaseHas('orders', ['user_id' => $existing->id]);

        // Existing account: no set-password email needed.
        Mail::assertNotSent(\App\Mail\Admin\CustomerSetPasswordMail::class);
    }

    public function test_guest_checkout_is_rejected_when_setting_disallows_it(): void
    {
        Setting::setMany('order', ['allow_guest_checkout' => '0']);

        [, $variant] = $this->makeProduct();
        $cart = $this->cartForContext(null);
        $this->fillCart($cart, $variant, quantity: 1);

        $response = $this->post(route('visitor.checkout.store'), $this->checkoutPayload());

        $response->assertForbidden();
        $this->assertSame(0, Order::count());
    }

    // ─────────────────────────────────────────────
    // 3. INSUFFICIENT STOCK REJECTION
    // ─────────────────────────────────────────────

    public function test_checkout_rejects_when_cart_exceeds_available_stock(): void
    {
        $this->seedStoreSettings();

        [, $variant] = $this->makeProduct(price: 100, stock: 2);
        $cart = $this->cartForContext(null);
        $this->fillCart($cart, $variant, quantity: 5);

        $response = $this->post(route('visitor.checkout.store'), $this->checkoutPayload());

        $response->assertRedirect(route('visitor.checkout'));
        $response->assertSessionHas('error');

        // Nothing was created, nothing was decremented, cart preserved.
        $this->assertSame(0, Order::count());
        $this->assertSame(2, (int) $variant->refresh()->stock_quantity);
        $this->assertSame(5, (int) CartItem::where('cart_id', $cart->id)->value('quantity'));
    }

    public function test_checkout_rejects_when_variant_became_inactive_after_add_to_cart(): void
    {
        $this->seedStoreSettings();

        [, $variant] = $this->makeProduct(price: 100, stock: 10);
        $cart = $this->cartForContext(null);
        $this->fillCart($cart, $variant, quantity: 1);

        $variant->update(['is_active' => false]);

        $response = $this->post(route('visitor.checkout.store'), $this->checkoutPayload());

        $response->assertRedirect(route('visitor.checkout'));
        $response->assertSessionHas('error');
        $this->assertSame(0, Order::count());
        $this->assertSame(10, (int) $variant->refresh()->stock_quantity);
    }

    // ─────────────────────────────────────────────
    // 4. COUPON LIMITS
    // ─────────────────────────────────────────────

    public function test_valid_coupon_applies_discount_and_updates_usage(): void
    {
        $this->seedStoreSettings();

        [$product, $variant] = $this->makeProduct(price: 200, stock: 10);
        $cart = $this->cartForContext(null);
        $this->fillCart($cart, $variant, quantity: 2);

        $coupon = Coupon::factory()->create([
            'code' => 'SAVE20',
            'type' => 'percentage',
            'value' => 20,
        ]);

        $response = $this->post(route('visitor.checkout.store'), $this->checkoutPayload([
            'coupon_code' => 'SAVE20',
        ]));

        $response->assertRedirect(route('visitor.order-confirm'));

        $order = Order::firstOrFail();
        $this->assertSame($coupon->id, $order->coupon_id);
        $this->assertSame('SAVE20', $order->coupon_code);
        $this->assertSame('400.00', (string) $order->subtotal); // 2 × 200
        $this->assertSame('80.00', (string) $order->discount_amount); // 20%
        $this->assertSame('380.00', (string) $order->total_amount); // 400 − 80 + 60 shipping, no tax

        $this->assertSame(1, (int) $coupon->refresh()->used_count);
        $this->assertSame(0, CartItem::where('cart_id', $cart->id)->count());

        // Product analytics also updated.
        $this->assertSame(2, (int) $product->refresh()->total_sales);
    }

    public function test_coupon_rejected_below_minimum_order_amount(): void
    {
        $this->seedStoreSettings();

        [, $variant] = $this->makeProduct(price: 100, stock: 10);
        $cart = $this->cartForContext(null);
        $this->fillCart($cart, $variant, quantity: 1);

        Coupon::factory()->create([
            'code' => 'MIN500',
            'minimum_order_amount' => 500,
        ]);

        $response = $this->post(route('visitor.checkout.store'), $this->checkoutPayload([
            'coupon_code' => 'MIN500',
        ]));

        $response->assertRedirect(route('visitor.checkout'));
        $response->assertSessionHas('error');
        $this->assertSame(0, Order::count());
        $this->assertSame(10, (int) $variant->refresh()->stock_quantity); // untouched
    }

    public function test_coupon_rejected_after_per_user_limit_is_reached(): void
    {
        $this->seedStoreSettings();

        $customer = User::factory()->create();
        [, $variant] = $this->makeProduct(price: 100, stock: 50);
        $cart = $this->cartForContext($customer);
        $this->fillCart($cart, $variant, quantity: 1);

        $coupon = Coupon::factory()->create([
            'code' => 'ONCEONLY',
            'usage_per_user' => 1,
        ]);

        // Prior order from this customer that already used the coupon.
        Order::factory()->for($customer)->create([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($customer, 'customer')
            ->post(route('visitor.checkout.store'), $this->checkoutPayload(['coupon_code' => 'ONCEONLY']));

        $response->assertRedirect(route('visitor.checkout'));
        $response->assertSessionHas('error');

        // Only the pre-seeded order exists; the new one was rejected.
        $this->assertSame(1, Order::where('user_id', $customer->id)->count());
        $this->assertSame(0, (int) $coupon->refresh()->used_count);
    }

    public function test_expired_coupon_is_rejected(): void
    {
        $this->seedStoreSettings();

        [, $variant] = $this->makeProduct(price: 100, stock: 10);
        $cart = $this->cartForContext(null);
        $this->fillCart($cart, $variant, quantity: 1);

        Coupon::factory()->create([
            'code' => 'OLDCODE',
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->post(route('visitor.checkout.store'), $this->checkoutPayload([
            'coupon_code' => 'OLDCODE',
        ]));

        $response->assertRedirect(route('visitor.checkout'));
        $response->assertSessionHas('error');
        $this->assertSame(0, Order::count());
    }

    // ─────────────────────────────────────────────
    // 5. CART CLEARING AFTER PURCHASE
    // ─────────────────────────────────────────────

    public function test_multi_item_cart_is_fully_cleared_after_purchase(): void
    {
        $this->seedStoreSettings();

        $customer = User::factory()->create();
        [, $v1] = $this->makeProduct(price: 100, stock: 10);
        [, $v2] = $this->makeProduct(price: 50, stock: 10);

        $cart = $this->cartForContext($customer);
        $this->fillCart($cart, $v1, quantity: 2);
        $this->fillCart($cart, $v2, quantity: 1);
        $this->assertSame(2, CartItem::where('cart_id', $cart->id)->count());

        $response = $this->actingAs($customer, 'customer')
            ->post(route('visitor.checkout.store'), $this->checkoutPayload());

        $response->assertRedirect(route('visitor.order-confirm'));

        $order = Order::where('user_id', $customer->id)->firstOrFail();
        $this->assertSame(2, $order->items()->count());
        $this->assertSame('250.00', (string) $order->subtotal); // 200 + 50

        // Every cart row for this customer is gone.
        $this->assertSame(0, CartItem::whereHas('cart', fn ($q) => $q->where('user_id', $customer->id))->count());
        $this->assertSame(8, (int) $v1->refresh()->stock_quantity); // 10 − 2
    }

    public function test_order_confirm_page_shows_last_placed_order(): void
    {
        $this->seedStoreSettings();

        $customer = User::factory()->create();
        [, $variant] = $this->makeProduct(price: 100, stock: 10);
        $cart = $this->cartForContext($customer);
        $this->fillCart($cart, $variant, quantity: 1);

        $this->actingAs($customer, 'customer')
            ->post(route('visitor.checkout.store'), $this->checkoutPayload());

        $orderId = Order::firstOrFail()->id;

        $response = $this->actingAs($customer, 'customer')
            ->withSession(['last_order_id' => $orderId])
            ->get(route('visitor.order-confirm'));

        $response->assertOk();
        $response->assertViewHas('order', fn ($order) => $order !== null && $order->id === $orderId);
    }
}
