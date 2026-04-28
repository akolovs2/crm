# Laravel CRM

Laravel based CRM system wired with Vue thru Inertia. Project for improving my technical and practical skills of developing Laravel based solutions.

## Stack

- **Laravel 13** + Fortify
- **Vue 3**
- **Inertia.js v3** — no separate API
- **Tailwind CSS v4** + Reka UI (shadcn-style components)
- **Wayfinder** — type-safe route helpers auto-generated from PHP routes

## What's done

- Switched from default Laravel's initial auth config to closed-type system with master admin, that can manage users
- Can manage contacts, companies, tags, relate contacts to a company. DB Seeder included

## TODO

- Create deals pipelines
- Dashboard — stats, recent activity
- Activity log per contact / deal

## Setup

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
# set DB credentials in .env
php artisan migrate --seed
npm run dev && php artisan serve
```
