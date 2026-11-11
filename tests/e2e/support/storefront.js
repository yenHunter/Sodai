export const PRODUCT_SLUG = 'e2e-checkout-product';
export const COUPON_CODE = 'E2ECHECKOUT';

/**
 * Add the seeded product with the product page's own form. The form is
 * submitted natively so the test does not depend on any AJAX enhancement.
 */
export async function addProductToCart(page) {
    await page.goto(`/products/${PRODUCT_SLUG}`, { waitUntil: 'domcontentloaded' });
    await page.waitForSelector('#addToCartForm');

    // The redirect returns to the same product URL, so wait for the POST
    // itself instead of matching the current URL (which would resolve early
    // and let the next navigation abort the add-to-cart request).
    const addResponse = page.waitForResponse(
        (response) => response.url().includes('/cart/add') && response.request().method() === 'POST',
    );

    await page.evaluate(() => {
        document.getElementById('addToCartForm').submit();
    });

    const response = await addResponse;

    if (response.status() !== 302) {
        throw new Error(`Add-to-cart failed with HTTP ${response.status()}`);
    }

    await page.waitForURL(new RegExp(`/products/${PRODUCT_SLUG}$`));
}
