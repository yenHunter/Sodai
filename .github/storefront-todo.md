# Sodai — Storefront To-Do List & Missing Modules Audit

_Companion to `.github/admin-todo.md` and `.github/project-instructions.md`. Update as items are completed._

_Last verified against source: 10 November 2026 (full re-audit of routes, services, views and tests)._

---

## Current State Summary (verified against source)

**The customer journey is wired end-to-end; the remaining gaps are mostly navigation, one static page and the post-purchase lifecycle.**

- Every storefront controller listed below is routed in `routes/visitor.php` (group prefix name
  `visitor.`) or `routes/web.php`: `HomeController`, `ProductController`, `CartController`,
  `CheckoutController`, `OrderConfirmController`, `CmsPageController`, `AuthController`,
  `AccountController`, `AddressController`, `OrderController`, `ReviewController`,
  `WishlistController`.
- Visitor services: `HomeService`, `ProductCatalogService`, `CartService`, `CheckoutService`,
  `CmsPageService`, `AccountService`, `AddressService`, `CustomerOrderService`,
  `CustomerReviewService`, `WishlistService`. Money math is shared with the admin POS through
  `App\Services\Shared\PricingService`.
- Views wired to real data: `index` (homepage), `products`, `products-by-category`,
  `product-details` (+ variant matrix), `cart`, `checkout`, `order-confirm`, `offers`,
  `about` (CMS), `cms-page` (policy pages), `login`, `register`, `forgot-password`,
  `reset-password`, `set-password`, `verify-email`, `user-profile`, `user-address`,
  `user-order-history`, `user-order-details` (tracking steps + cancel), `user-wishlist`,
  `user-reviews`, `header` (mini-cart via `CartComposer`), `footer`.
- Branding (company name, light/dark logos, favicon, SEO title/description/keywords/author,
  footer contact block) resolves from admin Settings via `App\Support\StorefrontBranding`.
- Still template-static / not wired: `contact` (form posts to `#`), header category
  mega-menu, both header search inputs, wishlist count badge, and the homepage category
  sidebar widget.
- Not implemented at all: payment beyond COD, order-lifecycle emails, sitemap, og:/twitter:
  tags, quick-view, recently viewed, compare, guest→account cart merge, storefront search.
- Coverage relevant to this surface: `VisitorCheckoutTest`, `VisitorOrderCancellationTest`,
  `VisitorFaqTest`, `StorefrontBrandingTest`, `CartModuleTest`, `CartReminderSchedulerTest`, `FaqModuleTest`,
  `OfferModuleTest`, `SecurityHardeningTest`.

---

## PART 1 — P0: Core page wiring (highest priority, unlocks demoability)

- [x] **Homepage** (`visitor/pages/index.blade.php`): ✅ Done — `HomeController@index` feeds
      banners (`home_slider` / `home_promo`), categories, and the product sections via
      `HomeService`. **Caveat:** the section *sources* are placeholders — see the section-semantics
      item below.
- [x] **Checkout** (`visitor/pages/checkout.blade.php`): ✅ Done — `CheckoutController` +
      `CheckoutService`: guest vs. registered branching (`allow_guest_checkout`), address selection
      or inline new address, live shipping + tax from Settings, coupon AJAX
      (`visitor.checkout.apply-coupon`, `throttle:20,1`), per-item stock re-locking inside the order
      transaction, cart cleared after placement.
- [x] **Order confirmation** (`visitor/pages/order-confirm.blade.php`): ✅ Done — real order data from
      the session's `last_order_id`.
- [ ] **About / Contact pages**: About is CMS-backed (`HomeController@about`). The contact form
      still posts to `#` with no route/controller, and the right-hand column hardcodes a US address,
      `+44 0123 456 789` and `example@ec-email.com` instead of the Settings company/contact values
      (the map itself does use `setting('company', 'map_embed_url')`). Needs a POST route + validation
      + reCAPTCHA verification, delivering to `admin_alert_email` or storing a lead.
- [x] **CMS-backed policy pages**: ✅ Done — cached `CmsPageService::getPage()` renders admin-edited,
      HTML-sanitized content; per-page meta overrides `<head>`.
- [x] **Offers page**: ✅ Done — `HomeController@offer` renders `HomeService::getOfferList()`
      (`Offer::currentlyValid()->ordered()`), backed by the admin Offers module and `OfferModuleTest`.
- [x] **FAQ page**: ✅ Done — `visitor.faq` is served by
      `App\Http\Controllers\Visitor\FaqController` through
      `App\Services\Visitor\FaqService::getPublishedCategories()`, which returns only active
      categories that still have active FAQs (ordered, with only active FAQs eager-loaded). The view
      renders one `ec-faq-container` per category with the theme's existing accordion markup, escaped
      answers with `nl2br`, a real `route('visitor.index')` breadcrumb, and an empty state. Covered by
      `VisitorFaqTest` (published content, inactive filtering, ordering, escaping, empty state).
- [ ] **Homepage section semantics** — `HomeService` currently implements
      `getFeaturedProducts()`, `getTopRatedProducts()`, `getLimitedTimeOffers()` and
      `getFeatureItems()` all as `Product::active()->inStock()->newest()`. `Product::scopeFeatured()`
      (`is_featured`) and `Product::scopeTopRated()` exist but `HomeService` doesn't use them, and
      offers are not derived from the `offers`/coupon data at all. Decide per section: use the real scopes, or rename the
      sections so they don't promise filtering that isn't happening.
- [ ] **Route cleanup** — `/faq` now has a controller and its `routes/web.php` closure is gone. Still
      dead: the `/about` closure in `routes/web.php` is shadowed by the later-registered `visitor.about`
      route (`route:list` shows only `HomeController@about`), and `/contact` still bypasses controllers
      entirely. Remove the dead closure and move contact onto a controller.

---

## PART 2 — P0: Header/Nav data wiring gaps

- [ ] **Category mega-menu** in `include/header.blade.php` — still hardcoded "Category 1/2/3/4"
      placeholder columns with template menu banners. `ProductCatalogService::getFilterableCategories()`
      returns only leaf categories, so the header needs its own
      `Category::active()->parentOnly()->with('activeChildren')` query (or an extended service method).
- [ ] **Wishlist count badge** — `ec-header-wishlist` links to `wishlist.html` and shows a hardcoded
      count. `WishlistService` and the auth-guarded `visitor.account.wishlist.*` routes exist; add a
      `WishlistComposer` (mirroring `CartComposer`) that exposes the count for signed-in customers and
      zero/hidden for guests.
- [ ] **Search bar** — there are two dead inputs (desktop line ~25 and mobile line ~100) with no form
      action. `Product::scopeSearch()` already exists but nothing calls it; `ProductController` only
      extracts category/color/size/price/sort filters and `ProductCatalogService::getFilteredProducts()`
      has no `search` key. Wire the query param through the controller + service, then point both
      inputs at `visitor.products.index`.
- [ ] **Category sidebar widget** — `visitor/include/category-sidebar.blade.php` is still included by
      `index.blade.php` and is fully hardcoded with dummy categories/products/prices. Either wire it to
      `Category`/`Product` data or drop it from the homepage.

---

## PART 3 — P1: Checkout-adjacent / order lifecycle features

- [ ] **Guest checkout persistence (cart merge)**: guest carts are keyed by `session_id`
      (`CartService::getOrCreateCart` / `getExistingCart`) and there is no merge on login — a guest who
      signs in mid-session keeps a separate authenticated cart. Add a merge step to
      `AuthController@login` (sum quantities, respect stock, delete the guest cart).
- [x] **Stock re-validation at checkout time**: ✅ Done — `CheckoutService` re-locks and re-validates
      each item inside the order transaction (`lockForUpdate()`), matching admin POS behavior.
- [x] **Order cancellation by customer**: ✅ Done — `visitor.account.orders.cancel`, ownership-guarded,
      limited to `CustomerOrderService::CUSTOMER_CANCELLABLE_STATUSES`, with a cancel reason and the
      same stock-restore behavior as the admin flow (`VisitorOrderCancellationTest`).
- [x] **Customer-facing coupon validation endpoint** — ✅ Done, `visitor.checkout.apply-coupon`,
      customer-scoped usage-limit checks through `PricingService`, `throttle:20,1`
      (`SecurityHardeningTest`).

---

## PART 4 — P1: Storefront UX completeness

- [ ] **Product quick-view modal** — no `#ec_quickview_modal` usage and no JSON endpoint; the
      `a.quickview` buttons in `partials/product-card.blade.php` and the homepage are plain links to
      the PDP. Needs `visitor.products.quickview/{product}` returning the variant/gallery payload
      (`ProductCatalogService::buildVariantMatrix()` already produces most of it).
- [ ] **"Recently viewed" products** — no tracking exists. Session-based for guests, DB-based for
      signed-in customers; surface on the PDP sidebar and/or homepage.
- [ ] **Compare products** — the template's compare buttons are plain product links and a header
      dropdown menu points at a non-existent `compare.html`; there is no backend. Build a session-based
      comparison table or strip the UI affordances.
- [ ] **Product image zoom / gallery lightbox** — the PDP gallery renders real variant images with
      `zoom-image-hover` slides. Verify the theme's zoom/`magnificPopup` initializers still bind to
      dynamically rendered slides (they were written for the static template).
- [ ] **Breadcrumbs** — PDP and category pages now render real names (`Home > Shop > {name}` with
      route links), but nested parent-category levels are not included and static pages still link to
      `index.html`. Extend the trail using `$category->parent` / `$product->category` and route helpers.

---

## PART 5 — P2: Storefront-blocking for real launch (cross-referenced with admin-todo.md)

- [ ] **Payment gateway integration** — COD is fully wired (`orders.payment_method` migration exists,
      validated `in:cod`, checkout radio, order confirmation), but there is no gateway. Add
      Stripe/SSLCommerz/bKash/Nagad behind the existing settings toggle, branching checkout by the
      selected method.
- [ ] **Transactional customer emails** — none of order placed/shipped/delivered/cancelled/refund
      processed exist. Current mailables are `CustomerPasswordResetMail`, `CustomerSetPasswordMail`,
      `CartReminderMail` (customer) and `WeeklySalesSummaryMail` (admin). Hook into
      `OrderService::updateStatus()` / `cancel()` via events/listeners so admin POS and customer
      orders both notify the customer.
- [ ] **Queued email dispatch** — partially in place: `CartReminderMail` and the weekly/monthly
      summaries use `Mail::queue()`, and `composer dev` runs `queue:listen`. But no Mailable implements
      `ShouldQueue`, and reset/set-password mail is still sent synchronously. Move lifecycle emails to
      queued mailables with retry/failed-job monitoring.
- [x] **Abandoned cart auto-recovery** — ✅ Done. `carts:send-reminders` runs
      `everyThirtyMinutes()->withoutOverlapping()` in `routes/console.php`, gated by the
      `abandoned_cart_enabled` setting; admins can still trigger manually. Covered by
      `CartReminderSchedulerTest`.

---

## PART 6 — P2: Polish / SEO / performance

- [ ] **Sitemap.xml** — no route/controller generates one. Needed for real SEO once launched.
- [x] **Meta tag rendering from Settings** — ✅ Done via `App\Support\StorefrontBranding`:
      `<head>` resolves title, description, keywords, author and favicon from admin Settings
      (`seo` / `design` groups) with template defaults; CMS pages override description with their own
      meta. Covered by `StorefrontBrandingTest`.
- [ ] **og: / twitter: social tags** — `og_image` exists in Settings but nothing renders OpenGraph or
      Twitter card tags on the storefront. Add a shared `head` partial block driven by
      `StorefrontBranding` + the current product/page.
- [ ] **N+1 query audit** — `ProductCatalogService::getFilteredProducts()` and `HomeService` already
      eager-load `category`, `brand`, `primaryImage`, `defaultVariant` and `variants` (with option
      values). Still verify the PDP (`reviews.user`, `relatedProducts`, variant images) and the
      homepage category sidebar/product cards for lazy loads.
- [ ] **Rate limiting on public AJAX endpoints** — checkout and coupon-apply are throttled;
      `visitor.account.wishlist.toggle` (and any future storefront search/quick-view endpoint) has no
      throttle middleware.

---

## Suggested Build Order (mirrors admin-todo.md style prioritization)

1. **Header nav** (mega-menu, search, wishlist badge) — data/services already exist, all
   high-visibility, no schema changes.
2. **Contact form backend + branding values** — small, removes the last fully static form.
3. **Homepage section semantics** — use `scopeFeatured` / `scopeTopRated` / real offers instead of
   `newest()` everywhere.
4. **Guest cart merge** — closes a real data-loss path for signing-in guests.
5. **Payment gateway beyond COD + transactional emails (queued)** — the launch-blocking pair.
6. **Sitemap, og: tags, search, N+1 audit** — last before any real launch.

---

*Last updated: 10 November 2026 full source re-audit, cross-referenced with
`.github/project-instructions.md` and `.github/admin-todo.md`.*
