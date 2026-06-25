# Contributing Guide

## Setup Instructions
1. Clone the repository
2. Copy `.env.example` to `.env`
3. Run `composer install`
4. Run `npm install`
5. Run `php artisan key:generate`
6. Run `php artisan migrate`
7. Run `npm run dev`

## Branching Strategy
- Create feature branches from `develop`
- Name format: `feature/description` or `bugfix/description`
- Create PR to `develop` (not `main`)
- Request review before merging

## Coding Standards
- Follow PSR-12
- Run `php artisan test` before pushing
- Use Laravel Pint for formatting