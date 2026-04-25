# SaaS Foundation

Foundational Laravel 13 SaaS setup with Breeze-style authentication, Blade, Tailwind CSS, a shared app shell, and role-based access control.

## Included

- Laravel 12 project structure
- Login, logout, and registration controllers and views
- Shared Blade layout with sidebar, topbar, logo placeholder, role badge, and consistent UI styles
- Roles: `super_admin`, `admin`, `agent`
- `role` field on users
- `role` route middleware alias
- Placeholder dashboard, users, sales, and attendance pages
- Super Admin tenant management at `/super/dashboard`
- Super Admin subscription plans at `/super/subscriptions`
- Admin agent management at `/admin/dashboard`
- Admin sales approval at `/admin/sales`
- Admin attendance review at `/admin/attendance`
- Admin leave approval at `/admin/leaves`
- Admin compensation setup at `/admin/compensation`
- Agent sales entry and tracking at `/agent/dashboard`
- Agent attendance clock-in/out at `/agent/attendance`
- Agent leave requests at `/agent/leaves`
- Agent earnings at `/agent/earnings`
- Seeded users:
  - `super@example.com` / `password`
  - `admin@example.com` / `password`
  - `agent@example.com` / `password`

## Local setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run dev
php artisan serve
```

Open the app at `http://127.0.0.1:8000`.
