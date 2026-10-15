# Sodai — Admin Section To-Do List & Missing Modules Audit

_Companion to `.github/project-instructions.md`. Update as items are completed._

---

## A. Admin Section To-Do (prioritized)

### P0 — Fill functional gaps in existing modules
- [ ] **Dashboard**: replace placeholder view with real KPIs — today's orders, revenue (7/30 day),
      low-stock variant count, pending reviews, pending refunds. Data already exists via
      `OrderService::getOrderStats()`, `ReviewService::getReviewStats()`, `RefundService::getRefundStats()`
      — just needs a controller/view wiring these together.
- [x] **Reports module**: ✅ Shipped — `ReportService` with sales-by-date, top products, and
      low-stock reports, plus weekly/monthly summary emails delivered to the admin inbox via
      scheduled `reports:send-weekly-summary` / `reports:send-monthly-summary` commands
      (toggled in Settings → Notification).
- [ ] **Currency formatting helper**: add `format_price($amount)` (respecting
      `Setting::get('company','currency')` + symbol position) and replace hardcoded `$` across
      ~20 Blade files (product index/details, order index/details, refund, cart, coupon views).
- [x] **CMS static pages**: ✅ Shipped — `CmsPage` model/table, admin Quill editor
      (`admin.cms.pages.*`), and storefront rendering from `CmsPageService::getPage()`.
      Content is stored HTML-sanitized (see `App\Support\HtmlSanitizer`).

### P1 — Operational admin features that are commonly expected
- [ ] **Order invoice PDF** — admin can currently only view an order in-browser; add a
      "Download Invoice" action (a `pdf` skill/DomPDF-based export), using the invoice settings
      group that already exists (prefix, starting number, footer note, tax breakdown toggle).
- [ ] **2FA for admin accounts** — TOTP-based 2FA remains the biggest auth-surface gap;
      admin login is currently protected by reCAPTCHA v3 + rate limiting only.
- [ ] **Bulk product import/export (CSV)** — very common e-commerce admin need, none exists yet.
- [ ] **Admin activity log surfaced in UI** — `ActivityLog` model + migration already exist but
      nothing writes to it and there's no admin view. Either wire it up (model observers on
      Product/Order/Category etc.) or remove the unused table.
- [ ] **Low-stock / new-order notification delivery** — Settings has toggles
      (`notify_new_order`, `notify_low_stock`, `notify_new_review`) but nothing actually
      dispatches a notification when these events occur. Wire up `Illuminate\Notifications` or
      simple mailables triggered from `OrderService`/`ProductService`.

### P2 — Nice-to-have admin UX
- [ ] Global admin search (products/orders/customers) in the topbar — currently just a
      non-functional search input from the template.
- [ ] Saved/exportable filters on Order and Product index pages (CSV export of the current filter).
- [ ] Audit-friendly "who changed this coupon/price" trail (ties into the Activity Log item above).

---

## B. Missing Modules / Features for a Complete E-Commerce Application

Grouped by how blocking they are to calling this a real, launchable e-commerce app.

### Blocking for any real launch
1. **Payment gateway integration** — no Stripe/SSLCommerz/bKash/Nagad code exists; only a
   settings toggle. COD is fully wired end-to-end (`payment_method` column on `orders`,
   validated `in:cod`), so a gateway plugin mirrors an existing pattern. This is the
   single biggest gap.
2. **Storefront views wired to real data** — visitor controllers/services exist
   (`ProductCatalogService`, `CartService`, `WishlistService`, etc.) but the Blade views are
   still the raw UBold template markup, not bound to them.
3. **Transactional customer emails** — order confirmation, shipped, delivered, cancelled,
   refund processed. Only `CustomerSetPasswordMail` and `CartReminderMail` exist today.
4. **Queue-based email dispatch** — everything currently sends synchronously
   (`QUEUE_CONNECTION=database` is configured but nothing implements `ShouldQueue`).

### Important before considering it "feature complete"
5. **Search** — no full-text/relevance search on the storefront; only `LIKE` queries in
   `Product::scopeSearch()`. Consider Laravel Scout + a driver (Meilisearch/Algolia) for real use.
6. **Sitemap + SEO meta rendering** — SEO settings exist (`meta_title`, `og_image`, etc.) but
   nothing renders them into `<head>` on storefront pages, and there's no `sitemap.xml` route.
7. **Multi-image zoom / product gallery UX** on the storefront PDP.
8. **Order tracking page for customers** — `visitor.orders.show` exists but check it actually
   surfaces `OrderStatusHistory` in a customer-friendly timeline (admin side already has this).9. **Abandoned cart automated recovery**: ✅ Shipped — `carts:send-reminders` runs every
    30 minutes via the scheduler (`routes/console.php`), gated by the
    `abandoned_cart_enabled` setting; admins can still trigger manually.
10. **Return/RMA workflow** distinct from Refunds — currently Refunds only handle the money
    side; there's no "customer requests a return, admin approves the return, then refund" chain.

### Polish / scale
11. **API layer** (Sanctum-based) if a mobile app or headless frontend is ever planned.
12. **Multi-currency display** (if selling beyond one region) — Settings only stores one
    currency; no conversion/display-per-locale logic.
13. **Admin dashboard charts** (revenue trend, top categories) — ApexCharts is already bundled
    via the theme, just needs real data wired in (ties into P0 Dashboard item above).
14. **Rate limiting on public AJAX endpoints** (product/tag search) to prevent abuse.
15. **Automated backups** for the production database once deployed.

---

## Suggested Build Order (if picking up from here)

1. Dashboard KPIs (quick win, high visual payoff for a portfolio demo)
2. Currency formatting helper (small, prevents future rework across all templates)
3. Storefront view wiring (biggest unlock — makes the app actually demoable end-to-end)
4. Payment gateway (even a "fake"/sandbox gateway is fine for a portfolio — shows the pattern)
5. Transactional emails + queueing
6. Reports module
7. Everything else, as time allows