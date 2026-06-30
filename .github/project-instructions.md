# `project-instructions.md`

```markdown
# Project Instructions for LLM Models
# E-Commerce Application - Complete Development Guide

---

## IMPORTANT NOTE FOR LLM MODELS
This file contains every detail about this project's architecture,
decisions, conventions and development progress. Read this file
completely before making any suggestions or writing any code.
Never suggest anything that contradicts the decisions made here.

---

## 1. PROJECT OVERVIEW

### Basic Information
- **Project Name:** Sodai (Single Vendor E-Commerce)
- **Type:** Single Vendor E-Commerce Website
- **Purpose:** A complete e-commerce solution with separate
  admin panel and customer-facing storefront

### Technology Stack
- **Backend Framework:** Laravel 13.15.0
- **PHP Version:** PHP 8.3.24
- **Frontend:** Blade Template Engine
- **CSS Framework:** Bootstrap 5.3.3
- **Icons:** Bootstrap Icons 1.11.3
- **Build Tool:** Vite (with laravel-vite-plugin)
- **Database:** MySQL
- **ORM:** Eloquent
- **Authentication:** Laravel Auth with Multiple Guards
- **Authorization:** Spatie Laravel Permission Package
- **Mail:** Laravel Mail (SMTP via Mailtrap for development)
- **HTTP Client:** Laravel Http Facade (for reCAPTCHA)
- **Development Server:** Laragon (Windows)
- **Package Manager:** Composer (PHP), NPM (JS)

### Key Packages Installed
- spatie/laravel-permission → Role & Permission management
- laravel-vite-plugin → Asset bundling

---

## 2. ARCHITECTURE DECISIONS

### 2.1 Authentication Architecture
- **Two completely separate authentication systems**
- Admin and Customer use DIFFERENT guards, models, and tables
- This is intentional for maximum security isolation

```
Guard Name    Model          Table      Purpose
──────────    ─────          ─────      ───────
admin         Admin          admins     Admin panel access
customer      User           users      Customer storefront access
```

- Default guard is set to 'customer' in config/auth.php
- Admin has NO public registration — admins created by Super Admin only
- Customer has public registration

### 2.2 Route Architecture
- **TWO separate route files** (intentional design decision)
- `routes/web.php` → Visitor/Customer routes ONLY
- `routes/admin.php` → Admin routes ONLY
- `routes/admin.php` is registered in `bootstrap/app.php`
  with prefix 'admin' and name 'admin.' automatically applied

### 2.3 Controller Architecture
- Separated by user type in different namespaces
- `App\Http\Controllers\Admin\*` → All admin controllers
- `App\Http\Controllers\Visitor\*` → All customer controllers

### 2.4 Category Architecture
- **Single table, self-referencing** (parent_id approach)
- Maximum 2 levels enforced in VALIDATION LOGIC (not DB)
- Level 1: Categories (parent_id = NULL)
- Level 2: Sub-categories (parent_id = category_id)
- No deeper nesting allowed

### 2.5 Related Products Architecture
- **Hybrid approach** (manual + automatic)
- Admin can manually select related products
- System auto-fills remaining slots from:
  1. Same category products
  2. Products sharing same tags
- Uses `product_relations` pivot table for manual selection
- Uses `product_tag` pivot table for tag-based suggestions

### 2.6 Role-Permission Architecture
- Using **Spatie Laravel Permission** package
- Guard name for all roles/permissions: **'admin'**
- Roles are for ADMIN only (not customers)
- 5 roles defined (see Section 6)

### 2.7 Frontend Architecture
- Pre-built HTML templates integrated into Blade
- Developer has their own HTML templates for both admin and customer
- NO need to build views from scratch
- JavaScript is kept in SEPARATE files (not inline in blade)
- JS files located in: `resources/js/pages/`
- Loaded via: `@vite(['resources/js/pages/filename.js'])`
- Blade variables passed to JS via hidden input fields
  (NOT inline blade syntax inside .js files)

### 2.8 reCAPTCHA Architecture
- Using **Google reCAPTCHA v3** (NOT v2)
- v3 is invisible — no checkbox widget shown
- Works by executing JS and getting a score token
- Token injected into hidden field before form submit
- Server validates token with Google API
- Score threshold: 0.5 (configurable via RECAPTCHA_THRESHOLD)
- Applied on: Admin Login, Forgot Password, Reset Password
- Action names must match between JS and PHP exactly

---

## 3. FILE & DIRECTORY STRUCTURE

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── AuthController.php      ✅ Done
│   │   │   └── DashboardController.php ✅ Done (temporary)
│   │   └── Visitor/
│   │       └── AuthController.php      ✅ Created (empty)
│   ├── Middleware/
│   │   ├── AdminAuthenticated.php      ✅ Done
│   │   ├── CustomerAuthenticated.php   ⏳ Pending (customer auth)
│   │   ├── SecurityHeaders.php         ✅ Done
│   │   ├── PreventBackHistory.php      ✅ Done
│   │   ├── RedirectIfAuthenticated.php ✅ Done (custom)
│   │   └── CheckAdminPermission.php    ✅ Done
│   └── Requests/
│       ├── Admin/
│       │   ├── LoginRequest.php        ✅ Done
│       │   ├── ForgotPasswordRequest.php ✅ Done
│       │   └── ResetPasswordRequest.php  ✅ Done
│       └── Visitor/
│           ├── LoginRequest.php        ⏳ Pending
│           └── RegisterRequest.php     ⏳ Pending
├── Mail/
│   └── Admin/
│       └── AdminPasswordResetMail.php  ✅ Done
├── Models/
│   ├── Admin.php                       ✅ Done
│   ├── User.php                        ✅ Done
│   ├── Category.php                    ✅ Done
│   ├── Product.php                     ✅ Done
│   ├── Tag.php                         ✅ Done
│   ├── ProductImage.php                ⏳ Pending
│   ├── Order.php                       ⏳ Pending
│   ├── OrderItem.php                   ⏳ Pending
│   ├── Cart.php                        ⏳ Pending
│   ├── CartItem.php                    ⏳ Pending
│   ├── Wishlist.php                    ⏳ Pending
│   ├── Coupon.php                      ⏳ Pending
│   ├── Review.php                      ⏳ Pending
│   ├── Address.php                     ⏳ Pending
│   └── ActivityLog.php                 ⏳ Pending
└── Rules/
    └── ReCaptcha.php                   ✅ Done

routes/
├── web.php                             ✅ Done (visitor only)
├── admin.php                           ✅ Done (admin only)
└── console.php                         (default Laravel)

resources/
├── js/
│   ├── app.js
│   └── pages/
│       ├── admin-auth-login.js         ✅ Done
│       ├── admin-auth-forgot-password.js ✅ Done
│       └── admin-auth-reset-password.js  ✅ Done
└── views/
    ├── admin/
    │   ├── layouts/
    │   │   └── app.blade.php           ✅ Done (from template)
    │   ├── auth/
    │   │   ├── login.blade.php         ✅ Done
    │   │   ├── forgot-password.blade.php ✅ Done
    │   │   └── reset-password.blade.php  ✅ Done
    │   ├── emails/
    │   │   └── password-reset.blade.php  ✅ Done
    │   └── dashboard/
    │       └── index.blade.php         ✅ Done (temporary)
    └── visitor/
        └── (templates ready, routes pending)

database/
├── migrations/                         ✅ All Done
└── seeders/
    ├── DatabaseSeeder.php              ✅ Done
    ├── RolePermissionSeeder.php        ✅ Done
    └── AdminSeeder.php                 ✅ Done

bootstrap/
└── app.php                             ✅ Done (custom configured)

config/
├── auth.php                            ✅ Done (custom guards)
├── services.php                        ✅ Done (recaptcha keys added)
└── permission.php                      ✅ Done (spatie configured)
```

---

## 4. DATABASE SCHEMA

### Migration Order (Must follow for FK dependencies)
```
1.  admins
2.  users
3.  categories
4.  tags
5.  products
6.  product_images
7.  product_tag (pivot)
8.  product_relations (pivot)
9.  addresses
10. wishlists
11. carts
12. cart_items
13. coupons
14. orders
15. order_items
16. reviews
17. activity_logs
18. cache
19. jobs
20. admin_password_reset_tokens
```

### Tables Overview

#### admins
```
id, name, email (unique), password, phone (nullable),
avatar (nullable), is_active (bool, default:true),
last_login_at (nullable), last_login_ip (nullable),
remember_token, timestamps, softDeletes
```

#### users (customers)
```
id, name, email (unique), phone (nullable, unique),
email_verified_at (nullable), password,
avatar (nullable),
status (enum: active|inactive|banned, default:active),
ban_reason (nullable), last_login_at (nullable),
last_login_ip (nullable),
failed_login_attempts (tinyInt, default:0),
locked_until (nullable), remember_token,
timestamps, softDeletes
```

#### categories
```
id, name, slug (unique), description (nullable),
image (nullable), parent_id (nullable, FK→categories),
is_active (bool, default:true),
sort_order (int, default:0), timestamps, softDeletes
Indexes: parent_id, slug
```

#### tags
```
id, name, slug (unique), timestamps
```

#### products
```
id, name, slug (unique), sku (unique),
short_description (nullable), description (longText, nullable),
category_id (FK→categories, restrict on delete),
price (decimal 10,2),
sale_price (decimal 10,2, nullable),
stock_quantity (int, default:0),
low_stock_threshold (int, default:5),
thumbnail (nullable), weight (decimal 8,2, nullable),
is_active (bool, default:true),
is_featured (bool, default:false),
average_rating (decimal 3,2, default:0.00),
review_count (int, default:0),
total_sales (int, default:0),
meta (json, nullable), timestamps, softDeletes
Indexes: slug, sku, [category_id+is_active], [is_featured+is_active]
```

#### product_images
```
id, product_id (FK→products, cascade),
image_path, is_primary (bool, default:false),
sort_order (int, default:0), timestamps
Index: [product_id+is_primary]
```

#### product_tag (pivot)
```
id, product_id (FK→products, cascade),
tag_id (FK→tags, cascade), timestamps
Unique: [product_id+tag_id]
```

#### product_relations (pivot)
```
id, product_id (FK→products, cascade),
related_product_id (FK→products, cascade), timestamps
Unique: [product_id+related_product_id]
Index: product_id
```

#### addresses
```
id, user_id (FK→users, cascade),
label (default:'home'), recipient_name,
recipient_phone, address_line_1,
address_line_2 (nullable), city, state,
zip_code, country (default:'Bangladesh'),
is_default (bool, default:false), timestamps
Index: [user_id+is_default]
```

#### wishlists
```
id, user_id (FK→users, cascade),
product_id (FK→products, cascade), timestamps
Unique: [user_id+product_id]
Index: user_id
```

#### carts
```
id, user_id (FK→users, cascade, nullable),
session_id (nullable), timestamps
Indexes: user_id, session_id
Note: session_id used for guest carts
```

#### cart_items
```
id, cart_id (FK→carts, cascade),
product_id (FK→products, cascade),
quantity (int), timestamps
Unique: [cart_id+product_id]
```

#### coupons
```
id, code (unique), type (enum: percentage|fixed),
value (decimal 10,2),
minimum_order_amount (decimal 10,2, default:0),
maximum_discount (decimal 10,2, nullable),
usage_limit (nullable), usage_per_user (int, default:1),
used_count (int, default:0),
is_active (bool, default:true),
starts_at (nullable), expires_at (nullable),
timestamps, softDeletes
Index: code
```

#### orders
```
id, order_number (unique), user_id (FK→users, restrict),
status (enum: pending|confirmed|processing|
              shipped|delivered|cancelled|refunded),
subtotal, discount_amount (default:0),
shipping_charge (default:0), tax_amount (default:0),
total_amount, coupon_code (nullable),
coupon_id (FK→coupons, set null, nullable),
shipping_name, shipping_email, shipping_phone,
shipping_address_line_1, shipping_address_line_2 (nullable),
shipping_city, shipping_state, shipping_zip,
shipping_country,
notes (nullable), tracking_number (nullable),
shipped_at (nullable), delivered_at (nullable),
cancelled_at (nullable), cancel_reason (nullable),
timestamps, softDeletes
Indexes: order_number, [user_id+status]
Note: Shipping address is SNAPSHOT at order time
```

#### order_items
```
id, order_id (FK→orders, cascade),
product_id (FK→products, restrict),
product_name (snapshot), product_sku (snapshot),
product_image (snapshot, nullable),
unit_price, quantity, total_price, timestamps
Index: order_id
Note: Product details are snapshots to preserve
      order history even if product changes
```

#### reviews
```
id, product_id (FK→products, cascade),
user_id (FK→users, cascade),
order_id (FK→orders, cascade),
rating (tinyInt 1-5),
comment (text, nullable),
status (enum: pending|approved|rejected, default:pending),
timestamps
Unique: [product_id+user_id+order_id]
Index: [product_id+status]
Note: order_id ensures only buyers can review
```

#### activity_logs
```
id, loggable_type, loggable_id (morphs - auto indexed),
action, model_type (nullable), model_id (nullable),
old_values (json, nullable), new_values (json, nullable),
ip_address (nullable), user_agent (nullable),
url (nullable), method (nullable), timestamps
Index: action
Note: morphs() auto-creates index on loggable_type+loggable_id
      DO NOT manually add this index (causes duplicate key error)
```

#### admin_password_reset_tokens
```
email (primary), token (hashed), created_at
Note: Separate from customer password_reset_tokens
      Token expires in 30 minutes
      Token is hashed before storing
```

#### cache & cache_locks
```
Standard Laravel cache tables
```

#### jobs, job_batches, failed_jobs
```
Standard Laravel queue tables
```

---

## 5. AUTHENTICATION SYSTEM

### 5.1 Admin Authentication
**Status: ✅ Complete**

Features:
- Login with email + password + reCAPTCHA v3
- Remember Me (long-lived cookie)
- Forgot Password (email with reset link)
- Reset Password (token expires in 30 min)
- Rate limiting on login (5 attempts, 5 min lockout)
- Rate limiting on forgot password (3 attempts, 10 min)
- Session regeneration on login (prevents fixation)
- Session invalidation on logout
- Last login IP and timestamp tracking
- Account deactivation check on every request
- Prevent back button after logout
- No public registration (admin only created by super admin)

**Login Flow:**
```
1. Visit /admin/login
2. Enter email + password + reCAPTCHA
3. LoginRequest validates all fields + reCAPTCHA
4. Rate limiter checked
5. Auth::guard('admin')->attempt() called
6. If fails → increment rate limiter → show error
7. If success → clear rate limiter
8. Session regenerated (security)
9. Last login updated (IP + timestamp)
10. Redirect to /admin/dashboard
```

**Forgot Password Flow:**
```
1. Visit /admin/forgot-password
2. Enter email + reCAPTCHA
3. Rate limiter checked (3 attempts per IP)
4. If email exists → delete old token → create new token
5. Token is hashed before storing
6. Reset URL built with plain token
7. Email sent via AdminPasswordResetMail
8. Always show same success message
   (prevents email enumeration attack)
```

**Reset Password Flow:**
```
1. Click link in email → /admin/reset-password/{token}?email=...
2. Token and email passed to view as variables
3. Enter new password + confirm + reCAPTCHA
4. Validate token exists in DB
5. Check token not expired (30 minutes)
6. Verify token hash matches
7. Update password
8. Delete used token
9. Redirect to login with success message
```

### 5.2 Customer Authentication
**Status: ⏳ Pending**

Planned features:
- Register with email verification
- Login with email + password
- Remember Me
- Forgot/Reset Password
- Account locking after 5 failed attempts (30 min)
- Ban check on every request

---

## 6. ROLE & PERMISSION SYSTEM

### Roles (guard: 'admin')
```
Role             Description
────             ───────────
super-admin      Full access to everything
manager          Everything except admin management
order-manager    Orders and customers only
content-editor   Products, categories, banners (no delete)
support          View orders, handle reviews & customers
```

### All Permissions (guard: 'admin')
```
Category         Permissions
────────         ───────────
dashboard        dashboard.view
category         category.view, category.create,
                 category.edit, category.delete
product          product.view, product.create,
                 product.edit, product.delete
order            order.view, order.update-status,
                 order.cancel, order.delete
customer         customer.view, customer.ban,
                 customer.delete
coupon           coupon.view, coupon.create,
                 coupon.edit, coupon.delete
banner           banner.view, banner.create,
                 banner.edit, banner.delete
review           review.view, review.approve,
                 review.reject, review.delete
report           report.view
setting          setting.view, setting.edit
admin            admin.view, admin.create,
                 admin.edit, admin.delete
```

### Role-Permission Matrix
```
Permission          Super  Manager  Order  Content  Support
                    Admin           Mgr    Editor
──────────────────────────────────────────────────────────
dashboard.view        ✅     ✅      ✅      ✅       ✅
category.*            ✅     ✅      ❌    no del     ❌
product.*             ✅     ✅      ❌    no del     ❌
order.view            ✅     ✅      ✅      ❌       ✅
order.update-status   ✅     ✅      ✅      ❌       ❌
order.cancel          ✅     ✅      ✅      ❌       ❌
order.delete          ✅     ✅      ❌      ❌       ❌
customer.view         ✅     ✅      ✅      ❌       ✅
customer.ban          ✅     ✅      ❌      ❌       ❌
customer.delete       ✅     ❌      ❌      ❌       ❌
coupon.*              ✅     ✅      ❌      ❌       ❌
banner.*              ✅     ✅      ❌      ✅       ❌
review.view           ✅     ✅      ❌      ❌       ✅
review.approve        ✅     ✅      ❌      ❌       ✅
review.reject         ✅     ✅      ❌      ❌       ✅
review.delete         ✅     ✅      ❌      ❌       ❌
report.view           ✅     ✅      ❌      ❌       ❌
setting.view          ✅    view     ❌      ❌       ❌
setting.edit          ✅     ❌      ❌      ❌       ❌
admin.*               ✅     ❌      ❌      ❌       ❌
```

### How to Use in Routes
```php
// Single permission
Route::middleware('permission:product.view')->group(function () {});

// In controller constructor
$this->middleware('permission:product.create')->only(['create', 'store']);
```

### How to Use in Blade
```blade
@admincan('category.create')
    ...
@endadmincan

@admincanany(['category.edit', 'category.delete'])
    ...
@endadmincanany

@adminrole('super-admin')
    ...
@endadminrole
```

These are registered in AppServiceProvider::boot()
Always use @admincan instead of @can in admin views

---

## 7. MIDDLEWARE SYSTEM

### Registered Middleware Aliases
```
Alias                  Class                              Purpose
─────                  ─────                              ───────
guest                  RedirectIfAuthenticated            Custom redirect
auth.admin             AdminAuthenticated                 Admin auth check
auth.customer          CustomerAuthenticated              Customer auth check
prevent.back.history   PreventBackHistory                 No browser back
permission             CheckAdminPermission               Permission check
```

### Global Middleware
```
SecurityHeaders → Applied to ALL requests
Sets: X-Content-Type-Options, X-Frame-Options,
      X-XSS-Protection, Referrer-Policy,
      Permissions-Policy, HSTS (production only)
```

### Middleware Details

**AdminAuthenticated:**
- Checks Auth::guard('admin')->check()
- If not authenticated → redirect to admin.login
- If account deactivated → logout + redirect to admin.login

**RedirectIfAuthenticated (custom):**
- guard 'admin' → redirect to admin.dashboard
- guard 'customer' → redirect to visitor.home
- Overrides Laravel default which redirects to '/'

**PreventBackHistory:**
- Sets Cache-Control: no-store headers
- Applied to all authenticated admin routes

**CheckAdminPermission:**
- Super admin bypasses ALL permission checks
- Checks if admin has ANY of the given permissions
- Supports multiple permissions (OR logic)
- Returns 403 for unauthorized access

---

## 8. RECAPTCHA SYSTEM

### Version: Google reCAPTCHA v3

### Configuration
```
File: config/services.php
Keys: recaptcha.site_key, recaptcha.secret_key, recaptcha.threshold

File: .env
RECAPTCHA_SITE_KEY=your_v3_site_key
RECAPTCHA_SECRET_KEY=your_v3_secret_key
RECAPTCHA_THRESHOLD=0.5
```

### How It Works
```
1. Load reCAPTCHA script in <head>:
   <script src="https://www.google.com/recaptcha/api.js
               ?render=SITE_KEY"></script>

2. On form submit, JS intercepts:
   e.preventDefault()
   grecaptcha.ready(() => {
     grecaptcha.execute(SITE_KEY, {action: 'action_name'})
       .then(token => {
         document.getElementById('g-recaptcha-response').value = token
         form.submit()
       })
   })

3. Hidden field carries token:
   <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

4. Site key passed to JS via hidden input (NOT inline blade in JS):
   <input type="hidden" id="recaptchaSiteKey" value="{{ config(...) }}">
   const siteKey = document.getElementById('recaptchaSiteKey')?.value

5. Server validates via ReCaptcha rule:
   - POST to Google siteverify API
   - Check success = true
   - Check score >= threshold (0.5)
   - Check action matches
```

### Action Names (Must match JS and PHP exactly)
```
Page                   Action Name
────                   ───────────
Admin Login            admin_login
Admin Forgot Password  admin_forgot_password
Admin Reset Password   admin_reset_password
```

### ReCaptcha Rule Location
```
app/Rules/ReCaptcha.php
```

### Important Notes for reCAPTCHA
- v3 has NO visible widget (no checkbox)
- Only shows small badge in corner (required by Google ToS)
- Must show privacy/terms links near submit button
- Score 1.0 = definitely human, 0.0 = definitely bot
- Threshold 0.5 is Google's recommended minimum
- Action names: only alphanumeric and underscores (no hyphens)

---

## 9. JAVASCRIPT ARCHITECTURE

### Location
```
resources/js/pages/
├── admin-auth-login.js           ✅ Done
├── admin-auth-forgot-password.js ✅ Done
└── admin-auth-reset-password.js  ✅ Done
```

### Convention: Pass Blade Data to JS
```html
<!-- ✅ CORRECT: Use hidden input in blade -->
<input type="hidden" id="recaptchaSiteKey"
       value="{{ config('services.recaptcha.site_key') }}">

<!-- ✅ Then read in .js file -->
const siteKey = document.getElementById('recaptchaSiteKey')?.value;

<!-- ❌ WRONG: Never use blade syntax in .js files -->
<!-- grecaptcha.execute('{{ config(...) }}') → does NOT work -->
```

### Vite Configuration
```js
// vite.config.js
laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/pages/admin-auth-login.js',
        'resources/js/pages/admin-auth-forgot-password.js',
        'resources/js/pages/admin-auth-reset-password.js',
        // Add new page JS files here
    ],
})
```

### Layout Script Section Convention
```blade
{{-- In page blade view --}}
@push('head')
    {{-- Scripts that must load in <head> (e.g., reCAPTCHA) --}}
    <script src="..."></script>
@endpush

@section('scripts')
    {{-- Page-specific JS via Vite --}}
    @vite(['resources/js/pages/page-name.js'])
@endsection
```

### Layout Must Have
```html
<head>
    @stack('head')    <!-- For reCAPTCHA and other head scripts -->
</head>
<body>
    @yield('content')
    @vite(['resources/js/app.js'])
    @yield('scripts')  <!-- For page-specific JS -->
    @stack('scripts')  <!-- For additional scripts -->
</body>
```

---

## 10. ROUTES REFERENCE

### Admin Routes (`routes/admin.php`)
```
All routes auto-prefixed with: /admin
All route names auto-prefixed with: admin.

Method  URI                          Name                    Auth
──────  ───                          ────                    ────
GET     /admin/login                 admin.login             guest:admin
POST    /admin/login                 admin.login.post        guest:admin
GET     /admin/forgot-password       admin.password.request  guest:admin
POST    /admin/forgot-password       admin.password.email    guest:admin
GET     /admin/reset-password/{tok}  admin.password.reset.form guest:admin
POST    /admin/reset-password        admin.password.reset    guest:admin
POST    /admin/logout                admin.logout            auth.admin
GET     /admin/dashboard             admin.dashboard         auth.admin
```

### Visitor Routes (`routes/web.php`)
```
Status: Mostly pending, basic structure ready
```

---

## 11. EMAIL SYSTEM

### Mail Classes
```
App\Mail\Admin\AdminPasswordResetMail
- Constructor: resetUrl, adminName, expiresInMinutes
- View: resources/views/admin/emails/password-reset.blade.php
```

### Email Views
```
resources/views/admin/emails/
└── password-reset.blade.php  ✅ Done
    - HTML email template
    - Shows: Admin name, reset button, expiry notice
    - Fallback URL text (if button doesn't work)
```

### Development Mail Setup
```
Using Mailtrap for development (no real emails sent)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
```

---

## 12. SECURITY MEASURES IMPLEMENTED

```
Security Feature              Status    Location
────────────────              ──────    ────────
reCAPTCHA v3                  ✅        Admin login, forgot, reset
Rate Limiting (login)         ✅        LoginRequest (5 attempts/5min)
Rate Limiting (forgot pwd)    ✅        ForgotPasswordRequest (3/10min)
Session Regeneration          ✅        On every login
Session Invalidation          ✅        On logout
CSRF Protection               ✅        All forms (@csrf)
Password Hashing              ✅        bcrypt via 'hashed' cast
Security Headers              ✅        SecurityHeaders middleware
Prevent Back History          ✅        PreventBackHistory middleware
Email Enumeration Prevention  ✅        Same message for found/not found
Token Hashing                 ✅        Reset tokens hashed in DB
Token Expiry                  ✅        30 min for admin reset
Remember Me                   ✅        Long-lived cookie
Account Deactivation Check    ✅        AdminAuthenticated middleware
XSS Prevention                ✅        Blade {{ }} auto-escaping
SQL Injection Prevention      ✅        Eloquent ORM
Soft Deletes                  ✅        Admins, Users, Products, etc.
Activity Logging              ⏳        Table ready, implementation pending
Role-Based Access Control     ✅        Spatie Permission
Custom Redirect (guest)       ✅        RedirectIfAuthenticated
```

---

## 13. SEEDERS

### Run Order
```
1. RolePermissionSeeder → Creates all roles and permissions
2. AdminSeeder          → Creates admin accounts with roles
```

### Default Admin Accounts
```
Super Admin:
  Email: admin@admin.com
  Password: admin1234
  Role: super-admin

Manager (sample):
  Email: manager@admin.com
  Password: manager1234
  Role: manager
```

### Run Seeders
```bash
php artisan migrate:fresh --seed
# OR
php artisan db:seed
```

---

## 14. DEVELOPMENT PROGRESS

### ✅ Completed
1. Project setup and configuration
2. Database migrations (all 20 tables)
3. Auth configuration (guards, providers)
4. Admin model with HasRoles
5. Role-Permission system (Spatie)
6. All middleware (security, auth, redirect)
7. Admin authentication (login, forgot, reset)
8. reCAPTCHA v3 on all admin auth pages
9. Email system (password reset)
10. Seeders (roles, permissions, admins)
11. Route files (web.php + admin.php)
12. JS page files for admin auth

### ⏳ Pending (In Order)
1. Admin Dashboard (with sidebar layout)
2. Category Management CRUD
3. Product Management CRUD
4. Tag Management
5. Banner Management
6. Coupon Management
7. Order Management
8. Customer Management
9. Review Management
10. Admin Management (Super Admin only)
11. Settings
12. Reports
13. Customer Registration & Login
14. Customer Homepage
15. Product Listing & Detail
16. Cart System
17. Wishlist
18. Checkout Process
19. Payment Integration
20. Order Tracking
21. Review System (Customer side)
22. Customer Profile
23. Activity Logging Implementation
24. Final Security Audit

---

## 15. IMPORTANT CONVENTIONS TO FOLLOW

### Naming Conventions
```
Controllers:  PascalCase → ProductController.php
Models:       PascalCase singular → Product.php
Migrations:   snake_case → create_products_table
Routes:       kebab-case URI → /admin/product-categories
Route names:  dot notation → admin.categories.index
Blade views:  kebab-case → product-detail.blade.php
JS files:     kebab-case → admin-product-list.js
```

### Form Request Convention
```
Always use Form Requests for validation (never validate in controller)
Admin requests: app/Http/Requests/Admin/
Visitor requests: app/Http/Requests/Visitor/
```

### Response Convention
```
Success redirect: redirect()->route('...')->with('success', '...')
Error redirect:   redirect()->route('...')->with('error', '...')
Validation fail:  Automatic via Form Request
```

### Model Convention
```
Always use:
- $fillable (never $guarded)
- SoftDeletes where appropriate
- Proper casts for booleans, decimals, dates
- Scopes for common queries (scopeActive, scopeFeatured)
- Accessor methods for computed properties
```

### Security Convention
```
- Never use env() directly in code → use config()
- Never put sensitive data in JS files
- Always hash tokens before storing
- Always regenerate session on login
- Always invalidate session on logout
- Always use Form Requests (includes CSRF automatically)
```

### Blade Convention
```
- Error display: BOTH @if($errors->any()) summary AND @error('field')
- Session messages: Check session('success') and session('error')
- Always use @csrf in forms
- Never use inline JS in blade (use separate .js files)
- Pass PHP data to JS via hidden inputs
```

---

## 16. KNOWN ISSUES & SOLUTIONS

### Issue 1: Duplicate Key Error in activity_logs migration
```
Problem: morphs() auto-creates index, manual index causes duplicate
Solution: Remove manual $table->index(['loggable_type', 'loggable_id'])
          morphs() handles it automatically
```

### Issue 2: Spatie Permission TypeError (teamsKey null)
```
Problem: config/permission.php has team_foreign_key as null
Solution: Set team_foreign_key to 'team_id' and use_teams to false
```

### Issue 3: Spatie Permission wildcard not working
```
Problem: $role->givePermissionTo('category.*') throws exception
Solution: Must list each permission explicitly
          givePermissionTo(['category.view', 'category.create', ...])
```

### Issue 4: Admin logged in redirects to '/' instead of dashboard
```
Problem: Default Laravel RedirectIfAuthenticated redirects to '/'
Solution: Create custom RedirectIfAuthenticated middleware
          Register as 'guest' alias in bootstrap/app.php
          Handle each guard separately
```

### Issue 5: Composer update fails on Windows (file locked)
```
Problem: Windows Search Indexer or Antivirus locks vendor files
Solution: 
  1. Stop Windows Search service
  2. Run terminal as Administrator
  3. Exclude project folder from Antivirus
  4. Use specific package install instead of full update
```

### Issue 6: reCAPTCHA v2 vs v3 confusion
```
Problem: Using v2 widget code with v3 keys
Solution: v3 has NO visible widget
  - Load: api.js?render=SITE_KEY
  - Execute: grecaptcha.execute(key, {action: 'name'})
  - Hidden field receives token
  - No div widget needed
```

---

## 17. ENVIRONMENT VARIABLES REFERENCE

```env
# Application
APP_NAME="Sodai"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sodai_db
DB_USERNAME=root
DB_PASSWORD=

# Session (using database driver)
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=false   # true in production
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

# Cache
CACHE_STORE=database

# Mail (Mailtrap for development)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sodai.com"
MAIL_FROM_NAME="Sodai Admin"

# reCAPTCHA v3
RECAPTCHA_SITE_KEY=your_v3_site_key
RECAPTCHA_SECRET_KEY=your_v3_secret_key
RECAPTCHA_THRESHOLD=0.5
```

---

## 18. USEFUL COMMANDS

```bash
# Development
php artisan serve           # Start dev server
npm run dev                 # Start Vite dev server

# Database
php artisan migrate                    # Run migrations
php artisan migrate:fresh --seed       # Fresh + seed
php artisan migrate:status             # Check migration status
php artisan db:seed                    # Run seeders only

# Cache
php artisan config:clear    # Clear config cache
php artisan cache:clear     # Clear app cache
php artisan route:clear     # Clear route cache
php artisan view:clear      # Clear view cache
php artisan optimize:clear  # Clear all caches

# Code Generation
php artisan make:controller Admin/ExampleController
php artisan make:model Example
php artisan make:migration create_examples_table
php artisan make:request Admin/ExampleRequest
php artisan make:middleware ExampleMiddleware
php artisan make:mail Admin/ExampleMail

# Routes
php artisan route:list                 # List all routes
php artisan route:list | grep admin    # Admin routes only

# Debugging
php artisan tinker         # Interactive shell
tail -f storage/logs/laravel.log  # Watch logs
```

---

*Last Updated: After admin authentication completion*
*Next Step: Admin Dashboard with sidebar layout*
```

---

**This file is now ready. Save it as `project-instructions.md` in your project root. Any LLM model can read this file and immediately understand the full project context. Ready to move to Admin Dashboard!**