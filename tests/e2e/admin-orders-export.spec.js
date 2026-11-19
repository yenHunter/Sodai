import { expect, test } from '@playwright/test';
import { readFile } from 'node:fs/promises';
import { DEMO_ADMIN, submitAdminLogin } from './support/admin-auth';

// The fixtures come from database/seeders/E2eSeeder.php (E2E_SEED=true), which
// seeds one delivered and one pending order with fixed numbers, plus a third
// order dated 30 days ago so the date-range filter has something to exclude.
const DELIVERED_ORDER = 'E2E-ORD-DELIVERED';
const PENDING_ORDER = 'E2E-ORD-PENDING';
const ARCHIVED_ORDER = 'E2E-ORD-ARCHIVED';

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

    test('downloads only the orders inside the date range picked on the page', async ({ page }) => {
        await submitAdminLogin(page, DEMO_ADMIN);
        await page.goto('/admin/ecommerce/orders', { waitUntil: 'domcontentloaded' });

        // Set the window with the page's own picker: open it, pick the
        // "Last 7 Days" shortcut and confirm with Apply, which is what submits
        // the filter form.
        await page.locator('#dateRangeFilter').click();

        const picker = page.locator('.daterangepicker');
        await expect(picker).toBeVisible();

        await picker.locator('.ranges li', { hasText: 'Last 7 Days' }).click();

        // This daterangepicker build auto-applies shortcut ranges and submits
        // the form straight away; older builds wait for the Apply button. Cover
        // both so the test is not tied to the widget's autoApply default.
        //
        // waitUntil is pinned to domcontentloaded: the admin page keeps loading
        // images long enough that the default `load` event can miss the budget.
        const dateWindowUrl = /[?&]date_from=\d{4}-\d{2}-\d{2}/;

        try {
            await page.waitForURL(dateWindowUrl, { waitUntil: 'domcontentloaded', timeout: 7_000 });
        } catch {
            await picker.locator('.applyBtn').click();
            await page.waitForURL(dateWindowUrl, { waitUntil: 'domcontentloaded', timeout: 20_000 });
        }

        const params = new URL(page.url()).searchParams;
        const from = params.get('date_from');
        const to = params.get('date_to');

        expect(from).toMatch(/^\d{4}-\d{2}-\d{2}$/);
        expect(to).toMatch(/^\d{4}-\d{2}-\d{2}$/);

        const exportLink = page.getByRole('link', { name: /export csv/i });

        await expect(exportLink).toHaveAttribute('href', new RegExp(`date_from=${from}`));
        await expect(exportLink).toHaveAttribute('href', new RegExp(`date_to=${to}`));

        const [download] = await Promise.all([
            page.waitForEvent('download'),
            exportLink.click(),
        ]);

        const csv = await readFile(await download.path(), 'utf8');
        const [header, ...rows] = csv.replace(/^\uFEFF/, '').trim().split('\n');

        expect(header).toContain('order_date');
        // Recent fixtures are inside the window, the 30-day-old one is not.
        expect(csv).toContain(DELIVERED_ORDER);
        expect(csv).toContain(PENDING_ORDER);
        expect(csv).not.toContain(ARCHIVED_ORDER);

        // Every exported order is one the filtered table actually shows. The
        // CSV's order_date is rendered in the app timezone (Asia/Dhaka) while
        // the filter matches stored dates, so near midnight the displayed date
        // can be a day past the picked end date — membership is the stable
        // assertion that the window was honoured.
        expect(rows.length).toBeGreaterThan(0);
        const listedOrders = await page.locator('table tbody tr').allInnerTexts();

        for (const row of rows) {
            const orderNumber = row.split(',')[0];

            expect(listedOrders.some((text) => text.includes(orderNumber))).toBe(true);
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
