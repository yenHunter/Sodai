# Sodai — Admin Section To-Do List & Missing Modules Audit

_Companion to `.github/project-instructions.md`. Update as items are completed._

_Last verified against source: 10 November 2026 (full re-audit of routes, services, views and tests)._

---

## A. Admin Section To-Do (prioritized)

### P0 — Fill functional gaps in existing modules
- [ ] **Dashboard**: still the raw theme placeholder. `DashboardController@index` only passes the
      authenticated admin to `admin.main.index`, and that view hardcodes everything: `$124.7K` total
      sales, `2358` orders, `839` customers, `41` refund requests, a dummy product-inventory table
      (Wireless Earbuds, Laptop Stand…), dummy recent orders (`#ORD-2001`…), and static `<canvas>`
      charts. Data already exists via `OrderService::getOrderStats()`,
      `ReviewService::getReviewStats()`, `RefundService::getRefundStats()` and
      `ReportService` — this is a view/controller wiring job, not new domain logic.
- [x] **Reports module**: ✅ Shipped — `ReportController` (`admin.reports.index`), sales-by-date,
      top-products and low-stock pages, with CSV exports for sales-by-date and top-products.
      `ReportService` backs the numbers; `reports:send-weekly-summary` (Mondays 08:00) and
      `reports:send-monthly-summary` (1st, 08:00) email summaries to the admin inbox, gated by the
      notification settings. Covered by `ReportModuleTest`, `WeeklySalesSummaryTest` and
      `MonthlySalesSummaryTest`.
- [ ] **Currency formatting helper**: no `format_price()` / `money()` helper exists (`app/Support/helpers.php`
      only defines `setting()`), while `currency` and `currency_symbol_position` settings are already
      stored and editable. Hardcoded `$` remains across the product/order/refund/cart Blade views.
      Add the helper (respecting symbol position + decimals) and replace the hardcoded symbols.
- [x] **CMS static pages**: ✅ Shipped — `CmsPage` model/table, admin editor (`admin.cms.pages.*`)
      and storefront rendering through `App\Services\Visitor\CmsPageService::getPage()`. Content is
      HTML-sanitized on save (`App\Support\HtmlSanitizer`); per-page meta overrides the storefront `<head>`.
- [x] **FAQ module**: ✅ Shipped on the admin side — `FaqCategory` + `Faq` models, `FaqService`,
      `FaqCategoryController` / `FaqController`, permission-gated `admin.cms.faq.*` routes, ordering,
      active toggles, bulk delete, and a delete guard that blocks removing a category that still has
      FAQs. Covered by `FaqModuleTest`. The public `/faq` page now renders this data through
      `App\Http\Controllers\Visitor\FaqController` + `App\Services\Visitor\FaqService`
      (`VisitorFaqTest`).
- [x] **Offers module**: ✅ Shipped — `Offer` model (image, scheduling window, optional category link,
      resolved CTA URL, status label), `OfferService` + `OfferController`, permission-gated
      `admin.cms.offer.*` routes with toggle/bulk delete, sidebar entry, and `OfferModuleTest`.
      The storefront `/offers` page consumes `Offer::currentlyValid()` via `HomeService::getOfferList()`.
- [x] **Shared pricing core**: ✅ Done — `App\Services\Shared\PricingService` is now the single source
      of truth for order totals, tax (including `prices_include_tax` handling), settings-driven
      shipping, coupon lock/validation/discount, order-number generation and the order-item snapshot.
      Both the admin POS (`OrderService`) and storefront checkout (`CheckoutService`) use it, so the
      two paths can't drift (covered by `tests/Unit/PricingServiceTest.php`).

### P1 — Operational admin features that are commonly expected
- [ ] **Order invoice PDF** — admin can only view an order in-browser. There is no PDF package in
      `composer.json` (no dompdf/snappy) and no invoice action or route. The invoice settings group
      already exists (prefix, starting number, footer note, tax-breakdown toggle) and
      `RefundService` already derives refund numbers from `invoice_prefix`, so a DomPDF export
      mirrors an established pattern.
- [ ] **2FA for admin accounts** — no TOTP implementation or package exists; the `/auth/two-factor`
      route is a static theme demo page. Admin login is protected by reCAPTCHA v3 + rate limiting
      only. This remains the biggest auth-surface gap.
- [ ] **Bulk product import/export (CSV)** — only report exports exist (sales-by-date, top products).
      No product or order import/export controller, route or view.
- [ ] **Admin activity log surfaced in UI** — `ActivityLog` model + migration + `Admin::loggable()`
      morph relation exist, but nothing writes entries and there is no admin view. Either add model
      observers (Product/Order/Category/Coupon changes) plus a log screen, or drop the unused table.
- [ ] **Low-stock / new-order notification delivery** — `notify_new_order`, `notify_low_stock` and
      `notify_new_review` settings are stored and validated, but nothing dispatches when those events
      occur. Wire `Illuminate\Notifications` or small mailables into `OrderService` /
      `ProductService` / `ReviewService`.

### P2 — Nice-to-have admin UX
- [ ] Global admin search (products/orders/customers) in the topbar — only scoped Select2 endpoints
      exist today (`admin.products.search`, `admin.products.tags.search`,
      `admin.products.options.search`, `admin.orders.customers.search`,
      `admin.orders.products.search`). The template's topbar search input is still non-functional.
- [ ] Saved/exportable filters on Order and Product index pages (CSV export of the current filter).
      Report exports are the only CSV generation in the app so far.
- [ ] Audit-friendly "who changed this coupon/price" trail (ties into the Activity Log item above).

---

## B. Missing Modules / Features for a Complete E-Commerce Application

Grouped by how blocking they are to calling this a real, launchable e-commerce app.

### Blocking for any real launch
1. **Payment gateway integration** — no Stripe/SSLCommerz/bKash/Nagad code exists; only a settings
   toggle. COD *is* fully wired end-to-end (`orders.payment_method` migration, validated `in:cod`,
   checkout radio, order confirmation), so a gateway follows an existing pattern. Single biggest gap.
2. **Transactional customer emails** — order placed, confirmed, shipped, delivered, cancelled and
   refund processed. Only `CustomerPasswordResetMail`, `CustomerSetPasswordMail`,
   `CartReminderMail` and the admin `WeeklySalesSummaryMail` exist. Trigger lifecycle mail from
   order status transitions (`OrderService::updateStatus()` / `cancel()`) via events/listeners so
   admin POS and customer checkout orders produce the same notifications.
3. **Queue discipline for mail** — `CartReminderMail` and the weekly/monthly summaries already use
   `Mail::queue()`, but password reset / set-password mail is still sent synchronously, no Mailable
   implements `ShouldQueue`, and there is no retry/failed-job monitoring story. Finish this before
   adding lifecycle emails.
4. **Remaining storefront wiring** — category mega-menu, live search and the wishlist count badge.
   These are the last template-static public surfaces; details live in
   `.github/storefront-todo.md` (Parts 1–2).
5. **Sitemap + social share tags** — no `sitemap.xml` route/controller, and `<head>` has no
   `og:`/`twitter:` tags (`og_image` setting exists but is unused). Title, description, keywords,
   author and favicon are already rendered from Settings via `StorefrontBranding`.
6. **Return/RMA workflow** distinct from Refunds — Refunds only handle the money side; there is no
   "customer requests a return → admin approves → refund" chain, and no return-request model/table.

### Important before considering it "feature complete"
7. **Search** — `Product::scopeSearch()` exists but is unused; `ProductCatalogService::getFilteredProducts()`
   accepts only `category` / `color` / `size` / `price_min` / `price_max` / `sort`, and both header
   search inputs submit nowhere. Minimum is wiring `search` into the catalog service; real
   relevance/full-text would suggest Laravel Scout + Meilisearch/Algolia.
8. **PDP gallery zoom / lightbox** — the PDP renders `zoom-image-hover` slides from real variant
   images; verify the theme's `magnificPopup`/zoom initializers still bind to dynamically rendered
   slides (template selectors were written for static markup).
9. **Rate limiting on remaining AJAX endpoints** — checkout and coupon validation are throttled
   (`throttle:20,1`); admin product/tag/customer search endpoints and the visitor wishlist toggle
   (`visitor.account.wishlist.toggle`) are not.
10. **Automated backups** for the production database once deployed.

### Polish / scale
11. **API layer** (Sanctum-based) if a mobile app or headless frontend is ever planned — Sanctum is
    not installed.
12. **Multi-currency display** — Settings stores one currency (default BDT) plus symbol position; no
    conversion or per-locale display logic.
13. **Dashboard charts** (revenue trend, top categories) — ApexCharts and chart.js are bundled with
    the theme, but the dashboard canvases are static (ties into the P0 Dashboard item).
14. **Performance pass** — `ProductCatalogService` and `HomeService` eager-load
    category/brand/primaryImage/defaultVariant/variants; verify PDP reviews/related products and
    admin index pages (orders/customers) for remaining N+1 queries.
15. **Credential hygiene before any shared deployment** — seeders create documented default accounts
    (`Admin#123`, `Manager#123`, `Customer#123`), and CSP still allows `unsafe-inline` scripts with
    no `report-uri`. Rotate credentials and set `SESSION_SECURE_COOKIE=true` in production.

---

## Suggested Build Order (if picking up from here)

1. **Storefront header nav** — the admin data already exists (categories, wishlist service); wiring
   these is high-visibility and low-risk.
2. **Dashboard KPIs + charts** — every stat service already exists; this is the biggest remaining
   "looks unfinished" surface in the admin panel.
3. **Currency formatting helper** — small, and it prevents rework across ~20 templates.
4. **Payment gateway** — mirror the COD flow (`payment_method`, checkout branching, order placement)
   with a sandbox gateway first.
5. **Transactional emails + queueing** — event/listener-driven, queued from day one.
6. **Invoice PDF, 2FA, CSV import/export** — operational completeness.
7. **Sitemap, og: tags, search, N+1 audit** — last before any real launch.
