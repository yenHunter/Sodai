# Sodai — Security Posture Audit

_Last verified against source: 11 November 2026. Companion to `.github/admin-todo.md`, `.github/storefront-todo.md` and `.github/project-instructions.md`._

This file records the **current state** of the controls requested in the security checklist, the evidence behind each verdict, and whether the control is actually needed for this application. It is a point-in-time audit, not a guarantee: re-run the checks in [How this was verified](#how-this-was-verified) after dependency updates or new features.

**Remediation status (11 Nov 2026):** every P0 and P1 finding in this report was fixed in the working tree and verified with the focused test suite listed under [How this was verified](#how-this-was-verified). Remaining items are P2 or operational and are tracked in [Summary of concrete gaps](#summary-of-concrete-gaps).

**Stack context that drives many "not applicable" verdicts:** Laravel 13 single-vendor storefront + admin panel, session-cookie authentication (no JWT, no API), one tenant per deployment, SQLite by default locally and in tests (any Laravel-supported relational database in production), no payment gateway yet, no webhook consumers yet.

---

## At a glance

| # | Control | Status | Priority |
|---|---------|--------|----------|
| 1 | SQL injection | ✅ Mitigated | — |
| 2 | Cross-site scripting | ✅ Mitigated | — |
| 3 | CSRF protection | ✅ Mitigated | — |
| 4 | File upload validation | ✅ Mitigated | — |
| 5 | SSRF | ✅ Not applicable / mitigated | — |
| 6 | Broken object-level authorization | ✅ Mitigated | — |
| 7 | Rate limiting | ✅ Mitigated | — |
| 8 | Password hashing | ✅ Mitigated | — |
| 9 | Multi-factor authentication | ✅ Implemented (admin TOTP) | — |
| 10 | Server-side permission enforcement | ✅ Mitigated | — |
| 11 | Row-level security (RLS) | ➖ Not applicable | — |
| 12 | JWT secret handling | ➖ Not applicable | — |
| 13 | API secrets kept server-side | ✅ Mitigated | — |
| 14 | Tokens in local storage | ✅ Mitigated / not applicable | — |
| 15 | Default credentials | ✅ Mitigated | — |
| 16 | CORS configuration | ➖ Not applicable today | — |
| 17 | Webhook signatures | ➖ Not applicable today | — |
| 18 | Exposed source maps | ✅ Mitigated | — |
| 19 | Sensitive data in logs | ✅ Mostly mitigated | P2 |
| 20 | Vulnerable dependencies | ⚠️ Composer clean; npm 5 (2 moderate, 3 high) | P2 |

**Fixed on 11 Nov 2026:** demo-credential seeding (#15), Composer advisories (#20), the hardcoded Google Maps key (#13), all three XSS gaps (#2), route-level rate limits (#7) and admin TOTP MFA (#9). Remaining work is P2/operational: CSP nonces, trusted hosts/proxies, the PDF.js major upgrade and production environment defaults.

---

## Injection and input

### 1. SQL injection — ✅ Mitigated

**Evidence**
- All data access goes through Eloquent or the query builder (`where`, `whereIn`, `whereHas`, `orderBy`). No `DB::unprepared`, no `DB::select` with interpolated input.
- Raw SQL exists only as **static** fragments in aggregate queries: `selectRaw('status, count(*) as count')`, `selectRaw('DATE(created_at) as date')`, `selectRaw('COALESCE(SUM(total_amount), 0) as revenue')` (`app/Services/Admin/OrderService.php`, `RefundService.php`, `ReviewService.php`, `ReportService.php`) and `DB::raw('carts.updated_at')` in `app/Services/Admin/CartService.php`. No request value is concatenated into SQL.
- Sorting is whitelisted, not passed through: `ProductCatalogService::applySort()` uses `match ($sort)` with fixed columns and `latest()` as default.
- Search term filters use bound parameters (`where('question','like',"%{$search}%")` binds the value; `%` only acts as a wildcard inside the pattern, not as SQL).

**Why it matters here:** the storefront accepts many user-controlled filters (category, colour, size, price, sort) and a contact/FAQ/search surface. One concatenated query would be enough to read `users` or `admins` tables.

**Residual risk:** none found. Keep the whitelist pattern for any future sortable columns or dynamic `select()` lists.

---

### 2. Cross-site scripting — ✅ Mitigated

**Evidence (what is right)**
- `App\Support\HtmlSanitizer` is a whitelist sanitizer: allows a fixed tag/attribute set, strips `on*` handlers, `javascript:`/non-image `data:` URIs, and unknown schemes. It is applied **on write** to all CMS content (`app/Services/Admin/CmsPageService.php:33`) and product rich text (`app/Services/Admin/ProductService.php:70-71,146-147,727-733`), which is why `{!! $page->content !!}` / `{!! $product->description !!}` are safe to render unescaped.
- Uploaded SVGs are sanitized (`HtmlSanitizer::cleanSvg()` — removes `script`, `foreignObject`, `on*`, `javascript:`) before storage in `SettingService`.
- FAQ answers and the contact-email body are escaped and only newlines are converted (`nl2br(e(...))`).
- Everything else uses Blade `{{ }}` escaping; `tests/Feature/SecurityHardeningTest.php` asserts the sanitizer behavior and stored-product sanitization.

**Gaps found and closed (11 Nov 2026)**

| Gap | Where | Fix now in place |
|-----|-------|------------------|
| JSON embedded in a `<script>` block without hex flags | `resources/views/visitor/pages/product-details.blade.php` — `{!! json_encode($variantMatrix['combinations']) !!}` inside `<script type="application/json">` | Encoded with `JSON_HEX_TAG\|JSON_HEX_AMP\|JSON_HEX_APOS\|JSON_HEX_QUOT`; a variant SKU containing `</script>` renders as `\u003C\/script\u003E`. Regression test: `SecurityHardeningTest::test_variant_matrix_json_cannot_break_out_of_its_script_tag`. |
| CTA URLs were unvalidated strings | Banner/Offer store + update requests accepted `button_url` as `string\|max:255`; rendered as `href` on the storefront | New `App\Rules\SafeUrl` allows only `http(s)://` absolute URLs or app-relative `/path` values; rejects `javascript:`, `data:`, `vbscript:`, protocol-relative `//host` and control-character smuggling. Applied to all four banner/offer requests; covered by `tests/Unit/SafeUrlTest.php` and a `javascript:` rejection test in `SecurityHardeningTest`. |
| Raw `old()` re-render | `resources/views/admin/ecommerce/product/form.blade.php` — `{!! old('description', ...) !!}` | Reflected value now passes through `HtmlSanitizer::clean()` before rendering, preserving editor formatting while stripping scripts and event handlers. |

- **CSP is still not a safety net:** `app/Http/Middleware/SecurityHeaders.php` sets a CSP, but `script-src`/`style-src` include `'unsafe-inline'` (the theme uses inline scripts), so a future injection would still execute. Kept as P2 below.

**Why it matters here:** two roles write HTML (admins) and everyone else reads it. Stored XSS in a product option or CMS page would execute in every shopper's session, including admins previewing the storefront.

**Residual (P2):** replace `'unsafe-inline'` with nonces/hashes once inline theme scripts are consolidated. Until then, escaping and sanitizing at each sink is the actual control.

---

### 3. CSRF protection — ✅ Mitigated

**Evidence**
- All application routes are registered on the `web` middleware group (`bootstrap/app.php`), which includes Laravel's `ValidateCsrfToken`. `bootstrap/app.php` contains **no** `validateCsrfTokens(except: [...])` exemption.
- Every state-changing visitor form includes `@csrf` (login, register, password reset, checkout, cart, address, review, wishlist, contact). Admin forms carry `_token`.
- AJAX requests send the token explicitly: `X-CSRF-TOKEN` from the `csrf-token` meta tag in `resources/views/visitor/include/head.blade.php:3` (checkout, product add-to-cart) and from the form's `_token` input in admin scripts (product create/edit, order POS).

**Not an issue:** the newsletter forms in `visitor/include/footer.blade.php` and `layout/app.blade.php` have `method="post" action="#"` and no `@csrf`, but they are **not wired to any route** — they post back to the current page and would simply 419. They create no state change. When newsletter signup is implemented, add `@csrf` with the route.

**Why it matters here:** authenticated customers and admins have cookie sessions; without CSRF an attacker page could cancel orders, change addresses, or perform admin actions on behalf of a logged-in user.

---

### 4. File upload validation — ✅ Mitigated

**Evidence**
- Every upload field is validated with `image` + an explicit MIME whitelist + a per-field size cap (512 KB – 4 MB): product thumbnails/gallery, banners/offers, brand/category/CMS images, avatars, favicons and design logos.
- **Product** images are re-encoded server-side to WebP through Intervention Image (`ProductService::uploadImage()`), which also neutralizes polyglot/EXIF payloads. Brand, category, banner, offer, CMS and settings images are validated and stored with UUID filenames but keep their original bytes; for those, `image` + MIME validation is the only content barrier (SVG is additionally sanitized).
- Filenames are server-generated UUIDs (`Str::uuid()`), never the client filename; uploads go to the `public` disk under fixed directories.
- SVG is the one format that survives as markup, and it is passed through `HtmlSanitizer::cleanSvg()` before being written (`SettingService::uploadFile()`), with a failure aborting the upload. `SecurityHardeningTest` includes an SVG-with-script upload test.
- `.gitignore` excludes `/storage/*.key`, `/public/storage` and `/public/build`; uploaded files are not tracked.

**Why it matters here:** admins upload marketing assets and product photos; a PHP/HTML/SVG polyglot in a web-served directory is a direct code-execution or XSS path.

**Residual notes:** CMS page images and non-SVG settings files stored under `settings/` are validated but not re-encoded (they keep the original extension), so `image` validation remains the only barrier for them. That is acceptable; converting them to WebP as well would make the guarantee uniform.

---

### 5. SSRF — ✅ Not applicable / mitigated

**Evidence**
- The only outbound server-side HTTP call in `app/` is reCAPTCHA verification: `Http::timeout(10)->post('https://www.google.com/recaptcha/api/siteverify', ...)` in `app/Rules/ReCaptcha.php:44`. Host and path are hardcoded; only the token and the caller IP are sent.
- No import-from-URL, image-from-URL, feed reader, PDF-from-URL, or webhook fetcher exists. `fopen('php://output')` in `ReportController` is a CSV stream, not a network call.
- `company.map_embed_url` is an admin setting rendered **client-side** in an iframe; the CSP `frame-src https://www.google.com` limits what can be framed, and the server never fetches it.

**Why it matters here:** this class of app often adds "fetch image from URL" or payment/webhook callbacks, which is where SSRF appears. Today there is no feature that lets a user choose a URL the server will request.

**When adding features:** validate URL schemes/hosts and block private ranges (`127.0.0.0/8`, `10/8`, `172.16/12`, `192.168/16`, `169.254/16`, `::1`) for any server-side fetch.

---

## Authentication and access

### 6. Broken object-level authorization (BOLA/IDOR) — ✅ Mitigated

**Evidence**
- `App\Traits\Visitor\EnsuresCustomerOwnership::ensureOwnedByCustomer()` compares the model's `user_id` (or a given column) to `auth('customer')->id()` and aborts with 403. It is used by:
  - `AddressController` — `update`, `destroy`, `setDefault`
  - `OrderController` — `show`, `cancel`
  - `ReviewController` — `update`, `destroy`
- Order lists and details are additionally scoped in `CustomerOrderService` (`where('user_id', ...)` and an explicit ID comparison), and reviews/wishlists are scoped in their services.
- Cart items have no direct owner column, so `CartController::authorizeCartItem()` checks ownership through the parent cart (`user_id` for customers, `session_id` for guests) before update/delete.
- Guest order confirmation reads only the order id stored in the visitor's own session (`last_order_id`).
- Admin routes are intentionally cross-tenant by role: every admin with the relevant permission can see all orders/customers, which is the design of a single-store admin panel.

**Why it matters here:** sequential numeric IDs (`/account/orders/12`, `/account/addresses/3`) are trivially enumerable; without the ownership checks one customer could read or cancel another's order.

**Residual risk:** none found in the audited controllers. Add explicit cross-customer feature tests (403 on another customer's address/review/cart item) to prevent regressions when new resources are added.

---

### 7. Rate limiting — ⚠️ Partial

**Implemented**

| Surface | Limit | Where |
|---|---|---|
| Customer login | 5 attempts / 300 s, keyed by email + IP, plus account lockout after 5 failed attempts for 30 min | `Visitor/Auth/LoginRequest`, `User::incrementFailedAttempts()` |
| Admin login | 5 attempts / 300 s, keyed by email + IP | `Admin/Auth/LoginRequest` |
| Admin MFA challenge | 5 attempts / 5 min per IP | `admin.two-factor.verify` route |
| Forgot password (both guards) | same limiter as login | `ForgotPasswordRequest` + `ensureIsNotRateLimited()` |
| Resend verification email | 3 / 600 s | `Visitor\AuthController::resendVerificationEmail()` |
| Registration (`POST /register`) | 10 / minute | `routes/visitor.php` |
| Password reset/set attempt | 10 / minute | `routes/visitor.php` |
| Checkout + coupon validation | 20 / minute | `routes/visitor.php` |
| Contact form | 5 / minute | `routes/visitor.php` |
| Cart add/update/remove | 60 / minute | `routes/visitor.php` |
| Wishlist toggle | 30 / minute | `routes/visitor.php` |
| Address create/update/delete/default | 30 / minute | `routes/visitor.php` |
| Review create/update/delete | 10 / minute | `routes/visitor.php` |

**Not throttled (deliberate):** admin AJAX search endpoints (`/products/search`, `/tags/search`, `/options/search`, `/orders/customers/search`, `/orders/products/search`) — authenticated admins only, and throttling them would degrade normal typeahead use. There is no global `RateLimiter::for('api')` because there is no API.

**Why it matters here:** login/OTP-less password reset and mail-sending endpoints are the classic abuse targets (credential stuffing, reset-mail flooding, contact-spam). Most critical surfaces are covered; the remainder is defense-in-depth.

**Note on reCAPTCHA fail-open:** `ReCaptcha::validate()` returns success when Google's API is unreachable (availability bias). Combined with contact-form rate limiting this is acceptable, but do not rely on reCAPTCHA alone for abuse prevention.

---

### 8. Password hashing — ✅ Mitigated

**Evidence**
- `User` and `Admin` both declare `'password' => 'hashed'` casts, so assignments hash automatically; registration and reset flows also call `Hash::make()` explicitly.
- Hashing driver is Laravel's default bcrypt (no `config/hashing.php`, framework default), with `BCRYPT_ROUNDS=12` in `.env.example`.
- `password` and `remember_token` are in `$hidden` on both models.
- Password reset tokens are random 64-char strings stored as bcrypt hashes and compared with `Hash::check()`; both guards use 30–60 minute expiry and delete tokens after use. Admin reset calls `logoutOtherDevices()`.
- Password policies: `Password::min(8)->mixedCase()->numbers()` for customer registration, admin creation and customer password change; admin self-service change additionally requires symbols and verifies the current password.

**Why it matters here:** the database contains customer PII and admin accounts that can edit prices/content.

**Residual:** admin login validation only enforces `min:6` on the submitted password (it is a login, not a policy check) — harmless, but confusing next to the creation policy. Consider aligning to `min:8` for consistency.

---

### 9. Multi-factor authentication — ✅ Implemented for admins (TOTP)

**Evidence**
- Dependency-free RFC 6238 TOTP (SHA-1, 6 digits, 30 s period, ±1 period drift) lives in `app/Services/Admin/TwoFactorService.php`; secrets are base32 and stored through the `Admin` model's `encrypted` cast.
- `admins` gained `two_factor_secret`, `two_factor_recovery_codes` (`encrypted:array`) and `two_factor_confirmed_at` (migration `2026_11_11_000001_add_two_factor_columns_to_admins_table`). `hasTwoFactorEnabled()` is the single gate used by login and the controller.
- `Admin\AuthController::loginAttempt()` verifies the password first, then **logs the session out** and stores only a pending admin id (`login.2fa.admin_id`) before redirecting to `admin.two-factor.challenge`. `TwoFactorController::verifyChallenge()` accepts a current TOTP code **or** a single-use recovery code, consumes it, regenerates the session, and only then authenticates the guard.
- Enrolment is at `GET admin/two-factor` (secret + `otpauth://` URL, confirmation code, eight recovery codes shown once). Disabling requires the current password. Management routes sit behind `auth.admin`; challenge routes sit behind `guest:admin` and are throttled `5,5`. The profile page links to the MFA screen.
- Covered by `tests/Feature/AdminTwoFactorTest.php`: setup, enable, invalid code, login challenge, wrong code, single-use recovery code, pending-session requirement and password-confirmed disable.

**Not implemented:** customer MFA. Customers have no privileged actions beyond their own orders/addresses, so TOTP stays optional for them; add it before saved payment methods or account credit.

**Why it matters here:** an admin account can change prices, refund orders, edit content and export customer data. Password + reCAPTCHA v3 + rate limiting still lets a targeted attacker with a valid or reused password in; TOTP closes that path.

**Operational notes:** recovery codes are shown once and stored encrypted; there is no "regenerate codes" screen yet (disable and re-enable to rotate them). MFA is available but not forced — enforce enrolment for `super-admin` and roles with `setting.*`, `order.*` or `refund.*` through onboarding policy until a middleware gate is added.

---

### 10. Server-side permission enforcement — ✅ Mitigated

**Evidence**
- Admin routes require `auth.admin` and then per-module `permission:` middleware; mutating actions have their own `permission:*.create/edit/delete` middleware in addition to the module's `.view` gate (`routes/admin.php`).
- `App\Http\Middleware\CheckAdminPermission` checks `hasPermissionTo($permission, 'admin')` on the server and returns 403 for both web and JSON requests; `super-admin` bypasses intentionally.
- Blade's `@admincan` / `@admincanany` directives (`AppServiceProvider`) only hide UI; the same permissions are enforced by route middleware, so hiding a button is never the only control.
- `AdminAuthenticated` also rejects deactivated admins and invalidates their session, and `prevent.back.history` is applied to authenticated admin routes.

**Why it matters here:** multiple staff roles exist (manager, editor, etc.). Client-side hiding of menu items would let any authenticated admin call the underlying URL directly.

**Residual:** the dashboard route requires authentication but no specific permission — acceptable, since it will show aggregate data once wired. Review any new admin route to ensure it carries a `permission:` middleware, not only Blade directives.

---

### 11. Row-level security (RLS) — ➖ Not applicable

**Why not needed here:** RLS is a PostgreSQL feature that pushes tenant/owner filtering into the database. Sodai is a **single-tenant** store on MySQL (SQLite locally), and the database user is the application itself; there is no direct client access to the database that RLS would protect.

The equivalent guarantee is enforced at the application layer:
- customer-owned rows are filtered by `user_id` in services and guarded by `EnsuresCustomerOwnership` (#6);
- guest carts are scoped by `session_id`;
- admin access is gated by roles/permissions (#10).

**When it would become necessary:** if the product becomes multi-tenant (several stores sharing one database) or if you add a Postgres data API (Supabase/PostgREST) that clients query directly. At that point add `tenant_id` columns plus Postgres RLS policies, and keep the existing scoped queries as defense-in-depth.

---

## Secrets and tokens

### 12. JWT secrets — ➖ Not applicable

**Evidence**
- No JWT or token-auth package is installed (no `tymon/jwt-auth`, no Sanctum), and there are no API routes. Authentication uses Laravel's session guard (`web` for customers, `admin` guard for staff), so the signing material is `APP_KEY`, not a JWT secret.
- `.env` is gitignored and no `.env*` file other than `.env.example` is tracked. `APP_KEY` is generated per environment (`php artisan key:generate`).
- Sessions are encrypted (`SESSION_ENCRYPT=true` in `.env.example`) and cookies are `HttpOnly` with `SameSite=Lax`.

**What to do instead:** treat `APP_KEY` as the crown jewel — unique per environment, never reused between local/staging/production, stored only in the server environment or secret manager, and rotated with the understanding that it invalidates sessions/encrypted data. If a JWT/API layer is added later, keep the signing secret server-side only, use asymmetric keys or a long random secret, and never ship it to the browser.

---

### 13. API secrets kept server-side — ✅ Mitigated

**Good**
- The reCAPTCHA **secret** is only read server-side via `config('services.recaptcha.secret_key')` (from `RECAPTCHA_SECRET_KEY`) inside `App\Rules\ReCaptcha`. The browser only receives the public **site key** (`config('services.recaptcha.site_key')`), which is expected.
- No `VITE_*` variable carries a secret; Vite only gets `VITE_APP_NAME`. No API keys were found in `resources/js`.

**Issue found and fixed (11 Nov 2026)**
- `resources/views/admin/ecommerce/order/order-details.blade.php` embedded a theme-leftover Google Maps place with the template author's client key. The block was removed from the page; `rg "AIza" resources/` now returns nothing. The key remains in git history, so treat it as public and rotate/restrict it in the template author's Google project — it was never a Sodai key.
- The store's own map is the contact-page embed, configured through the admin `company.map_embed_url` setting (validated `url` + `starts_with:https://www.google.com/maps/embed`) and containing no API key.

**Why it matters here:** secrets in Blade/JS are shipped to every visitor of that page and remain in git history even after removal. A leaked server-side key (payment, mail, SMS) would be far worse, so the rule must be enforced before adding those integrations.

**Rule going forward:** any key read in Blade/JS is public by definition. Keep server-side secrets in `.env`/the secret manager, call the provider from the server, and restrict any deliberately public browser key by HTTP referrer and API.

---

### 14. Tokens in local storage — ✅ Mitigated / not applicable

**Evidence**
- Session cookies are `HttpOnly` (`config/session.php`, `SESSION_HTTP_ONLY=true`), so JavaScript cannot read the session identifier.
- Grep of `resources/` found only theme preferences in web storage: `__THEME_CONFIG__`, `__THEME_LANG__` (`resources/js/app.js`), `theme` (`resources/js/pages/landing.js`) and a saved config in the admin head partial. No auth/session/API tokens.
- There is no JWT or bearer token in this app, so there is nothing token-like to put in `localStorage`.

**Why it matters here:** `localStorage` is readable by any script on the origin, so an XSS bug plus a stored token equals full account takeover. Keeping auth in `HttpOnly` cookies (plus the XSS fixes in #2) avoids that chain.

**Rule for the future:** never store access/refresh tokens, API keys or password-reset tokens in `localStorage`/`sessionStorage`; keep them in `HttpOnly` cookies or in-memory with short lifetimes.

---

### 15. Default credentials — ✅ Mitigated

**Evidence (fixed 11 Nov 2026)**
- `AdminSeeder` and `CustomerSeeder` now return immediately in `production` (with a console warning) and take passwords from `SEED_ADMIN_PASSWORD`, `SEED_MANAGER_PASSWORD` and `SEED_CUSTOMER_PASSWORD`; the demo defaults only apply locally/testing. `tests/Feature/SeederSafetyTest.php` runs both seeders in a production environment and asserts zero rows.
- `DatabaseSeeder` still calls them for local convenience; production deploys must not run seeders at all.
- Demo credentials remain documented for local onboarding, which is acceptable now that the application cannot create them in production.

**Why it matters here:** if `php artisan migrate --seed` (or `db:seed`) is ever run on a shared/staging/production database, anyone who reads the repository can log into the admin panel.

**Still required on existing deployments**
1. If an earlier deploy ran `db:seed`, delete or rotate `admin@cyberjatra.com`, `manager@cyberjatra.com` and the demo customer now — the gate prevents *new* creation, not existing rows.
2. Keep seeders out of production deploy scripts (`composer setup` runs `migrate --force` but not seeding); the environment gate is a second line of defence.
3. Set `SEED_*_PASSWORD` values in any shared non-production environment that does seed.

---

## Configuration and hygiene

### 16. CORS configuration — ➖ Not applicable today

**Evidence**
- `config/cors.php` does not exist and there is no `routes/api.php`. All routes are same-origin `web` routes; browsers do not need CORS for them.
- Laravel's default CORS handling only applies to the paths in its config (`api/*`, `sanctum/csrf-cookie`); with no API routes there is no cross-origin API surface to misconfigure.
- No `Access-Control-Allow-Origin: *` was found in the codebase.

**Why not needed here:** the storefront and admin are server-rendered Blade pages on the same origin; there is no separate SPA domain calling an API.

**When adding an API/mobile client:** publish `config/cors.php`, list explicit origins (no `*` when credentials/cookies are used), restrict methods/headers, and keep `supports_credentials` false unless cookies are genuinely required cross-site.

---

### 17. Webhook signatures — ➖ Not applicable today

**Evidence**
- No webhook routes or handlers exist (`grep webhook` in `routes/` and `app/` returns nothing).
- Payment is COD-only; there is no gateway callback to authenticate.

**Why not needed here:** without inbound provider callbacks there is nothing to verify.

**When adding a gateway (Stripe/bKash/Nagad/SSLCommerz):** verify the signature on the **raw** request body with a timing-safe comparison before parsing or trusting any field; reject unsigned/invalid requests with 400; use the provider's event id for idempotency; never trust amount/status from the client redirect.

---

### 18. Exposed source maps — ✅ Mitigated

**Evidence**
- `vite.config.js` does not enable `build.sourcemap`, so Vite's default (`false`) applies.
- `git ls-files "*.map"` returns nothing; `find public -name "*.map"` returns nothing after a production build.
- `public/build` is gitignored, so build artifacts (including any future maps) cannot be committed accidentally.

**Why it matters here:** source maps reconstruct readable source and internal paths, which helps an attacker find endpoints and logic flaws. There is no debugging benefit to shipping them publicly.

**Keep:** `build.sourcemap` disabled for production; if you ever need private maps for error tracking, upload them to the monitoring service instead of serving them from `public/`.

---

### 19. Sensitive data in logs — ✅ Mostly mitigated (P2)

**Evidence**
- No log statement writes passwords, reset tokens, session ids or API secrets. `ReCaptcha` logs success/score/action/error-codes and IP — **not** the token; the Google HTTP call is not logged with the secret.
- Auth flows log only outcomes; `updateLastLogin()` stores IP in the database, not in logs.
- Exceptions are logged with context in services (e.g. `ProductService` logs `collect($data)->except(['variants'])`), which can include product names/descriptions but no credentials. `OrderService` logs exception objects, which may include SQL and customer PII in edge cases.
- `.env.example` ships `LOG_LEVEL=debug` and `APP_DEBUG=true`; both are local defaults but dangerous if copied to production.

**Why it matters here:** logs are often shipped to third-party aggregators and retained longer than the database; a reset token or password in a log line defeats the hashing work in #8.

**Actions**
- Set `APP_DEBUG=false` and `LOG_LEVEL=warning` (or `error`) in production; never expose stack traces to visitors.
- Keep the existing rule: log identifiers and outcomes, never credentials/tokens/full request bodies.
- If you add request logging or an error tracker, add a scrub list for `password`, `password_confirmation`, `token`, `g-recaptcha-response`, `authorization`, `cookie`.

---

### 20. Vulnerable dependencies — ⚠️ Composer clean, npm residual (P2)

**Composer (`composer audit`): 0 advisories (11 Nov 2026)**

The five flagged Symfony packages were updated on the 7.4 patch line: `symfony/http-foundation` 7.4.21 → 7.4.23, `http-kernel` 7.4.21 → 7.4.23, `mime` 7.4.19 → 7.4.22, `routing` 7.4.21 → 7.4.23, `yaml` 7.4.21 → 7.4.23 (plus `error-handler`, `var-dumper`, `deprecation-contracts`, `event-dispatcher-contracts`, `polyfill-mbstring`). `composer audit` exits 0 with "No security vulnerability advisories found."

```bash
composer update symfony/http-foundation symfony/http-kernel symfony/mime symfony/routing symfony/yaml --with-dependencies
composer audit
```

**npm (`npm audit`): 5 vulnerabilities (2 moderate, 3 high), each accepted with rationale**

Non-breaking `npm audit fix` plus pinning `quill` to `2.0.2` (the `2.0.3` release is the XSS-affected version) removed the Vite, follow-redirects, glob-patch and Quill advisories; the production build was re-run afterwards.

| Package | Severity | Why it remains | Exit plan |
|---|---|---|---|
| `pdfjs-dist` 2.16.105 | high | The fix is `pdfjs-dist@6.5.438`, a major viewer-API rewrite. It is used only by the theme's admin PDF-viewer demo page and never parses user-uploaded PDFs in the storefront flow. | Upgrade together with the PDF-viewer page and retest; do not `npm audit fix --force` blindly. |
| `sharp` via `@tabler/icons-webfont` 3.35.0 | high | Build-time only: `sharp`/libvips generate the icon webfont during packaging; it is not shipped to the browser, and the app's own image processing uses Intervention Image. The fix moves `@tabler/icons-webfont` to 3.51.0, outside the pinned range and with possible icon-set changes. | Upgrade the icon package and visually diff the admin icon set, or move to inline SVG icons. |
| `glob` / `minimatch` | moderate | Transitive build-tool dependency (Vite/plugins); the advisory requires a breaking `glob@14`. No runtime exposure. | Take the upgrade with the next Vite/tooling bump. |

Re-run both audits in CI on every dependency change. Do not run `npm audit fix --force` without reviewing each major-version jump.

---

## Production configuration checklist

These are not code vulnerabilities but determine whether the controls above hold in a real deployment:

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://your-domain`.
- [ ] Unique `APP_KEY` per environment; never reuse the local key.
- [ ] `SESSION_SECURE_COOKIE=true` (HTTPS only), `SESSION_ENCRYPT=true`, `SESSION_HTTP_ONLY=true`, `SESSION_SAME_SITE=lax`.
- [ ] HTTPS terminated correctly and `Strict-Transport-Security` active (the `SecurityHeaders` middleware adds HSTS only in production).
- [ ] Configure trusted hosts/proxies (`TrustHosts` / `trustProxies`) for the actual load balancer; do not trust arbitrary `X-Forwarded-*`.
- [ ] `LOG_LEVEL=warning` or `error`; log storage not publicly served.
- [ ] Mail credentials and any future gateway keys only in the environment/secret manager.
- [ ] Rotate/remove any demo accounts created before the seeder gate; never run `db:seed` in production.
- [ ] Run `composer audit` and `npm audit` in CI; fail the build on high/critical advisories.
- [ ] Storage symlink (`php artisan storage:link`) only if uploads must be public; ensure `storage/` is not web-accessible.

---

## How this was verified

Commands and checks used for this audit (re-run them to refresh the report):

```bash
# Raw SQL / injection surface
rg "whereRaw|selectRaw|orderByRaw|havingRaw|DB::raw|DB::statement|DB::select\(" app/

# Unescaped Blade output (each was traced to its write path)
rg "\{!!" resources/views

# CSRF coverage
rg "validateCsrfTokens|csrf" bootstrap/ app/Http/Middleware/
rg "@csrf" resources/views/visitor
rg "X-CSRF-TOKEN|csrf-token" resources/js resources/views/visitor/include/head.blade.php

# Ownership checks
rg "ensureOwnedByCustomer|abort_unless|where\('user_id'" app/

# Rate limiting
rg "throttle|RateLimiter|tooManyAttempts" routes/ app/

# Secrets and storage
git ls-files | rg -i "\.(env|pem|key)$|id_rsa"
rg "localStorage|sessionStorage" resources/
rg "AIza|sk_live|api[_-]?key" resources/ public/

# Outbound requests (SSRF)
rg "Http::|file_get_contents|curl_init|fopen\(" app/

# Webhooks / API / CORS
rg "webhook|signature" routes/ app/
ls routes/api.php config/cors.php

# Source maps
git ls-files "*.map"; find public -name "*.map"

# Dependencies
composer audit
npm audit
```

**Test coverage relevant to these controls:** `tests/Feature/SecurityHardeningTest.php` (HTML sanitizer, CMS/product sanitization, SVG upload stripping, coupon rate limiting, variant-matrix JSON escaping, CTA URL rejection), `tests/Unit/SafeUrlTest.php` (allowed/rejected link schemes), `tests/Feature/SeederSafetyTest.php` (production seeder gate), `tests/Feature/AdminTwoFactorTest.php` (TOTP setup/challenge/recovery/disable), `tests/Feature/VisitorContactTest.php` (reCAPTCHA score/action rejection, validation), `tests/Feature/VisitorOrderCancellationTest.php` and `VisitorCheckoutTest.php` (ownership/stock paths), `tests/Feature/StorefrontBrandingTest.php` (settings-driven output). No dedicated tests currently assert 403s for cross-customer address/review/cart access — add them when touching those controllers.

---

## Summary of concrete gaps

| Gap | Severity | Status | Next action |
|---|---|---|---|
| CSP allows `unsafe-inline` | Low | Open (P2) | Nonces/hashes after consolidating inline theme scripts |
| No trusted-host/proxy configuration | Low | Open (P2) | Configure `TrustHosts`/`trustProxies` for the production load balancer |
| `pdfjs-dist` 2.16.105 advisory | Low (admin demo only) | Accepted (P2) | Upgrade with the PDF-viewer page; never open untrusted PDFs with it |
| `sharp`/libvips via icon webfont | Low (build-time) | Accepted (P2) | Upgrade the icon package or move to inline SVG |
| reCAPTCHA fails open when Google is unreachable | Low | Accepted | Rate limits + admin MFA are the primary controls; revisit if captcha becomes load-bearing |
| Production env defaults (`APP_DEBUG=true`, `SESSION_SECURE_COOKIE=false`) | Medium | Operational | Set explicitly in the deployment environment; see checklist above |
| Demo accounts created by earlier deploys | High | Operational | Rotate/remove on any existing shared database |

_All code-level P0/P1 findings from the 11 November 2026 audit are fixed in this branch and covered by the listed tests; the rows above are the remaining backlog. Keep this file current: when a gap is fixed, move it to "Mitigated" with the commit/PR reference, and re-run the dependency audits._
