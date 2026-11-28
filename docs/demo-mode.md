# Running Sodai as a public demo

Sodai is a portfolio project, so the intended public deployment is a demo: anyone can
browse, sign in with the seeded accounts, and place an order, and nobody receives real
email. This page covers the settings that make that safe and the one switch that enforces
it.

## The one switch

```dotenv
DEMO_MODE=true
```

`DEMO_MODE` is read by [`config/demo.php`](../config/demo.php) and applied at boot by
`App\Support\DemoEnvironment::enforce()`. When it is on:

- **every** configured mailer is replaced with the `log` transport, not just the default
  one, so `Mail::mailer('smtp')` cannot bypass it either;
- the queue connection is forced to `sync`, so queued mailables are handled in the request
  or command instead of waiting for a worker;
- a warning is written to the log if the configured mailer was not already `log`, so an
  operator notices a copied `.env`.

The point is that the demo does not depend on `MAIL_MAILER`, `QUEUE_CONNECTION` or the
`.env` being right. Registration, email verification, password reset, contact messages,
guest checkout confirmations and cart reminders all keep working — the messages are
written to the log instead of being delivered.

## Recommended demo `.env`

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://demo.example.com

DEMO_MODE=true
MAIL_MAILER=log
QUEUE_CONNECTION=sync

# A database of its own. Never the database a real shop uses.
DB_CONNECTION=sqlite
DB_DATABASE=/home/you/demo/database/demo.sqlite

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true

# The seeded demo accounts get this password instead of the built-in one.
SEED_DEMO_PASSWORD=change-me-before-publishing
```

`APP_ENV=production` is deliberate: it turns off debug pages and enables HSTS from
`SecurityHeaders`, while `DEMO_MODE` keeps email off.

## Throwaway database

The demo database is disposable by design. Build it once:

```bash
touch database/demo.sqlite
php artisan migrate --force
php artisan demo:seed
```

`php artisan demo:seed` needs `SEED_DEMO_PASSWORD` outside `local`/`development`/`testing`,
because the built-in demo password would otherwise be a live super-admin account. Resetting
the demo means throwing the file away and repeating those three commands; a scheduled reset
command is tracked in the roadmap.

Never point `DB_DATABASE` at an existing shop's database, and never run `demo:seed` against
one.

## Demo credentials

| Account | Email | Password |
| --- | --- | --- |
| Super admin | `demo-admin@example.com` | value of `SEED_DEMO_PASSWORD` (or `Demo#12345` locally) |
| Customer | `demo-customer@example.com` | same |

The demo seeder gives the admin the `super-admin` role, so do not treat the demo database
as containing anything private — and do not carry real customer rows into it.

## No real payment provider

Sodai takes **Cash On Delivery** only. There is no gateway package in `composer.json` and
no payment credentials in `.env.example`, `config/services.php` or the codebase, so a demo
checkout cannot charge anyone. Adding a gateway is on the roadmap; whoever does it will
have to gate it behind demo mode the same way mail is gated.

The only third-party request the app can make is reCAPTCHA verification, which uses
`RECAPTCHA_SITE_KEY`/`RECAPTCHA_SECRET_KEY`. Leave them unset unless you want the challenge;
the verification is skipped when no secret is configured.

## Checking a deployment

Email in a demo should only ever appear in `storage/logs/laravel.log`. After a guest
checkout, confirm the confirmation message is there and that no mail queue entry exists:

```bash
grep -i "order confirmation" storage/logs/laravel.log
php artisan queue:failed          # a demo with DEMO_MODE=true never queues mail
```

`tests/Feature/DemoModeTest.php` pins the behaviour: with demo mode on, both the default
mailer and a named `smtp` mailer resolve to Laravel's `LogTransport`, and the queue
connection becomes `sync`.
