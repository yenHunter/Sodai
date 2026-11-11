import { expect, test } from '@playwright/test';

const appOrigin = new URL(
    process.env.PLAYWRIGHT_BASE_URL ?? `http://127.0.0.1:${process.env.PLAYWRIGHT_PORT ?? 8000}`,
).origin;

test.describe('storefront smoke', () => {
    // The storefront loads fonts/maps from third-party CDNs. Abort those so the
    // suite is deterministic and does not wait on external networks.
    test.beforeEach(async ({ page }) => {
        await page.route('**/*', (route) => {
            const url = route.request().url();

            return url.startsWith(appOrigin) || url.startsWith('data:')
                ? route.continue()
                : route.abort();
        });
    });

    test('home page renders the storefront shell', async ({ page }) => {
        const response = await page.goto('/', { waitUntil: 'domcontentloaded' });

        expect(response?.status()).toBe(200);
        await expect(page).toHaveTitle(/Sodai/i);
        await expect(page.locator('header').first()).toBeVisible();
        await expect(page.locator('body')).toContainText(/products/i);
    });

    test('product catalog is reachable', async ({ page }) => {
        const response = await page.goto('/products', { waitUntil: 'domcontentloaded' });

        expect(response?.status()).toBe(200);
        await expect(page.locator('header').first()).toBeVisible();
    });

    test('contact page renders the form', async ({ page }) => {
        const response = await page.goto('/contact', { waitUntil: 'domcontentloaded' });

        expect(response?.status()).toBe(200);
        await expect(page.locator('form').first()).toBeVisible();
    });

    test('unknown URLs return 404', async ({ page }) => {
        const response = await page.goto('/this-route-does-not-exist');

        expect(response?.status()).toBe(404);
    });
});
