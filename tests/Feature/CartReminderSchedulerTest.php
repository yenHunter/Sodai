<?php

namespace Tests\Feature;

use App\Mail\Admin\CartReminderMail;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\AdminTestHelpers;

class CartReminderSchedulerTest extends TestCase
{
    use AdminTestHelpers, RefreshDatabase;

    private function enableScheduler(int $hours = 24): void
    {
        Setting::setMany('notification', [
            'abandoned_cart_enabled' => '1',
            'abandoned_cart_hours' => (string) $hours,
        ]);
    }

    private function disableScheduler(): void
    {
        Setting::setMany('notification', ['abandoned_cart_enabled' => '0']);
    }

    /**
     * A cart for $user holding one in-stock item, last active $hoursAgo.
     */
    private function abandonedCart(User $user, int $hoursAgo = 30): Cart
    {
        $product = Product::factory()->create();
        $variant = $product->defaultVariant;
        $variant->update(['price' => 100, 'stock_quantity' => 5]);
        $product->refreshPriceAndStockCache();

        $cart = Cart::create(['user_id' => $user->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $cart->forceFill(['updated_at' => now()->subHours($hoursAgo)])->save();

        return $cart->refresh();
    }

    private function outOfStockVariant(): ProductVariant
    {
        return ProductVariant::factory()->create(['stock_quantity' => 0]);
    }

    // ─────────────────────────────────────────────
    // SCHEDULED SEND
    // ─────────────────────────────────────────────

    public function test_command_queues_reminders_for_abandoned_carts(): void
    {
        $this->enableScheduler(hours: 24);
        $cart = $this->abandonedCart(User::factory()->create(), hoursAgo: 30);

        Queue::fake();

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, 1);
        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable instanceof CartReminderMail;
        });

        // Cart is stamped so the next run will not re-send.
        $this->assertNotNull($cart->refresh()->reminder_sent_at);
    }

    public function test_command_is_a_no_op_when_disabled_in_settings(): void
    {
        $this->disableScheduler();
        $cart = $this->abandonedCart(User::factory()->create());

        Queue::fake();

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Queue::assertNothingPushed();
        $this->assertNull($cart->refresh()->reminder_sent_at);
    }

    public function test_recently_active_carts_are_ignored(): void
    {
        $this->enableScheduler(hours: 24);
        $this->abandonedCart(User::factory()->create(), hoursAgo: 2);

        Queue::fake();

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_already_reminded_carts_are_not_resent(): void
    {
        $this->enableScheduler(hours: 24);
        $cart = $this->abandonedCart(User::factory()->create(), hoursAgo: 30);
        // Reminded AFTER its last activity. Raw update: Eloquent save()
        // would re-touch updated_at to now unless the value is dirty.
        DB::table('carts')->where('id', $cart->id)->update([
            'reminder_sent_at' => now()->subHours(29),
            'updated_at' => now()->subHours(30),
        ]);

        Queue::fake();

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_customer_returning_after_a_reminder_becomes_eligible_again(): void
    {
        $this->enableScheduler(hours: 24);
        $cart = $this->abandonedCart(User::factory()->create(), hoursAgo: 30);
        // Old reminder predates the cart's latest activity → worth a new one.
        // Raw update for the same timestamp-dirtiness reason as above.
        DB::table('carts')->where('id', $cart->id)->update([
            'reminder_sent_at' => now()->subHours(40),
            'updated_at' => now()->subHours(30),
        ]);

        Queue::fake();

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Queue::assertPushed(SendQueuedMailable::class, 1);
        $this->assertNotNull($cart->refresh()->reminder_sent_at);
    }

    // ─────────────────────────────────────────────
    // EXCLUSION RULES
    // ─────────────────────────────────────────────

    public function test_guest_carts_are_never_emailed(): void
    {
        $this->enableScheduler();

        $cart = Cart::create(['session_id' => 'scheduler-guest-session']);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => ProductVariant::factory()->create(['stock_quantity' => 3])->id,
            'quantity' => 1,
        ]);
        $cart->forceFill(['updated_at' => now()->subHours(48)])->save();

        Queue::fake();

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_empty_carts_are_ignored(): void
    {
        $this->enableScheduler();

        $cart = Cart::create(['user_id' => User::factory()->create()->id]);
        $cart->forceFill(['updated_at' => now()->subHours(48)])->save();

        Queue::fake();

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Queue::assertNothingPushed();
    }

    public function test_carts_with_sold_out_items_are_not_reminded(): void
    {
        $this->enableScheduler();

        $variant = $this->outOfStockVariant();
        $cart = Cart::create(['user_id' => User::factory()->create()->id]);
        CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);
        $cart->forceFill(['updated_at' => now()->subHours(48)])->save();

        Queue::fake();

        $this->artisan('carts:send-reminders')->assertSuccessful();

        Queue::assertNothingPushed();
        // Not stamped either — if stock returns before the cart goes stale again, a reminder can still go out.
        $this->assertNull($cart->refresh()->reminder_sent_at);
    }

    // ─────────────────────────────────────────────
    // MANUAL ADMIN SEND (legacy path now queues)
    // ─────────────────────────────────────────────

    public function test_manual_admin_reminder_queues_the_email_and_stamps_cart(): void
    {
        $admin = $this->createAdminWithPermissions(['cart.view']);
        $cart = $this->abandonedCart(User::factory()->create(), hoursAgo: 1);

        Queue::fake();

        $this->actingAsAdmin($admin)
            ->post(route('admin.ecommerce.cart.send-reminder', $cart))
            ->assertRedirect();

        Queue::assertPushed(SendQueuedMailable::class, 1);
        Queue::assertPushed(SendQueuedMailable::class, function (SendQueuedMailable $job) {
            return $job->mailable instanceof CartReminderMail;
        });
        $this->assertNotNull($cart->refresh()->reminder_sent_at);
    }
}
