import { defineConfig, devices } from '@playwright/test';

const port = Number(process.env.PLAYWRIGHT_PORT ?? 8000);
const baseURL = process.env.PLAYWRIGHT_BASE_URL ?? `http://127.0.0.1:${port}`;

/**
 * Browser smoke tests for the storefront and admin entry points.
 *
 * The web server is started automatically unless PLAYWRIGHT_BASE_URL points
 * at an already-running instance. Locally, an existing `php artisan serve`
 * on port 8000 is reused; in CI a fresh server is always started.
 */
export default defineConfig({
    testDir: './tests/e2e',
    // `php artisan serve` is a single-threaded PHP process; parallel workers
    // queue asset requests and produce flaky navigation timeouts.
    fullyParallel: false,
    workers: 1,
    forbidOnly: Boolean(process.env.CI),
    retries: process.env.CI ? 2 : 0,
    reporter: [
        ['list'],
        ['html', { open: 'never' }],
    ],
    use: {
        baseURL,
        trace: 'on-first-retry',
        screenshot: 'only-on-failure',
        video: 'retain-on-failure',
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
    ],
    webServer: process.env.PLAYWRIGHT_BASE_URL
        ? undefined
        : {
              command: `php artisan serve --host=127.0.0.1 --port=${port}`,
              url: baseURL,
              reuseExistingServer: !process.env.CI,
              timeout: 120_000,
              stdout: 'pipe',
              stderr: 'pipe',
          },
});
