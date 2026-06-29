# Dermovive Pharma — Portfolio & Products Platform

Premium, trilingual (Arabic · English · French) brand & product showcase for **Dermovive Pharma** cosmetics.
Phase 1 is **browse-only** (no cart/checkout/payment), architected to add commerce later without rework.

## Stack
- **frontend/** — Next.js 15 (App Router, TypeScript), Tailwind CSS v4, next-intl (ar/en/fr).
- **backend/** — Laravel 11 API + Filament 3 admin panel, MySQL 8/9.

## Architecture
```
dermovive/
├── backend/     Laravel API (/api/v1) + Filament admin (/admin)
├── frontend/    Next.js public site (3 locales)
└── docker-compose.yml   optional mailpit (mail catcher) for local dev
```
Decoupled / headless: Next.js renders the public site (SEO, RTL, full design control); Laravel owns data + admin.

## Local development

### Prerequisites
- PHP 8.2+, Composer 2.x
- Node 20+ / npm
- MySQL 8+ running locally (database `dermovive`, utf8mb4)

### Backend
```bash
cd backend
composer install
cp .env.example .env   # then set DB + APP creds
php artisan key:generate
php artisan migrate --seed
php artisan serve       # http://127.0.0.1:8000  (admin at /admin)
```

### Frontend
```bash
cd frontend
npm install
cp .env.example .env.local   # set NEXT_PUBLIC_API_URL
npm run dev                  # http://localhost:3000  (/en /ar /fr)
```

## Brand
- Coral `#E87A8E` · Deep teal `#1C4A45`
- Design language: elegant soft-luxury (editorial whitespace, serif display, subtle motion).

## Roadmap
Phased build — see `/Users/ahmedomar/.claude/plans/misty-rolling-pond.md`.
Phase 0 Foundations → Phase 1 Data+Admin → Phase 2 API → Phase 3 Home/Hero → Phase 4 Catalog → Phase 5 Product/Company → Phase 6 Polish/Deploy.
