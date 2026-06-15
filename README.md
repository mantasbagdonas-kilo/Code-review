# Code Review

A small **Laravel 13** ecommerce application.

## Setup

```bash
composer install
php artisan key:generate
php artisan migrate --seed   # SQLite by default
```

Verify it boots:

```bash
php artisan route:list
php artisan serve
```

## Stack

- Laravel 13 / PHP 8.4
- SQLite (`database/database.sqlite`) for zero-config local runs
