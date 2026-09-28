# PanDev

Website resmi PanDev (software house Indonesia) — dibangun dengan Laravel 12 + Blade + Alpine.js + Tailwind CSS v4.

## Setup

```bash
composer install
npm install
cp .env.example .env        # atur kredensial MySQL (pandev_db)
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
php artisan serve
```

Login admin seed: `ijichinijika@yopmail.com` / `password123`.

## Commands

```bash
php artisan test            # 41+ test (Pest-style via PHPUnit)
npm run build               # build aset Vite + Tailwind
composer run pint           # Laravel Pint code style
php artisan migrate:fresh --seed   # rebuild database + demo data
```

## Struktur

- Sitweb publik: home, layanan, portofolio (pencarian/filter/carousel), tentang, kontak.
- Dashboard: CMS portfolio, keuangan (transaksi + invoice/PDF), user management, settings.
- Auth session native; tidak ada registrasi publik — user dibuat oleh admin.
- Media: Cloudinary bila credential tersedia, fallback ke disk `public`.

## Env opsional

- `WEB3FORMS_ACCESS_KEY` — form kontak (Web3Forms).
- `CLOUDINARY_CLOUD_NAME/API_KEY/API_SECRET` — upload media jarak jauh.