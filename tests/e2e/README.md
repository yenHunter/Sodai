# End-to-end tests

Playwright smoke and authentication tests for the storefront and admin panel.

## Local run

Use a throwaway SQLite database so the development database is untouched. The
`E2E_SEED` flag adds the deterministic MFA admin used by `admin-auth.spec.js`;
it is never seeded in production.

```bash
E2E_SEED=true DB_CONNECTION=sqlite DB_DATABASE=database/e2e.sqlite \
  php artisan migrate:fresh --seed --force

E2E_SEED=true DB_CONNECTION=sqlite DB_DATABASE=database/e2e.sqlite \
  npm run test:e2e
```

On Windows Git Bash the same multi-line `VAR=value command` syntax works. The
config starts `php artisan serve` on `127.0.0.1:8000` automatically, or reuses
an existing server on that port.

- `npm run test:e2e` — headless run.
- `npm run test:e2e:ui` — Playwright UI mode.

## Seeded accounts

| Account | Email | Password |
|---|---|---|
| MFA-enabled admin | `e2e-admin@example.com` | `E2ePassword#123` |
| Regular demo admin | `admin@cyberjatra.com` | `Admin#123` |

The MFA admin's TOTP secret and recovery codes are fixed in
`database/seeders/E2eSeeder.php`; `tests/e2e/support/totp.js` derives current
codes from that secret. **These are test-only credentials** — the seeder
requires `E2E_SEED=true` and refuses to run in production.

The same seeder creates the checkout fixtures used by `checkout.spec.js`:

| Fixture | Value |
|---|---|
| Product | `/products/e2e-checkout-product` ($100, 50 in stock) |
| Coupon | `E2ECHECKOUT` (10% off) |
| Guest checkout | enabled via the `order.allow_guest_checkout` setting |

## CI

The `e2e` job in `.github/workflows/laravel.yml` starts from a fresh SQLite
database with `E2E_SEED=true`, builds assets, installs Chromium, runs the suite
and uploads `playwright-report/` plus `test-results/`.
