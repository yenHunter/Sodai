import { expect, test } from '@playwright/test';
import { readFile } from 'node:fs/promises';
import { DEMO_ADMIN, submitAdminLogin } from './support/admin-auth';

// The fixtures come from database/seeders/E2eSeeder.php (E2E_SEED=true): the
// checkout product in its own category, a second product in the "E2E
// Accessories" category so a category filter has something to exclude, and an
// inactive plus an out-of-stock product so the status filter has one of each.
const CHECKOUT_PRODUCT = 'E2E Checkout Product';
const ACCESSORY_PRODUCT = 'E2E Accessory Product';
const ACCESSORY_CATEGORY = 'E2E Accessories';
const INACTIVE_PRODUCT = 'E2E Inactive Product';
const OUT_OF_STOCK_PRODUCT = 'E2E Out Of Stock Product';

// What the export must contain for each status filter value: the fixtures that
// qualify, and the is_active flag and stock level every exported row must show.
const STATUS_CASES = [
    {
        status: 'active',
        exported: [CHECKOUT_PRODUCT, ACCESSORY_PRODUCT],
        isActive: '1',
        stock: 'in stock',
    },
    {
        status: 'inactive',
        exported: [INACTIVE_PRODUCT],
        isActive: '0',
        stock: 'in stock',
    },
    {
        status: 'out_of_stock',
        exported: [OUT_OF_STOCK_PRODUCT],
        isActive: '1',
        stock: 'out of stock',
    },
];

test.describe.configure({ mode: 'serial', timeout: 90_000 });

test.describe('products CSV export', () => {
    test('downloads only the products matching the search term', async ({ page }) => {
        await submitAdminLogin(page, DEMO_ADMIN);
        await page.goto(
            `/admin/ecommerce/products?search=${encodeURIComponent(ACCESSORY_PRODUCT)}`,
            { waitUntil: 'domcontentloaded' },
        );

        const exportLink = page.getByRole('link', { name: /export csv/i });

        await expect(exportLink).toBeVisible();
        // The button must carry the page's search term into the export URL.
        await expect(exportLink).toHaveAttribute(
            'href',
            /[?&]search=E2E(?:\+|%20)Accessory(?:\+|%20)Product/,
        );

        const [download] = await Promise.all([
            page.waitForEvent('download'),
            exportLink.click(),
        ]);

        expect(download.suggestedFilename()).toMatch(/^products-filtered_.*\.csv$/);

        const { header, rows } = parseCsv(await readFile(await download.path(), 'utf8'));

        // Same columns as the import template, so the filtered file stays
        // re-importable.
        expect(header).toEqual(expect.arrayContaining(['product_name', 'category', 'variant_sku']));

        expect(rows.length).toBeGreaterThan(0);
        for (const row of rows) {
            expect(row.product_name).toBe(ACCESSORY_PRODUCT);
            expect(row.category).toBe(ACCESSORY_CATEGORY);
        }

        expect(rows.some((row) => row.product_name === CHECKOUT_PRODUCT)).toBe(false);
    });

    test('downloads only the products in the category picked on the page', async ({ page }) => {
        await submitAdminLogin(page, DEMO_ADMIN);
        await page.goto('/admin/ecommerce/products', { waitUntil: 'domcontentloaded' });

        // Pick the fixture category through the page's own filter, which
        // submits the form on change.
        const categoryOption = page.locator('#categoryFilter option', {
            hasText: ACCESSORY_CATEGORY,
        });
        const categoryId = await categoryOption.getAttribute('value');

        expect(categoryId).toMatch(/^\d+$/);

        await page.selectOption('#categoryFilter', categoryId);

        // waitUntil is pinned to domcontentloaded: the admin page keeps loading
        // images long enough that the default `load` event can miss the budget.
        await page.waitForURL(new RegExp(`[?&]category_id=${categoryId}`), {
            waitUntil: 'domcontentloaded',
            timeout: 20_000,
        });

        const exportLink = page.getByRole('link', { name: /export csv/i });

        await expect(exportLink).toHaveAttribute(
            'href',
            new RegExp(`[?&]category_id=${categoryId}`),
        );

        const [download] = await Promise.all([
            page.waitForEvent('download'),
            exportLink.click(),
        ]);

        expect(download.suggestedFilename()).toMatch(/^products-filtered_.*\.csv$/);

        const { rows } = parseCsv(await readFile(await download.path(), 'utf8'));

        expect(rows.length).toBeGreaterThan(0);
        for (const row of rows) {
            expect(row.product_name).toBe(ACCESSORY_PRODUCT);
            expect(row.category).toBe(ACCESSORY_CATEGORY);
        }

        expect(rows.some((row) => row.product_name === CHECKOUT_PRODUCT)).toBe(false);

        // Every exported product is one the filtered table actually lists.
        const listedProducts = (await page.locator('#productsTable tbody tr').allInnerTexts()).join('\n');

        expect(listedProducts).toContain(ACCESSORY_PRODUCT);
        expect(listedProducts).not.toContain(CHECKOUT_PRODUCT);

        for (const productName of new Set(rows.map((row) => row.product_name))) {
            expect(listedProducts).toContain(productName);
        }
    });

    test('downloads only the products matching the status picked on the page', async ({ page }) => {
        // One login, then a filter → download round trip per status value, so
        // the test needs room for three navigations.
        test.setTimeout(180_000);

        await submitAdminLogin(page, DEMO_ADMIN);

        for (const statusCase of STATUS_CASES) {
            await page.goto('/admin/ecommerce/products', { waitUntil: 'domcontentloaded' });

            // Driven through the page's own filter, which submits the form on
            // change.
            await page.selectOption('#statusFilter', statusCase.status);

            await page.waitForURL(new RegExp(`[?&]status=${statusCase.status}(?:&|$)`), {
                waitUntil: 'domcontentloaded',
                timeout: 20_000,
            });

            const exportLink = page.getByRole('link', { name: /export csv/i });

            await expect(exportLink).toHaveAttribute(
                'href',
                new RegExp(`[?&]status=${statusCase.status}(?:&|$)`),
            );

            const [download] = await Promise.all([
                page.waitForEvent('download'),
                exportLink.click(),
            ]);

            expect(download.suggestedFilename()).toMatch(/^products-filtered_.*\.csv$/);

            const { rows } = parseCsv(await readFile(await download.path(), 'utf8'));

            expect(rows.length).toBeGreaterThan(0);

            const exportedNames = new Set(rows.map((row) => row.product_name));

            // The file holds exactly the fixtures this status qualifies — not
            // just "at least" them, so a product from another status leaking in
            // fails the case.
            for (const productName of exportedNames) {
                expect(statusCase.exported, `${statusCase.status} export leaked ${productName}`)
                    .toContain(productName);
            }

            for (const productName of statusCase.exported) {
                expect(exportedNames.has(productName), `${statusCase.status} export is missing ${productName}`)
                    .toBe(true);
            }

            for (const row of rows) {
                expect(row.is_active).toBe(statusCase.isActive);
                expect(Number(row.stock_quantity) > 0, `${row.product_name} stock does not match ${statusCase.status}`)
                    .toBe(statusCase.stock === 'in stock');
            }

            // Every exported product is one the filtered table actually lists.
            const listedProducts = (await page.locator('#productsTable tbody tr').allInnerTexts()).join('\n');

            for (const productName of exportedNames) {
                expect(listedProducts).toContain(productName);
            }
        }
    });
});

/**
 * Read the export back as objects keyed by header column. fputcsv quotes any
 * value containing a comma or quote (descriptions, tags, options), so a plain
 * split(',') would shift columns.
 */
function parseCsv(csv) {
    const lines = csv
        .replace(/^\uFEFF/, '')
        .trim()
        .split(/\r?\n/)
        .map((line) => line.replace(/\r$/, ''))
        .filter((line) => line !== '');

    const header = splitCsvLine(lines[0]);

    const rows = lines.slice(1).map((line) => {
        const cells = splitCsvLine(line);

        return Object.fromEntries(header.map((column, index) => [column, cells[index] ?? '']));
    });

    return { header, rows };
}

function splitCsvLine(line) {
    const cells = [];
    let cell = '';
    let quoted = false;

    for (let index = 0; index < line.length; index++) {
        const character = line[index];

        if (quoted) {
            if (character === '"' && line[index + 1] === '"') {
                cell += '"';
                index++;
            } else if (character === '"') {
                quoted = false;
            } else {
                cell += character;
            }
        } else if (character === '"') {
            quoted = true;
        } else if (character === ',') {
            cells.push(cell);
            cell = '';
        } else {
            cell += character;
        }
    }

    cells.push(cell);

    return cells;
}
