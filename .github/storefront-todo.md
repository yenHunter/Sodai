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
  `user-reviews`, `header` (mini-cart via `CartComposer`).
- Views that are STILL raw UBold template markup with hardcoded/dummy content, not bound to
  controllers/services: `index` (homepage), `about`, `contact`, `checkout`, `order-confirm`,
  `offers`, `faq`, `privacy-policy`, `terms-conditions`, `shipping-policy`, `return-refund-policy`.
- No payment gateway, no transactional emails beyond password/reminder, no queueing.

---

## PART 1 — P0: Wire remaining core views to real data (highest priority, unlocks demoability)

- [ ] **Homepage (`visitor/pages/index.blade.php`)**: Currently 100% static dummy products/testimonials.
      Needs a `HomeController@index` (new) pulling: featured products (`Product::scopeFeatured`),
      newest arrivals (`Product::scopeNewest`), active categories, and active banners
      (`Banner::scopeCurrentlyValid()->scopeOfPosition('home_slider'|'home_promo')` — Banner model
      and admin CMS already fully support this, just needs storefront consumption).
- [ ] **Checkout (`visitor/pages/checkout.blade.php`)**: Currently a static form posting to `#`.
      This is the single biggest storefront gap. Needs:
      - `CheckoutController` (new) + `routes/visitor.php` entries
      - Guest vs. registered checkout branching (guest checkout toggle already exists in
        Settings → Order group: `allow_guest_checkout`)
      - Address selection (reuse `AddressService`) or inline new-address form
      - Live shipping charge + tax computation reusing `SettingService::resolveShippingCharge()`
        and the same tax logic already built for `OrderService` (consider extracting a shared
        `PriceCalculationService` so admin POS and storefront checkout don't duplicate logic)
      - Coupon apply/remove (reuse the same validation logic as `OrderController::applyCoupon`
        AJAX endpoint — currently admin-only, needs a customer-facing equivalent with customer's
        own usage-limit check)
      - On submit: create `Order` + `OrderItem`s via a new `Services/Visitor/CheckoutService`
        (mirroring `OrderService::store()` but scoped to the authenticated/guest customer,
        with the same stock-locking transaction pattern)
      - Clear the cart after successful order placement
- [ ] **Order confirmation (`visitor/pages/order-confirm.blade.php`)**: Currently static "Thank You"
      page with hardcoded products. Needs `$order` passed in from checkout, showing real order
      number/items/total. Route currently takes no order id — needs
      `visitor.order-confirm` → `visitor.orders.confirm/{order}` or session-flash the last order id.
- [ ] **About / Contact pages**: Low effort — About can stay mostly static (marketing copy is fine
      to hardcode) but Contact form should actually submit somewhere (mail to admin alert email
      from Settings → Notification group `admin_alert_email`, or store as a lead). Currently posts
      to `#` with no backend.
- [ ] **CMS-backed policy pages**: `privacy-policy.blade.php`, `terms-conditions.blade.php`,
      `shipping-policy.blade.php`, `return-refund-policy.blade.php` are static Lorem Ipsum.
      The `CmsPage` model + admin editor already exist and are fully functional
      (`.github/admin-todo.md` P0 item — CMS admin editor). Once that ships, these 4 storefront
      views just need to fetch `CmsPage::findOrCreateBySlug($slug)->content` and render it instead
      of the hardcoded blocks. This is a quick win once the admin-side editor exists.
- [ ] **FAQ / Offers pages**: Currently fully static. Lower priority — either leave as marketing
      content or (offers) wire to `Coupon::active()->currentlyValid()` for a real "current offers" list.

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
      `ProductCatalogService::getFilteredProducts()` (the service doesn't currently accept a
      `search` filter key at all — needs adding alongside `category`/`color`/`size`).
- [ ] **Category sidebar widget** (`visitor/include/category-sidebar.blade.php`, shown on homepage)
      is fully hardcoded with dummy categories/products/prices. Either wire to real
      `Category`/`Product` data or remove it from the homepage if not needed for MVP.

---

## PART 3 — P1: Checkout-adjacent / order lifecycle features

- [ ] **Guest checkout persistence**: `Cart` already supports `session_id` for guests
      (`CartService::getOrCreateCart`), but there's no guest → registered account merge flow if a
      guest logs in mid-session. Decide: merge guest cart into user cart on login (recommended,
      small addition to `AuthController@login`).
- [ ] **Stock re-validation at checkout time**: `CartService::addItem`/`updateQuantity` check stock
      when adding, but by the time checkout is submitted stock may have changed. `CheckoutService`
      must re-lock and re-validate stock per item (same `lockForUpdate()` pattern already used in
      `OrderService::buildItemsWithStockLock()` — can likely reuse that method directly if it's
      moved to a shared trait/service, since Admin POS order creation and Customer checkout have
      near-identical stock-locking needs).
- [ ] **Order cancellation by customer**: `visitor.account.orders.show` currently only displays order
      status/timeline — no "Cancel Order" action for customers on cancellable (pending/confirmed)
      orders. Needs a new route + `CustomerOrderService::cancel()` (should reuse
      `OrderService::cancel()` logic but restrict who can call it and what reasons are allowed).
- [ ] **Customer-facing coupon validation endpoint** (AJAX) — needed for checkout to show live
      discount preview, mirroring `Admin\OrderController::applyCoupon` but scoped to
      `Auth::guard('customer')` and without admin-only fields.

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
- [ ] **Meta tag rendering from Settings → Marketing (SEO group)** — `Setting::get('seo', 'meta_title')`
      etc. exist but nothing injects them into `<head>` on storefront pages
      (`visitor/include/head.blade.php` has hardcoded `<title>`/`<meta>` tags).
- [ ] **og:image / social share tags** — same gap, `og_image` setting exists but unused on storefront.
- [ ] **N+1 query audit** on `products.blade.php` / `products-by-category.blade.php` — verify eager
      loading (`with([...])`) in `ProductCatalogService::getFilteredProducts()` covers everything
      the Blade loop touches (`$product->category`, `$product->brand`, `$product->primaryImage`,
      `$product->defaultVariant` — check these are all in the `with()` call, some already are).
- [ ] **Rate limiting on public AJAX endpoints** — product search, wishlist toggle
      (`/account/wishlist/{product}/toggle`) have no throttle middleware.

---

## Suggested Build Order (mirrors admin-todo.md style prioritization)

1. **Part 1 items** (homepage, checkout, order-confirm) — biggest unlock, makes the app demoable
   end-to-end as a real storefront.
2. **Part 2 items** (header search/category/wishlist wiring) — small, high-visibility fixes.
3. **Part 3 items** (checkout stock-locking, cancellation, coupon AJAX) — needed for checkout to be
   production-safe, not just "technically working."
4. **Part 4 items** — nice-to-have UX, do after core flow is solid.
5. **Part 5 items** — payment gateway + transactional emails, do once checkout flow is stable.
6. **Part 6 items** — polish, do last before any real launch.

---

*Last generated from source analysis: current repo snapshot, cross-referenced against
`.github/project-instructions.md` and `.github/admin-todo.md`.*