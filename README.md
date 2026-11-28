<p align="center">
  <img src="public/images/logo-black.png" alt="Sodai" width="200">
</p>

<h1 align="center">Sodai</h1>

<p align="center">
  <strong>Open-source Laravel 13 e-commerce platform: a customer storefront and a
  permission-gated admin panel around a variant-based catalog, covered by 459 tests.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap" alt="Bootstrap">
  <img src="https://img.shields.io/badge/Vite-7.x-646CFF?logo=vite" alt="Vite">
  <a href="https://github.com/yenHunter/Sodai/actions/workflows/laravel.yml"><img src="https://github.com/yenHunter/Sodai/actions/workflows/laravel.yml/badge.svg" alt="CI"></a>
  <img src="https://img.shields.io/badge/tests-459%20passing-2ea44f" alt="459 tests passing">
</p>

<p align="center">
  <a href="#quick-start">Quick start</a> ·
  <a href="#screenshots">Screenshots</a> ·
  <a href="#highlights">Features</a> ·
  <a href="#why-sodai">Why Sodai?</a> ·
  <a href="#tech-stack">Tech stack</a> ·
  <a href="#documentation">Documentation</a>
</p>

<p align="center">
  <img src="docs/screenshots/storefront-home.png" alt="Sodai storefront homepage" width="880">
</p>

**Live demo:** coming soon. Until it is hosted, the [quick start](#quick-start) below
brings the seeded demo catalog up locally in two commands.

> **License note:** Sodai's own code is MIT. The bundled **UBold** admin theme and
> **Ekka** storefront theme are commercially licensed on ThemeForest and are excluded
> from that grant — see [License](#license).

## What is Sodai?

Sodai is a single-vendor e-commerce platform split into two applications with their own
authentication guards: a Blade storefront for customers, and a separate admin panel behind
Spatie roles and granular permissions. It covers what a shop actually needs — scheduled
CMS banners and a filtered variant catalog, guest or registered COD checkout with real
stock locking, order tracking and cancellation, review moderation, refunds, CSV catalog
and order import/export, and a ten-group settings module.

It is a portfolio project rather than a hosted product: payment is Cash On Delivery only,
and the demo data is synthetic. The interesting part is the backend — a service layer that
writes orders inside a transaction, object-level authorization on every customer resource,
and a test suite that pins the business rules down. See
[`.github/project-instructions.md`](.github/project-instructions.md) for a continuously
updated breakdown of what is built versus planned.

## Quick start

Requirements: PHP 8.3+, Composer 2, Node 20+, and a database (SQLite by default).

```bash
git clone https://github.com/yenHunter/Sodai.git
cd Sodai
composer run setup          # install, .env, key, migrate, npm install, build assets
php artisan storage:link
php artisan demo:seed       # optional: the full demo catalog
composer run dev            # server + queue listener + logs + Vite
```

`composer run setup` expands to `composer install`, `.env` creation, `key:generate`,
`migrate --force`, `npm install`, and `npm run build`. The step-by-step equivalent:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install && npm run build
php artisan migrate --seed
```

After `php artisan demo:seed`, sign in as `demo-admin@example.com` / `Demo#12345`
(super admin) or `demo-customer@example.com` / `Demo#12345`. The normal development
accounts come from `database/seeders/AdminSeeder.php` and `CustomerSeeder.php` — change
them before any shared deployment.

### Demo catalog

`php artisan migrate --seed` installs only the normal development data. For a full
storefront — categories, brands, variant products with photos, banners, customers, orders
in every status, reviews, a coupon and a refund:

```bash
php artisan demo:seed        # or: php artisan db:seed --class=DemoCatalogSeeder
```

Set `SEED_DEMO_PASSWORD` to replace the demo password; outside local/testing the seeder
refuses to create accounts without it. `DEMO_DATE=2026-01-15` pins every relative date so
the history reproduces exactly for screenshots and tests.

Undo a demo run, and only that run, with `php artisan demo:clear` (`--dry-run` previews
it). Demo rows use their own identifiers (`DEMO-ORD-…` order numbers, `demo-…` slugs,
`DEMO-…` SKUs, `@example.com` accounts), and the command never deletes a real record: a
demo product still referenced by a real order, a demo coupon on a real order, or any
setting a shop has since changed is kept and reported. Settings the seeder created are
removed with it, and the demo photos copied into `storage/app/public` are cleaned up too.

## Screenshots

Every screenshot is captured from the seeded demo catalog.

| | |
|---|---|
| ![Storefront homepage](docs/screenshots/storefront-home.png) | ![Product page with variant selection](docs/screenshots/product-variants.png) |
| **Cart** | **Admin — order management** |
| ![Cart](docs/screenshots/cart.png) | ![Admin orders list](docs/screenshots/admin-orders.png) |
| **Admin — roles and permissions** | |
| ![Role permission matrix](docs/screenshots/admin-roles-permissions.png) | |

## Highlights

**Storefront**
- Homepage driven by real data: scheduled CMS banners, featured/new/limited-time products,
  top categories
- Catalog with category/option/color/size/price filters and sorting; product pages with a
  variant matrix
- Guest or registered checkout, COD, with live shipping, tax and coupon validation
- Customer area: order history and timeline, cancellation, addresses, reviews, wishlist,
  profile and password

**Admin**
- Spatie RBAC with granular permissions
- Variant-based product catalog (Color/Size/Weight/custom), generated SKUs, per-variant
  galleries, related products, tags, CSV import/export
- POS-style order builder, status-history audit trail, refund workflow, filtered CSV export
- Coupons, review moderation, abandoned-cart reminders, sales report emails, CMS banners,
  a ten-group cached settings module, and reCAPTCHA v3 plus rate limiting on every auth
  surface

**Engineering**
- Thin controllers with business logic in a dedicated service layer; Form Request
  validation and soft deletes on core models
- How the variant model, the two auth guards, RBAC, the checkout transaction and the
  security checks are built — and which test pins each one — is in
  [Why Sodai?](#why-sodai)

<details>
<summary>Full feature list</summary>

**Storefront (Customer)**
- Homepage wired to real data: banners (scheduled CMS placements), featured/new/limited-time
  products, top categories
- Catalog with category/option/color/size/price filters and sorting, product detail pages
  with variant matrix
- Guest or registered checkout (guest checkout toggle in Settings), COD payment,
  live shipping charge/free-shipping threshold/tax and coupon validation via throttled AJAX
- Customer account area: order history + timeline tracking, cancellation of pending orders,
  addresses (default management), reviews (only after delivered purchase), wishlist,
  profile/password management
- Email verification at registration; set-password bootstrap for admin-created customers
- CMS-backed policy pages (privacy, terms, shipping, return/refund) editable in admin
- Admin-brandable storefront: company name, logos (light/dark/favicon), SEO meta, and
  footer contact details all resolve from Settings → Branding with template defaults

**Admin Panel**
- Role-based access control (Spatie Permission) with 5 seeded roles and granular permissions
- Product catalog with **variant support** (Color/Size/Weight/custom options), auto-generated
  SKUs, image galleries scoped per-variant or shared, related products, tags
- Bulk product **CSV import/export**: re-importable catalog export (all products, or just the
  rows matching the current search/category/status filter), downloadable template, and an importer
  that matches variants by SKU and reports skipped rows with line numbers
- Category (2-level) and Brand management
- Order management with a POS-style builder: live AJAX shipping/tax/coupon calculation,
  editable overrides, full status-history audit trail, and a CSV export of the currently
  filtered order list (search, status, date range)
- Coupon engine (percentage/fixed, usage limits, validity windows)
- Refund workflow, independent from order cancellation, with approval/rejection
- Product review moderation with automatic rating recalculation
- Abandoned cart visibility + automatic reminder emails (30-minute scheduled job)
- Sales report emails: weekly (Mon–Sun) and monthly revenue summaries to the admin inbox
- CMS banners (position + scheduling based)
- A 10-group Settings module (company, design, shipping, payment, inventory, invoice,
  order rules, tax, notifications, marketing/SEO/social) — all backed by a generic,
  cached key-value `Setting` model
- reCAPTCHA v3 + rate limiting on all authentication surfaces (admin and customer)

</details>

## Why Sodai?

Sodai is built like a shop that has to take real orders, not a CRUD demo. Six things are
worth reading in the code.

**Variants are modelled, not flattened.** A product owns `product_options` (`Color`,
`Size`, `Weight`, or a custom option) and their `product_option_values` (with an optional
swatch); each `product_variants` row joins them through `product_variant_option_values`.
A variant carries its own SKU, price, purchase price, discount, stock, low-stock threshold,
weight and thumbnail, plus `is_default` / `is_active` flags. The product row only caches
`min_price`, `max_price` and `total_stock`, recomputed on every stock change
(`Product::refreshPriceAndStockCache`). Order lines snapshot what was sold — product name,
SKU, image and a readable `variant_options` string — and `order_items.product_variant_id`
is `nullOnDelete`, so an order still renders correctly after the catalog is edited or a
variant retired.

**Two guards, two identity tables.** Customers authenticate through the `customer` guard
on the `User` model, staff through the `admin` guard on the `Admin` model — not one users
table with an `is_admin` flag. Password reset is split the same way: separate token tables,
60-minute tokens for customers and 30-minute tokens for admins. Middleware re-checks
account state on every request, so a deactivated admin is logged out by
`AdminAuthenticated`, and `CustomerAuthenticated` enforces verified email, ban and lock.
Admin accounts also support TOTP two-factor with recovery codes.

**RBAC is enforced per route.** Spatie permissions live on the `admin` guard, seeded as
**65 permissions across 19 groups and 5 roles** (`super-admin`, `manager`, `order-manager`,
`content-editor`, `support`). Routes are gated by `CheckAdminPermission`, which accepts
any of the permission strings it is given and short-circuits for `super-admin`. The
navigation and buttons read those same permission names, so the UI cannot drift from the
route rules.

**Checkout is one transaction or nothing.** `CheckoutService::placeOrder` runs inside
`DB::transaction`: every cart line re-reads its variant with `lockForUpdate()`, then
re-checks availability and stock against that locked row instead of trusting the cart; the
coupon is locked and re-validated; the order number is allocated under a lock; stock is
decremented, product sales and price/stock caches updated, the status-history row written
and the cart cleared — all before commit. Any failure rolls the whole order back. The
confirmation email is queued *after* the commit, so a mail outage cannot roll back an
order and a rolled-back order is never emailed.

**Security decisions live in code, not config.** `HtmlSanitizer` is a whitelist, not a
blacklist, for admin-entered rich text rendered with `{!! !!}`: unknown tags and `on*`
handlers are dropped, `href`/`src` are limited to http(s), mailto and tel after double
URL-decoding, and `data:` is only accepted for images. Uploaded SVGs are stripped of
script and `foreignObject`. `SecurityHeaders` applies CSP, `nosniff`, `SAMEORIGIN`,
referrer and permissions policies to every response, plus HSTS in production. Each
customer resource (order, review, address, cart, wishlist) is checked with the
`EnsuresCustomerOwnership` trait and answers 403 on a foreign id. Uploads are
`mimes`/`max`-validated images stored under UUID filenames, no source maps ship to the
browser, authentication plus checkout and coupon endpoints are throttled, and
`composer audit` fails the build on a known advisory.

**The rules are pinned by tests.** The suites are behavioural, not smoke tests:
`VisitorCheckoutTest` covers stock exhaustion discovered
*after* add-to-cart, a variant deactivated mid-session, coupon minimums and per-user
limits, full cart clearing and a queued confirmation delivered by a real worker;
`SecurityHardeningTest` covers the sanitizer, variant-matrix JSON that tries to break out
of its `<script>` tag, a `javascript:` CTA URL, and SVG logos stored without active
content.

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13.x, PHP 8.3 |
| Database | SQLite (dev), MySQL-ready |
| Authorization | Spatie Laravel Permission |
| Image processing | Intervention Image v4 (WebP conversion) |
| Frontend | Blade, Bootstrap 5.3 (UBold admin theme), Vite |
| Jobs / scheduling | Database queue, Laravel scheduler |
| Testing | PHPUnit 12, Playwright for browser flows |

## Testing

```bash
composer test                      # 459 tests, 1,899 assertions
php artisan test --filter=ProductModuleTest
```

Tests run against in-memory SQLite, so no database setup is needed. The feature suites
cover permissions and validation edges as well as business rules (stock locking, coupon
eligibility, refund balances, report window anchoring); the unit suites cover pure
services. Every pull request runs five GitHub Actions jobs: Pint style, asset build,
the PHP 8.3 test suite, a Playwright browser run, and a blocking `composer audit`.

## Project Structure Highlights

```
app/
├── Http/Controllers/Admin/     # Thin admin controllers
├── Http/Controllers/Visitor/   # Customer-facing controllers
├── Services/Admin/             # Admin business logic
├── Services/Visitor/           # Customer-facing business logic
├── Models/                     # Eloquent models (variant-based product graph)
routes/
├── admin.php                   # Prefixed /admin, permission-gated
├── visitor.php                 # Customer routes
├── web.php                     # Public + legacy template demo routes
tests/Feature/                  # Module-level test suites
```

## Documentation

- [`docs/queue-operations.md`](docs/queue-operations.md) — workers, retries, and the
  scheduled queue and failure alerts
- [`docs/demo-mode.md`](docs/demo-mode.md) — running a public demo that cannot send real
  email
- [`deploy/supervisor/sodai-queue-worker.conf`](deploy/supervisor/sodai-queue-worker.conf) —
  production queue worker program
- [`.github/project-instructions.md`](.github/project-instructions.md) — what is built
  versus planned, plus the full backlog
- [`CONTRIBUTING.md`](CONTRIBUTING.md) — branching strategy and coding standards
- [`THIRD-PARTY-NOTICES.md`](THIRD-PARTY-NOTICES.md) — bundled asset licenses

## Roadmap

See [`.github/project-instructions.md`](.github/project-instructions.md) for the full,
up-to-date backlog. Near-term priorities:

1. Payment gateway integration (COD is the only payment method today)
2. Admin dashboard KPIs (reports module is shipped, dashboard view is still placeholder)
3. Shipment update emails and broader queued customer notifications (guest order confirmation is shipped)
4. Storefront header polish: real category mega-menu, live search, wishlist count badge
5. 2FA for admin accounts

## Connect & Support

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Connect-blue?style=for-the-badge&logo=linkedin)](https://www.linkedin.com/in/firoz-ebna-jobaier)
[![Buy Me a Coffee](https://img.shields.io/badge/Buy_Me_a_Coffee-Support-yellow?style=for-the-badge&logo=buymeacoffee)](https://buymeacoffee.com/yenHunter)
[![Fork me on GitHub](https://img.shields.io/badge/Fork_on_GitHub-000?style=for-the-badge&logo=github)](https://github.com/yenHunter)

## Contributing

This is currently a solo portfolio project, but the [contribution guide](CONTRIBUTING.md)
documents the branching strategy and coding standards used, in case that changes.

## License

Sodai's own source code — the application, routes, migrations, seeders, tests and
documentation — is released under the [MIT license](LICENSE).
Copyright (c) 2026 Firoz Ebna Jobaier. It is built on the
[Laravel framework](https://laravel.com), which is MIT-licensed as well.

The two UI templates bundled with the repository are **not** MIT and are excluded from
that grant:

- **UBold** — admin template by Coderthemes (admin panel)
- **Ekka** — storefront template by ashishmaraviya (customer storefront)

Both are commercially licensed on ThemeForest. They are committed so the application can
be run and reviewed, but reusing them requires your own license, or replacing them.
Composer and npm dependencies keep their own licenses. See
[THIRD-PARTY-NOTICES.md](THIRD-PARTY-NOTICES.md) for the full audit.
