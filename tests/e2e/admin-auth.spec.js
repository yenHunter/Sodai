import { expect, test } from '@playwright/test';
import { DEMO_ADMIN, MFA_ADMIN, MFA_SECRET, RECOVERY_CODE, submitAdminLogin } from './support/admin-auth';
import { generateTotp } from './support/totp';

// The recovery-code test consumes a code, so these must run in order and are
// not retried against a database that may already have advanced state.
// Two logins per test plus the single-threaded PHP server exceed the default
// 30 s budget, especially on Windows and cold CI workers.
test.describe.configure({ mode: 'serial', retries: 0, timeout: 60_000 });

test.describe('admin authentication', () => {
    test('an admin without MFA lands on the dashboard', async ({ page }) => {
        await submitAdminLogin(page, DEMO_ADMIN);

        await expect(page).toHaveURL(/\/admin\/dashboard$/);
        await expect(page.locator('body')).toBeVisible();
    });

    test('an MFA admin is stopped at the challenge and the dashboard stays protected', async ({ page }) => {
        await submitAdminLogin(page, MFA_ADMIN);

        await expect(page).toHaveURL(/\/admin\/two-factor-challenge$/);
        await expect(page.locator('#code')).toBeVisible();

        await page.goto('/admin/dashboard');

        await expect(page).toHaveURL(/\/admin\/login$/);
    });

    test('an invalid authentication code is rejected', async ({ page }) => {
        const validCode = generateTotp(MFA_SECRET);
        const invalidCode = validCode === '000000' ? '111111' : '000000';

        await submitAdminLogin(page, MFA_ADMIN);

        await page.fill('#code', invalidCode);
        await page.click('button[type="submit"]');

        await expect(page.locator('.alert-danger')).toContainText(/invalid/i);
        await expect(page).toHaveURL(/\/admin\/two-factor-challenge$/);
    });

    test('a valid TOTP code completes the login', async ({ page }) => {
        await submitAdminLogin(page, MFA_ADMIN);

        await page.fill('#code', generateTotp(MFA_SECRET));
        await page.click('button[type="submit"]');

        await expect(page).toHaveURL(/\/admin\/dashboard$/);
        await expect(page.locator('body')).toBeVisible();
    });

    test('a recovery code works once and is rejected afterwards', async ({ page }) => {
        await submitAdminLogin(page, MFA_ADMIN);

        await page.fill('#code', RECOVERY_CODE);
        await page.click('button[type="submit"]');
        await expect(page).toHaveURL(/\/admin\/dashboard$/);

        await page.context().clearCookies();
        await submitAdminLogin(page, MFA_ADMIN);

        await page.fill('#code', RECOVERY_CODE);
        await page.click('button[type="submit"]');

        await expect(page.locator('.alert-danger')).toContainText(/invalid/i);
        await expect(page).toHaveURL(/\/admin\/two-factor-challenge$/);
    });
});
