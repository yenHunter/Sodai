# `project-instructions.md`

# Project Instructions for LLM Models
# Sodai — Single Vendor E-Commerce (Admin-first build)

---

## IMPORTANT NOTE FOR LLM MODELS
This file describes the **actual current state** of the codebase (verified against
the real source, not aspirational). Read it fully before suggesting or writing code.
Do not assume anything is "pending" without checking the file tree first — several
modules previously marked pending in older versions of this doc are already built.

---

## 1. PROJECT OVERVIEW

- **Project Name:** Sodai (Single Vendor E-Commerce)
- **Stage:** Active development, pre-production. Admin panel is the current focus.
- **Backend:** Laravel 13.17, PHP 8.3
- **Frontend:** Blade + Bootstrap 5.3 (UBold admin theme by Coderthemes) + Vite
- **DB (dev):** SQLite (`database/database.sqlite`), MySQL-ready via `config/database.php`
- **Auth:** Two fully separate guards — `admin` (table `admins`) and `customer` (table `users`, model `App\Models\User`)
- **Authorization:** Spatie Laravel Permission, guard `admin`, 5 seeded roles
- **Image handling:** `intervention/image` v4 + Laravel wrapper, images converted to WebP on upload
- **Testing:** PHPUnit 12, ~15 Feature test suites under `tests/Feature`, using `AdminTestHelpers` trait
- **CI:** GitHub Actions (`.github/workflows/laravel.yml`) — SQLite-based test run

---

## 2. ARCHITECTURE — VERIFIED CURRENT STATE

### 2.1 Authentication
- `admin` guard → `Admin` model → `admins` table. No public registration; seeded via `AdminSeeder`.
- `customer` guard → `User` model → `users` table. Public registration, email verification
  (`MustVerifyEmail`), ban/lock support (`status`, `failed_login_attempts`, `locked_until`).
- Default guard in `config/auth.php` is `customer`.
- reCAPTCHA v3 (`App\Rules\ReCaptcha`) applied on admin login/forgot/reset AND customer
  login/register/forgot/reset — action names differ per form (`admin_login`, `customer_login`, etc.)
- Rate limiting via Laravel `RateLimiter` facade, keyed per-guard and per-IP/email.
- Admin "set password" flow exists for admin-created customers (`CustomerSetPasswordMail`),
  separate from the customer self-service reset flow.

### 2.2 Routing
- `routes/web.php` — visitor/public + many legacy UBold template demo routes (non-functional demo pages,
  safe to delete once no longer needed for reference).
- `routes/admin.php` — registered with `prefix('admin')` + `name('admin.')` in `bootstrap/app.php`.
  All admin routes gated by `auth.admin` + `prevent.back.history` middleware, and per-resource
  `permission:<name>` middleware (Spatie).
- `routes/visitor.php` — registered with `name('visitor.')`, no prefix. Guest routes wrapped in
  `guest:customer`; authenticated routes in `auth.customer`.

### 2.3 Product Data Model (IMPORTANT — variant-based, not flat)
```
Product          — name, slug, category_id, brand_id, thumbnail, is_active, is_featured,
                    min_price/max_price/total_stock (denormalized cache from variants),
                    average_rating, review_count, total_sales, meta (json)
ProductVariant    — belongs to Product. price, purchase_price, discount_type/value,
                    stock_quantity, low_stock_threshold, weight, thumbnail, is_default, is_active, sku
ProductOption          — e.g. "Color", "Size" (admin-defined, reusable across products)
ProductOptionValue     — e.g. "Red", "XL", with optional swatch hex
ProductVariantOptionValue (pivot) — links a variant to its option value combination
ProductImage      — product_id + nullable product_variant_id (null = shared gallery image,
                    set = variant-specific image), is_primary, sort_order
```
- `Product::refreshPriceAndStockCache()` must be called after any variant create/update/delete —
  this keeps `min_price`/`max_price`/`total_stock` accurate for listing/filtering without joins.
- A product with **zero real variants** still gets one auto-created "default" variant
  (see `ProductFactory::configure()` and `ProductService::defaultVariantPayload()`), so
  simple (non-configurable) products work the same way as configurable ones.
- SKUs are variant-level and auto-generated from category name + sequence
  (`ProductService::generateUniqueVariantSku()`).

### 2.4 Order / Cart / Checkout Data Model
- `Order` → `OrderItem` (snapshotted product name/sku/image/price at time of order) →
  optional `product_variant_id`.
- `OrderStatusHistory` — audit trail of every status transition, with `changed_by` (admin) and note.
- `Cart` (per-user or per-session guest cart) → `CartItem` (variant-level, unique per cart+variant).
- `Refund` — separate from order cancellation; has its own approve/reject workflow
  (`RefundService`) that, on approval, transitions the linked order to `refunded` and can restore stock.
- Coupons (`Coupon`) support percentage/fixed, min order amount, max discount cap, usage limits
  (total + per-customer), validity windows. Applied via AJAX preview in the admin POS-style order form.
- Shipping charge and tax are **computed from Settings** (`SettingService::resolveShippingCharge()`,
  tax group) unless the admin explicitly overrides the field in the order form — this dual-mode
  (auto vs. manual override) is intentional and tested (`OrderModuleTest`).

### 2.5 Admin Module Status (verified from code, not assumed)

| Module | Status | Notes |
|---|---|---|
| Admin auth (login/forgot/reset) | ✅ Done | reCAPTCHA v3, rate limited |
| Dashboard | ⚠️ Placeholder | `DashboardController` just renders a static view, no real KPIs wired |
| Roles & Permissions | ✅ Done | Spatie-based, super-admin bypass, protected role guard |
| Admin user management | ✅ Done | Self-protection guards (can't delete/deactivate self, can't remove last super-admin) |
| Categories | ✅ Done | 2-level nesting enforced in validation, image upload, soft delete |
| Brands | ✅ Done | |
| Products (with variants) | ✅ Done | Variant matrix builder, tag autocomplete, related products (Select2 AJAX), image dropzones |
| Attributes (Color/Size/Weight toggle) | ✅ Done | Controls which field groups render on product form |
| Orders | ✅ Done | POS-style create/edit, live shipping/tax/coupon AJAX preview, status history |
| Customers (admin-side) | ✅ Done | Admin-created customers get a "set password" email flow |
| Cart (admin view of abandoned carts) | ✅ Done | Read-only + reminder email + delete |
| Coupons | ✅ Done | |
| Refunds | ✅ Done | |
| Reviews (moderation) | ✅ Done | Approve/reject recalculates product rating |
| Banners (CMS) | ✅ Done | Position-based (slider/promo/category/popup), scheduling window |
| Settings / Configuration | ✅ Done | 10 groups, generic `SettingService` with cache-per-group |
| CMS static pages (privacy/terms/shipping/refund policy) | ❌ Not started | Blade files exist but are **empty stubs** |
| Reports | ❌ Not started | No controller, no route, listed in permission seeder only |
| File Manager / Chat / Calendar (sidebar links) | ❌ Template demo only | Not wired to real features — decide whether to build or remove from sidebar |

### 2.6 Visitor/Storefront Module Status
- Controllers + services exist for: product catalog + filters (`ProductCatalogService`),
  cart, wishlist, reviews, addresses, account/profile, order history.
- **Views are largely still the raw UBold template markup**, not wired to the real
  visitor controllers/data. This is the single biggest gap before this becomes a
  demoable storefront.
- No payment gateway is integrated. `Setting::get('payment', 'online_payment_enabled')`
  exists as a toggle only — no Stripe/SSLCommerz/bKash/Nagad code.
- No order-confirmation/shipped/delivered transactional emails to the customer
  (only `CartReminderMail` and `CustomerSetPasswordMail` exist).

### 2.7 Role-Permission Matrix
- Guard: `admin`. Roles: `super-admin`, `manager`, `order-manager`, `content-editor`, `support`.
- Full permission set is seeded in `RolePermissionSeeder` (25+ permissions across
  dashboard/category/brand/product/order/customer/cart/refund/attribute/coupon/banner/review/report/setting/admin/role).
- Blade directives: `@admincan`, `@admincanany`, `@adminrole` (registered in `AppServiceProvider::boot()`).
  **Always use these over `@can`/`@role` in admin views.**

### 2.8 Known Inconsistencies / Tech Debt (fix opportunistically)
1. Currency is hardcoded as `$` in ~20+ Blade files despite `Setting::get('company','currency')`
   defaulting to BDT. Introduce a `money()` / `format_price()` helper and replace hardcoded `$`.
2. No queued jobs anywhere — all mail sends and heavy operations run synchronously in the request.
   Fine for dev, must be addressed before production (`QUEUE_CONNECTION=database` is already configured).
3. `routes/web.php` still contains ~150 lines of unused UBold template demo routes
   (charts, tables, layouts, plugins showcase). Safe to prune once no longer needed as reference.
4. No `.env.testing` — `phpunit.xml` inlines test env vars instead, which is fine but worth
   knowing when adding new required env vars.
5. `laravel/sail` is a dev dependency but there's no `docker-compose.yml` — Sail was never
   actually initialized (`sail:install` not run).

---

## 3. NAMING & CONVENTIONS (unchanged from original, confirmed still followed)

```
Controllers:  PascalCase → ProductController.php
Models:       PascalCase singular → Product.php
Migrations:   snake_case, dated
Routes:       kebab-case URI → /admin/product-categories
Route names:  dot notation → admin.ecommerce.product.index
Blade views:  kebab-case → product-details.blade.php
JS files:     kebab-case → admin-ecommerce-product-index.js
```

- Form Requests always used for validation (never inline `$request->validate()` in admin controllers
  — exception: a few small AJAX endpoints in `OrderController`/`ProductController` validate inline,
  which is acceptable for lightweight AJAX-only inputs).
- Services layer (`app/Services/Admin/*`, `app/Services/Visitor/*`) holds all business logic;
  controllers stay thin and delegate.
- `$fillable` always used, never `$guarded`.
- SoftDeletes on: Admin, User, Product, ProductVariant, Category, Brand, Order, Coupon, Refund, Banner.
- Every admin list/detail Blade uses `@admincan` gates around action buttons, matching route middleware.

---

## 4. SECURITY MEASURES IN PLACE (confirmed in code)

```
reCAPTCHA v3                  ✅  Admin + Customer: login, forgot, reset, register
Rate Limiting                 ✅  Per guard, keyed by email+IP
Session regen on login        ✅
Session invalidate on logout  ✅
CSRF                          ✅  All forms
Password hashing              ✅  bcrypt via 'hashed' cast
Security headers + CSP        ✅  SecurityHeaders middleware (global)
Prevent-back-history           ✅  Admin authenticated routes
Email enumeration prevention  ✅  Same message regardless of match
Token hashing                 ✅  Reset tokens hashed in DB
Soft deletes                  ✅  Key models
RBAC                          ✅  Spatie Permission
Ownership checks (customer)   ✅  EnsuresCustomerOwnership trait on visitor controllers
```

**Not yet addressed (flag before production):**
- No CSP `report-uri`, no rate limiting on public API-like AJAX endpoints (product search, tag search).
- No explicit file-upload MIME sniffing beyond Laravel's `image`/`mimes` rules.
- No 2FA for admin accounts.

---

## 5. USEFUL COMMANDS (unchanged, still accurate)

```bash
# Dev
composer run dev            # server + queue:listen + pail + vite, concurrently

# Database
php artisan migrate:fresh --seed
php artisan migrate:status

# Cache
php artisan optimize:clear

# Tests
composer test                # config:clear + php artisan test
php artisan test --filter=ProductModuleTest
```

---

## 6. DEVELOPMENT PROGRESS TRACKER

Keep this section updated as work progresses — this is the source of truth for "what's done."

### ✅ Completed
- Admin auth, RBAC, admin user management
- Category, Brand, Product (+variants/options/attributes), Order, Customer (admin-side),
  Cart (admin view), Coupon, Refund, Review, Banner, Attribute, Settings (10 groups)
- Full Feature test suite for all of the above

### ⏳ In Progress / Next Up
See Part 5 (To-Do List & Missing Modules) of the accompanying analysis for the prioritized backlog.

### ❌ Not Started
- Admin Dashboard real KPIs/widgets
- CMS static pages (privacy/terms/shipping/refund policy content)
- Reports module
- Visitor/storefront view wiring to real data
- Payment gateway integration
- Transactional customer emails (order confirmation, shipped, delivered)
- Queue-based email dispatch
- API layer

---

*Last verified against source: current repo snapshot analyzed turn-by-turn, PHP 8.3 / Laravel 13.17.*