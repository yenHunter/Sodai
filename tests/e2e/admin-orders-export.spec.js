import { expect, test } from '@playwright/test';
import { readFile } from 'node:fs/promises';
import { DEMO_ADMIN, submitAdminLogin } from './support/admin-auth';

// The fixtures come from database/seeders/E2eSeeder.php (E2E_SEED=true), which
// seeds one delivered and one pending order with fixed numbers.
const DELIVERED_ORDER = 'E2E-ORD-DELIVERED';
const PENDING_ORDER = 'E2E-ORD-PENDING';

test.describe.configure({ mode: 'serial', timeout: 90_000 });

test.describe('orders CSV export', () => {
    test('downloads only the orders matching the active status filter', async ({ page }) => {
        await submitAdminLogin(page, DEMO_ADMIN);
        await page.goto('/admin/ecommerce/orders?status=delivered', { waitUntil: 'domcontentloaded' });

        const exportLink = page.getByRole('link', { name: /export csv/i });

        await expect(exportLink).toBeVisible();
        // The button must carry the page's filters into the export URL.
        await expect(exportLink).toHaveAttribute('href', /[?&]status=delivered/);

        const [download] = await Promise.all([
            page.waitForEvent('download'),
            exportLink.click(),
        ]);

        expect(download.suggestedFilename()).toMatch(/^orders-filtered_.*\.csv$/);

        const csv = await readFile(await download.path(), 'utf8');
        const [header, ...rows] = csv.replace(/^\uFEFF/, '').trim().split('\n');

        expect(header).toContain('order_number');
        expect(header).toContain('items_quantity');
        expect(csv).toContain(DELIVERED_ORDER);
        expect(csv).not.toContain(PENDING_ORDER);

        // Every exported row is a delivered order, not just the visible page.
        expect(rows.length).toBeGreaterThan(0);
        for (const row of rows) {
            const columns = row.split(',');
            expect(columns[0]).toBe(DELIVERED_ORDER);
            expect(columns[2]).toBe('delivered');
        }
    });

    test('downloads only the orders matching the search term', async ({ page }) => {
        await submitAdminLogin(page, DEMO_ADMIN);
        await page.goto(
            `/admin/ecommerce/orders?search=${encodeURIComponent(PENDING_ORDER)}`,
            { waitUntil: 'domcontentloaded' },
        );

        const exportLink = page.getByRole('link', { name: /export csv/i });

        await expect(exportLink).toHaveAttribute('href', /[?&]search=E2E-ORD-PENDING/);

        const [download] = await Promise.all([
            page.waitForEvent('download'),
            exportLink.click(),
        ]);

        const csv = await readFile(await download.path(), 'utf8');

        expect(csv).toContain(PENDING_ORDER);
        expect(csv).not.toContain(DELIVERED_ORDER);
    });
});
