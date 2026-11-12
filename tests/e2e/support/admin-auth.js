export const DEMO_ADMIN = {
    email: process.env.E2E_DEMO_ADMIN_EMAIL ?? 'admin@cyberjatra.com',
    password: process.env.E2E_DEMO_ADMIN_PASSWORD ?? 'Admin#123',
};

export const MFA_ADMIN = {
    email: process.env.E2E_ADMIN_EMAIL ?? 'e2e-admin@example.com',
    password: process.env.E2E_ADMIN_PASSWORD ?? 'E2ePassword#123',
};

export const MFA_SECRET = process.env.E2E_MFA_SECRET ?? 'JBSWY3DPEHPK3PXPJBSWY3DPEHPK3PXP';

export const RECOVERY_CODE = process.env.E2E_RECOVERY_CODE ?? 'E2EAA-BBBBB';

/**
 * Submit the admin login form.
 *
 * The page's own submit handler calls Google reCAPTCHA and blocks the request
 * if the script cannot load. The server skips verification in local+debug, so
 * tests submit the form natively with a placeholder token instead. This keeps
 * the suite independent of Google and of network access.
 */
export async function submitAdminLogin(page, { email, password }) {
    await page.goto('/admin/login', { waitUntil: 'domcontentloaded' });

    await page.fill('input[name="email"]', email);
    await page.fill('input[name="password"]', password);

    await page
        .evaluate(() => {
            const token = document.getElementById('g-recaptcha-response');
            if (token) {
                token.value = 'e2e-test-token';
            }

            document.getElementById('adminLoginForm').submit();
        })
        .catch(() => {
            // Navigation can destroy the execution context before evaluate resolves.
        });

    await page.waitForURL(/\/(admin\/dashboard|admin\/two-factor-challenge)$/);
}
