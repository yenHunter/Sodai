<p align="center">
  <img src="public/images/logo-black.png" alt="Sodai" width="220">
</p>

<h1 align="center">Sodai — Single Vendor E-Commerce Platform</h1>

<p align="center">
  A full-featured, admin-first e-commerce application built with Laravel 13.
  Developed as a portfolio project to demonstrate real-world backend architecture,
  role-based access control, variant-based product modeling, and test-driven development.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap" alt="Bootstrap">
  <img src="https://img.shields.io/badge/Vite-7.x-646CFF?logo=vite" alt="Vite">
  <a href="../../actions"><img src="https://github.com/yenHunter/sodai/actions/workflows/laravel.yml/badge.svg" alt="CI"></a>
</p>

---

## About Sodai

Sodai is a single-vendor e-commerce platform with a completely separated admin panel and
customer storefront, each with its own authentication guard. The project is currently
**admin-first**: the admin panel is feature-complete for core catalog and order operations,
while the customer-facing storefront is next on the roadmap.

This is a **learning and portfolio project** — it is not deployed to production, and some
areas (payment gateway, transactional customer emails, storefront UI) are intentionally
incomplete. See [`.github/project-instructions.md`](.github/project-instructions.md) for a
detailed, continuously-updated breakdown of what's built vs. what's planned.

## Key Features

**Admin Panel**
- Role-based access control (Spatie Permission) with 5 seeded roles and granular permissions
- Product catalog with **variant support** (Color/Size/Weight/custom options), auto-generated
  SKUs, image galleries scoped per-variant or shared, related products, tags
- Category (2-level) and Brand management
- Order management with a POS-style builder: live AJAX shipping/tax/coupon calculation,
  editable overrides, full status-history audit trail
- Coupon engine (percentage/fixed, usage limits, validity windows)
- Refund workflow, independent from order cancellation, with approval/rejection
- Product review moderation with automatic rating recalculation
- Abandoned cart visibility + customer reminder emails
- CMS banners (position + scheduling based)
- A 10-group Settings module (company, design, shipping, payment, inventory, invoice,
  order rules, tax, notifications, marketing/SEO/social) — all backed by a generic,
  cached key-value `Setting` model
- reCAPTCHA v3 + rate limiting on all authentication surfaces (admin and customer)

**Engineering practices**
- Thin controllers, business logic in a dedicated Service layer
- ~15 PHPUnit Feature test suites covering permissions, validation edge cases, and
  business rules (e.g. stock locking on order creation, coupon eligibility, refund balances)
- Consistent Form Request validation, soft deletes on core models, security headers + CSP

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13.17, PHP 8.3 |
| Database | SQLite (dev), MySQL-ready |
| Authorization | Spatie Laravel Permission |
| Image processing | Intervention Image v4 (WebP conversion) |
| Frontend | Blade, Bootstrap 5.3 (UBold admin theme), Vite |
| Testing | PHPUnit 12 |

## Getting Started

```bash
git clone https://github.com/yenHunter/Sodai.git
cd sodai
composer run setup   # installs deps, copies .env, generates key, migrates, builds assets
```

Or step by step:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install && npm run build
php artisan migrate --seed
```

Run the dev environment (server + queue listener + logs + Vite, all at once):

```bash
composer run dev
```

Default seeded accounts (see `database/seeders/AdminSeeder.php` / `CustomerSeeder.php`
for current values — change these before any shared/public deployment).

## Running Tests

```bash
composer test
# or target a suite
php artisan test --filter=ProductModuleTest
```

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

## Roadmap

See [`.github/project-instructions.md`](.github/project-instructions.md) for the full,
up-to-date backlog. Near-term priorities:

1. Wire the storefront views to real catalog/cart/order data
2. Payment gateway integration
3. Transactional customer emails (order confirmation, shipment updates)
4. Admin dashboard KPIs
5. CMS static page content (privacy policy, terms, shipping/refund policy)

## Connect & Support

[![LinkedIn](https://img.shields.io/badge/LinkedIn-Connect-blue?style=for-the-badge&logo=linkedin)](https://www.linkedin.com/in/firoz-ebna-jobaier)
[![Buy Me a Coffee](https://img.shields.io/badge/Buy_Me_a_Coffee-Support-yellow?style=for-the-badge&logo=buymeacoffee)](buymeacoffee.com/yenHunter)
[![Fork me on GitHub](https://img.shields.io/badge/Fork_on_GitHub-000?style=for-the-badge&logo=github)](https://github.com/yenHunter)

## Contributing

This is currently a solo portfolio project, but the [contribution guide](CONTRIBUTING.md)
documents the branching strategy and coding standards used, in case that changes.

## License

This project is built on the Laravel framework, which is open-sourced under the
[MIT license](https://opensource.org/licenses/MIT). Application-specific code follows
the same license unless noted otherwise.