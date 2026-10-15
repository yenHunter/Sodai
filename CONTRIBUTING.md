# Contributing Guide

## Setup Instructions
1. Clone the repository
2. Copy `.env.example` to `.env`
3. Run `composer install`
4. Run `npm install`
5. Run `php artisan key:generate`
6. Run `php artisan migrate`
7. Run `npm run dev`

A single command does steps 3–7: `composer run setup`

## Branching Strategy
- Create feature branches from `main`
- Name format: `feature/description` or `bugfix/description`
- CI (`.github/workflows/laravel.yml`) runs on pushes/PRs to `main` and `develop`
- Request review before merging

## Coding Standards
- Follow PSR-12; use Laravel Pint for formatting:
  `vendor/bin/pint` to fix, `vendor/bin/pint --test` to check (CI enforces the latter)
- Run `composer test` before pushing (CI runs `php artisan test` on SQLite)
- Add or update Feature tests for the behavior you change — new modules should ship with
  a suite covering permissions, validation, and business rules
- Run `composer audit` after dependency changes; do not introduce new advisories