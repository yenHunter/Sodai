# Sodai — Storefront To-Do List & Missing Modules Audit

_Companion to `.github/admin-todo.md` and `.github/project-instructions.md`. Update as items are completed._

---

## Current State Summary (verified against source)

**Backend is largely ready, frontend is not wired:**
- `ProductCatalogService`, `CartService`, `WishlistService`, `AddressService`, `AccountService`,
  `CustomerOrderService`, `CustomerReviewService` — all exist and are functional.
- Visitor controllers (`ProductController`, `CartController`, `AccountController`, `AddressController`,
  `OrderController`, `ReviewController`, `WishlistController`, `AuthController`) are wired to routes and services.
- Views that ARE already wired to real data: `login`, `register`, `forgot-password`, `reset-password`,
  `set-password`, `verify-email`, `products` (index + by-category), `product-details`, `cart`,
  `user-profile`, `user-address`, `user-order-history`, `user-order-details`, `user-wishlist`,
  `user-reviews`, `header` (mini-cart via `CartComposer`) — plus, since the storefront wiring
  pass: `index` (homepage w/ banners, featured/new/limited-time products, categories),
  `checkout` (COD, guest or registered, stock re-locking), `order-confirm`, `offers`,
  `about`, and the CMS policy pages (`privacy-policy`, `terms-conditions`, `shipping-policy`,
  `return-refund-policy` via `CmsPageService`). Header/footer branding (logos, favicon,
  SEO meta, contact block, copyright) resolves from admin Settings via
  `App\Support\StorefrontBranding`.
- Views that are STILL raw UBold template markup with hardcoded/dummy content, not bound to
  controllers/services: `contact` (form posts nowhere), `faq`, and the header's category
  mega-menu / wishlist badge / search bar.
- No payment gateway beyond COD, no transactional lifecycle emails beyond password/reminder,
  no queueing.

---

## PART 1 — P0: Wire remaining core views to real data (highest priority, unlocks demoability)

- [x] **Homepage (`visitor/pages/index.blade.php`)**: ✅ Done — `HomeController@index` pulls featured
      products, new arrivals, top-rated, limited-time offers, top categories, feature items,
      and active banners (`home_slider` / `home_promo`) via `HomeService`.
- [x] **Checkout (`visitor/pages/checkout.blade.php`)**: ✅ Done — `CheckoutController` +
      `Services/Visitor/CheckoutService` + throttled routes: guest vs. registered branching
      (`allow_guest_checkout`), address selection/new form, live shipping + tax computation
      from Settings, customer-scoped coupon AJAX (`visitor.checkout.apply-coupon`, throttle:20,1),
      per-item stock re-locking inside the order transaction, cart cleared after placement.
- [x] **Order confirmation (`visitor/pages/order-confirm.blade.php`)**: ✅ Done — real order data
      from session `last_order_id`.
- [ ] **About / Contact pages**: About is served from the CMS (`HomeController::about`).
      Contact form still posts nowhere — needs a mail-to-admin (`admin_alert_email`) or lead
      storage backend (reCAPTCHA `data-recaptcha` attr is already in the markup).
- [x] **CMS-backed policy pages**: ✅ Done — cached `CmsPageService::getPage()` renders
      admin-edited content (HTML-sanitized on save); per-page meta overrides `<head>`.
- [ ] **Offers page**: ✅ Wired to real offers (`HomeService::getOfferList`).
      **FAQ page**: still static marketing content.

---

## PART 2 — P0: Header/Nav data wiring gaps

- [ ] **Category mega-menu** in `header.blade.php` — hardcoded "Category 1/2/3/4" placeholder links.
      Should pull from `Category::active()->parentOnly()->with('activeChildren')` (same data
      `ProductCatalogService::getFilterableCategories()` already partially provides for the shop
      sidebar — reuse or extend it for the header).
- [ ] **Wishlist count badge** in header (`ec-header-wishlist` shows hardcoded "4") — needs a
      `WishlistComposer` (mirror the existing `CartComposer` pattern exactly) so the count is
      accurate for logged-in customers, and hidden/zero for guests (wishlist requires auth currently).
- [ ] **Search bar** — `<input class="ec-search-bar">` in header submits nowhere. Needs a
      `visitor.products.index` GET with a `search` query param wired into
      `ProductCatalogService::getFilteredProducts()` (the service currently accepts only
      `category`/`color`/`size`/`price`/`sort` — still no `search` filter key).
- [ ] **Category sidebar widget** (`visitor/include/category-sidebar.blade.php`, shown on homepage)
      is fully hardcoded with dummy categories/products/prices. Either wire to real
      `Category`/`Product` data or remove it from the homepage if not needed for MVP.

---

## PART 3 — P1: Checkout-adjacent / order lifecycle features

- [ ] **Guest checkout persistence**: `Cart` already supports `session_id` for guests
      (`CartService::getOrCreateCart`), but there's no guest → registered account merge flow if a
      guest logs in mid-session. Decide: merge guest cart into user cart on login (recommended,
      small addition to `AuthController@login`).
- [x] **Stock re-validation at checkout time**: ✅ Done — `CheckoutService` re-locks and
      re-validates stock per item inside the order transaction (same `lockForUpdate()`
      pattern as admin order creation).
- [x] **Order cancellation by customer**: ✅ Done — `visitor.account.orders.cancel` exists,
      ownership-guarded, restricted to `CustomerOrderService::CUSTOMER_CANCELLABLE_STATUSES`,
      with a cancel-reason and the same stock-restore behavior as the admin flow.
- [x] **Customer-facing coupon validation endpoint** (AJAX) — ✅ Done, `visitor.checkout.apply-coupon`,
      customer-scoped usage-limit checks, `throttle:20,1` for brute-force protection
      (covered in `SecurityHardeningTest`).

---

## PART 4 — P1: Storefront UX completeness

- [ ] **Product quick-view modal** — template markup exists (`#ec_quickview_modal` referenced
      throughout) but no controller/AJAX endpoint feeds it. Needs a lightweight
      `visitor.products.quickview/{product}` JSON endpoint.
- [ ] **"Recently viewed" products** — no tracking exists. Would need a session-based or
      DB-based (if logged in) recently-viewed list, surfaced on PDP/homepage.
- [ ] **Compare products** — template has compare buttons/icons everywhere (`ec-btn-group compare`)
      but no backend at all. Decide: build it (session-based comparison table) or strip the UI
      elements if out of scope for MVP.
- [ ] **Product image zoom / gallery lightbox on PDP** — `product-details.blade.php` has basic
      slick-slider gallery wired to real `$product->images`, but the "zoom on hover" and lightbox
      popup (`magnificPopup`) init in `main.js` still targets template selectors — verify it still
      fires correctly against dynamically rendered images.
- [ ] **Breadcrumbs** — currently static ("Home > Shop") on most pages; category/product pages
      should build a real breadcrumb trail using `$category->parent`/`$product->category`.

---

## PART 5 — P2: Storefront-blocking for real launch (cross-referenced with admin-todo.md)

These overlap with `.github/admin-todo.md` Section B but are called out specifically from the
storefront angle:

- [ ] **Payment gateway integration** — checkout currently only has a "Cash On Delivery" radio in
      static markup. At minimum, wire COD as a real functioning option end-to-end (order created
      with a `payment_method` field — doesn't exist on `orders` table yet, needs a migration) before
      adding any real gateway (Stripe/SSLCommerz/bKash/Nagad).
- [ ] **Transactional customer emails**: order placed, order shipped, order delivered, order
      cancelled, refund processed. None of these exist yet — only `CustomerPasswordResetMail`,
      `CustomerSetPasswordMail`, `CartReminderMail`. Should hook into
      `OrderService::updateStatus()` / `cancel()` transitions (admin-side triggers, customer-side
      recipient) via Laravel events/listeners rather than inline mail calls, so both admin POS
      orders and future customer self-checkout orders trigger the same notifications.
- [ ] **Queued email dispatch** — `QUEUE_CONNECTION=database` is configured but nothing implements
      `ShouldQueue`. Once transactional emails exist, they should be queued from day one.
- [ ] **Abandoned cart auto-recovery** — admin can manually send a reminder
      (`CartService::sendReminderEmail`), but there's no scheduled job auto-triggering it after
      N hours of cart inactivity. Needs a `Schedule::command()` entry + Artisan command.

---

## PART 6 — P2: Polish / SEO / performance

- [ ] **Sitemap.xml** — no route/controller generates one. Needed for real SEO once launched.
- [x] **Meta tag rendering from Settings** — ✅ Done via `App\Support\StorefrontBranding`:
      `<head>` resolves title prefix, description, keywords and favicon from admin Settings
      (`seo`, `design` groups) with template defaults when unset; CMS pages override with
      their own meta. **Still open:** og:/twitter: card tags (`og_image` setting exists but
      unused on storefront).
- [ ] **og:image / social share tags** — same gap, `og_image` setting exists but unused on storefront.
- [ ] **N+1 query audit** on `products.blade.php` / `products-by-category.blade.php` — verify eager
      loading (`with([...])`) in `ProductCatalogService::getFilteredProducts()` covers everything
      the Blade loop touches (`$product->category`, `$product->brand`, `$product->primaryImage`,
      `$product->defaultVariant` — check these are all in the `with()` call, some already are).
- [ ] **Rate limiting on public AJAX endpoints** — product search, wishlist toggle
      (`/account/wishlist/{product}/toggle`) have no throttle middleware.

---

## Suggested Build Order (mirrors admin-todo.md style prioritization)

1. **Part 1/Part 3 items** are done — homepage, checkout (with stock-locking), order-confirm,
   CMS policy pages, offers, and customer order cancellation are all shipped and exercised by
   the Feature test suites.
2. **Part 2 → Part 4 items** (header search/category menu/wishlist badge, contact backend) —
   small, high-visibility fixes next.
3. **Part 4 UX polish** (quick-view, breadcrumbs, recently viewed) — after core flow polish.
4. **Part 5 items** — payment gateway beyond COD + transactional emails + queueing.
5. **Part 6 items** — sitemap, og: tags, N+1 audit — last before any real launch.

---

*Last updated: October 2026 storefront-wiring + security-hardening pass, verified against
source and cross-referenced with `.github/project-instructions.md` and `.github/admin-todo.md`.*