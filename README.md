# Car Selling Website

A car marketplace built with Laravel 13 and PHP 8.3+.

## Features

- Browse and search cars with filters, sorting, and pagination
- Favourites and a personal watchlist
- Owner car CRUD with photo uploads
- Soft delete and restore for cars
- Authentication, registration, and password reset
- REST API protected by Sanctum tokens
- Queued "car published" notifications
- Cached catalog lookups and home page data

## Requirements

- PHP 8.3 or newer with the usual Laravel extensions
- Composer
- Node.js and npm only if you choose to use the unused Vite scaffold
- A database (SQLite by default, MySQL/PostgreSQL supported)

## Local setup

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

In a second terminal, run the queue worker used by publish notifications:

```bash
php artisan queue:work
```

### Seeded login

```
Email: test@example.com
Password: password
```

## Frontend assets

The site serves static CSS and JavaScript directly from `public/` (`/css/app.css` and `/js/app.js`, linked in `resources/views/layouts/base.blade.php`). The Vite/Tailwind scaffold (`vite.config.js`, `package.json`, `resources/css/app.css`, `resources/js/app.js`) is not referenced by any Blade view and is currently unused; it is kept for a possible future migration to Vite.

## Queue and mail

This project uses the database queue for queued work such as the "car published" notification. Start a worker with:

```bash
php artisan queue:work
```

With `MAIL_MAILER=log` in `.env`, outgoing mail is written to `storage/logs/laravel.log`. To send real email, set `MAIL_MAILER=smtp` and fill in `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, and the `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` values in `.env`.

Schedule the daily maintenance commands with:

```bash
php artisan schedule:work
```

## Running tests

```bash
php artisan test
```

## API overview

All endpoints are prefixed with `/api/v1`.

| Method | Endpoint | Description | Auth |
| --- | --- | --- | --- |
| POST | `/login` | Issue a Sanctum token | No |
| POST | `/logout` | Revoke the current token | Yes |
| GET | `/user` | Current user | Yes |
| GET | `/cars` | List published cars | No |
| GET | `/cars/{car}` | Show a car | No |
| GET | `/my-cars` | List the user's cars | Yes |
| POST | `/cars` | Create a car | Yes |
| PUT | `/cars/{car}` | Update a car | Yes |
| DELETE | `/cars/{car}` | Soft-delete a car | Yes |
| PATCH | `/cars/{car}/restore` | Restore a soft-deleted car | Yes |
| POST | `/cars/{car}/favourite` | Toggle a favourite | Yes |
| GET | `/favourites` | List favourite cars | Yes |

Send authenticated requests with `Authorization: Bearer <token>`.

## Production checklist

- Set `APP_ENV=production` and `APP_DEBUG=false`
- Generate and keep a real `APP_KEY`
- Configure a real `MAIL_MAILER` (SMTP or a transactional provider)
- Run a queue worker under a supervisor (for example `php artisan queue:work --tries=3`) and keep it alive
- Run `php artisan optimize`
- Run `php artisan storage:link`
- Add the scheduler cron entry: `* * * * * php artisan schedule:run`
- Ensure `storage/` and `bootstrap/cache/` are writable by the web server
