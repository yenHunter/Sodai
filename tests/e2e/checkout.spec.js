import { expect, test } from '@playwright/test';
import { addProductToCart, COUPON_CODE, PRODUCT_SLUG } from './support/storefront';

const appOrigin = new URL(
    process.env.PLAYWRIGHT_BASE_URL ?? `http://127.0.0.1:${process.env.PLAYWRIGHT_PORT ?? 8000}`,
).origin;

test.describe.configure({ mode: 'serial', timeout: 90_000 });

test.describe('guest checkout', () => {
    test.beforeEach(async ({ page }) => {
        // Keep the run independent of external font/map CDNs.
        await page.route('**/*', (route) => {
            const url = route.request().url();

            return url.startsWith(appOrigin) || url.startsWith('data:')
                ? route.continue()
                : route.abort();
        });
    });

    test('a guest can add a product to the cart', async ({ page }) => {
        await addProductToCart(page);
        await page.goto('/cart', { waitUntil: 'domcontentloaded' });

        await expect(page.locator('.ec-cart-pro-name')).toContainText('E2E Checkout Product');
        await expect(page.locator('.ec-cart-pro-subtotal')).toContainText('$100.00');
        await expect(page.locator('a', { hasText: 'Check Out' })).toBeVisible();
    });

    test('coupon validation and guest order placement', async ({ page }) => {
        await addProductToCart(page);
        await page.goto('/checkout', { waitUntil: 'domcontentloaded' });

        await expect(page.locator('#checkoutForm')).toBeVisible();

        // ── Invalid coupon is surfaced without applying anything ──
        await page.click('#toggleCoupon');
        await page.fill('#couponCodeInput', 'NOT-A-COUPON');
        await page.click('#applyCouponBtn');

        await expect(page.locator('#couponMessage')).toContainText(/does not exist/i);
        await expect(page.locator('#couponCodeHidden')).toHaveValue('');

        // ── Valid coupon is applied and stored on the hidden field ──
        await page.fill('#couponCodeInput', COUPON_CODE);
        await page.click('#applyCouponBtn');

        await expect(page.locator('#couponMessage')).toContainText(new RegExp(`Coupon "${COUPON_CODE}" applied`, 'i'));
        await expect(page.locator('#couponCodeHidden')).toHaveValue(COUPON_CODE);

        // ── Guest shipping details + COD ──
        await page.fill('input[name="shipping_name"]', 'Guest E2E');
        await page.fill('input[name="shipping_email"]', `guest-e2e-${Date.now()}@example.com`);
        await page.fill('input[name="shipping_phone"]', '+8801700000099');
        await page.fill('input[name="shipping_address"]', '12 Test Street');
        await page.fill('input[name="shipping_city"]', 'Dhaka');
        await page.fill('input[name="shipping_state"]', 'Dhaka');
        await page.fill('input[name="shipping_zip"]', '1207');
        await page.fill('input[name="shipping_country"]', 'Bangladesh');

        await page.click('#placeOrderBtn');

        await expect(page).toHaveURL(/\/order-confirm$/);
        await expect(page.locator('.ec-thank-content')).toContainText(/placed successfully/i);
        await expect(page.locator('.ec-thank-content')).toContainText(/ORD-/);

        // 10% of the $100 product, proving the submitted coupon reached the order.
        await expect(page.locator('.ec-vendor-card-body')).toContainText('Discount: -$10.00');
        await expect(page.locator('.ec-vendor-card-body')).toContainText('Total: $90.00');
    });
});
