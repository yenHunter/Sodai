import { expect, test } from '@playwright/test';
import { addProductToCart, COUPON_CODE, placeGuestOrder, PRODUCT_SLUG } from './support/storefront';

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
        const orderNumber = await placeGuestOrder(page, { email: `guest-e2e-${Date.now()}@example.com` });

        await expect(page.locator('.ec-thank-content')).toContainText(/placed successfully/i);
        await expect(page.locator('.ec-thank-content')).toContainText(orderNumber);

        // 10% of the $100 product, proving the submitted coupon reached the order.
        await expect(page.locator('.ec-vendor-card-body')).toContainText('Discount: -$10.00');
        await expect(page.locator('.ec-vendor-card-body')).toContainText('Total: $90.00');
    });

    test('a guest can look up their order again but another session cannot', async ({ page, browser }) => {
        await addProductToCart(page);
        await page.goto('/checkout', { waitUntil: 'domcontentloaded' });

        const email = `guest-lookup-${Date.now()}@example.com`;
        const orderNumber = await placeGuestOrder(page, { email });

        // The confirmation page is the guest's order lookup: their session
        // holds the order id, not a guessable id in the URL.
        await expect(page.locator('.ec-thank-content')).toContainText(orderNumber);
        await expect(page.locator('.ec-vendor-card-body')).toContainText('E2E Checkout Product');
        await expect(page.locator('.ec-vendor-card-body')).toContainText('Total: $100.00');

        await page.reload({ waitUntil: 'domcontentloaded' });

        await expect(page).toHaveURL(/\/order-confirm$/);
        await expect(page.locator('.ec-thank-content')).toContainText(orderNumber);
        await expect(page.locator('.ec-vendor-card-body')).toContainText('Shipping: $0.00');

        // The public lookup needs both the order number and the checkout email.
        await page.goto('/track-order', { waitUntil: 'domcontentloaded' });
        await page.fill('#trackOrderNumber', orderNumber);
        await page.fill('#trackOrderEmail', email);
        await page.click('#trackOrderBtn');

        await expect(page.locator('#trackOrderResult')).toContainText(orderNumber);
        await expect(page.locator('#trackOrderResult')).toContainText('E2E Checkout Product');
        await expect(page.locator('#trackOrderResult')).toContainText('Total: $100.00');

        // A different guest session has no last_order_id, so it must not see
        // this order even if it visits the same confirmation URL.
        const strangerContext = await browser.newContext();
        const stranger = await strangerContext.newPage();

        await stranger.route('**/*', (route) => {
            const url = route.request().url();

            return url.startsWith(appOrigin) || url.startsWith('data:')
                ? route.continue()
                : route.abort();
        });

        await stranger.goto('/order-confirm', { waitUntil: 'domcontentloaded' });

        await expect(stranger.locator('.ec-thank-content')).toContainText('For Shopping with us.');
        await expect(stranger.locator('.ec-vendor-dashboard-card')).toHaveCount(0);
        await expect(stranger.locator('body')).not.toContainText(orderNumber);

        await strangerContext.close();
    });
});
