import { expect, test } from '@playwright/test';

test.describe('admin authentication shell', () => {
    test('login page renders the credential form', async ({ page }) => {
        const response = await page.goto('/admin/login');

        expect(response?.status()).toBe(200);
        await expect(page.locator('input[name="email"]')).toBeVisible();
        await expect(page.locator('input[name="password"]')).toBeVisible();
        await expect(page.locator('form button[type="submit"]').first()).toBeVisible();
    });

    test('guests are redirected from the admin dashboard to login', async ({ page }) => {
        await page.goto('/admin/dashboard');

        await expect(page).toHaveURL(/\/admin\/login$/);
        await expect(page.locator('input[name="email"]')).toBeVisible();
    });
});
