# Agent Instructions

## Project

PanDev — website software agency Indonesia. Aplikasi ini murni Laravel (12.x) + Blade + Alpine.js, dibangun ulang dari migrasi Next.js. Tidak ada sisa kode Next.js di repo ini.

## Commands

```bash
php artisan serve          # Dev server (default http://localhost:8000)
php artisan test           # Test suite (Pest-style via PHPUnit; 41+ tests)
php artisan migrate:fresh --seed   # Rebuild & seed database (pandev_db / MySQL)
npm run build              # Build aset Vite + Tailwind
composer run pint          # PHP Code Style Fixer (Laravel Pint)
```

## Structure

```
app/
  Http/Controllers/    # Dashboard, Portfolio CMS, Finance, UserManagement, Settings, Auth
  Models/              # User, Portfolio, PortfolioGalery, Transaction, Invoice, InvoiceItem
  Services/            # MediaService (Cloudinary fallback ke disk public)
  Support/             # Format (idr, thousandSeparator, relativeTime, url), PortfolioOptions, Cva
  Enums/               # PortfolioStatus, Role, InvoiceStatus, TransactionType
config/                 # media.php (Cloudinary + constraint upload), services.php
database/
  migrations/           # schema portfolio, finance, users (idiomata kolom juga)
  factories/  seeders/  # DatabaseSeeder: 4 user, 5 portfolio, 5 transaksi, 1 invoice
resources/
  views/                # Blade + Alpine (layouts/, components/ui, components/dashboard, pages/)
  css/app.css           # Tailwind CSS v4 (@source inline-html)
  js/app.js             # Alpine, modals, sidebar store, coverflow carousel
routes/
  web.php               # 36 route publik + dashboard (admin middleware via bootstrap/app.php)
  auth.php              # login/logout/update password (tidak ada registrasi publik)
public/
  assets/               # gambar statis (common/, profiles/, tech-stacks/)
tests/                  # Feature/MigrationSmokeTest + Auth
```

## Key Details

- **Auth**: native Laravel session auth. Tidak ada registrasi publik — user dibuat admin dari dashboard. Guest di-redirect ke `route('login')`; route admin dilindungi alias middleware `admin`.
- **DB**: MySQL `pandev_db` (root, tanpa password). Tabel pakai UUID (`HasUuids`). Admin login seed: `ijichinijika@yopmail.com` / `password123` (semua user seed password: `password123`).
- **UI**: Blade components `x-ui.*` (shadcn-style, CSS variables oklch), Alpine.js untuk interaktivitas, Lucide icons (lokal `x-lucide`).
- **Format angka**: `App\Support\Format::idr()` memformat manual (pemisah ribuan `"."`, desimal `","`) karena PHP di mesin ini TIDAK punya ekstensi `intl`.
- **PHP tanpa GD**: test upload memakai `UploadedFile::fake()->create()` (bukan `->image()`), karena validasi gambar Laravel hanya membaca MIME.
- **Media**: `MediaService` memakai `config('media.*')` (bukan `config('cloudinary.*')`). Jika `CLOUDINARY_CLOUD_NAME` kosong, upload jatuh ke disk `public` (`storage/app/public`).
- **Vite**: memakai `@tailwindcss/vite`; `vite.config.js` mengosongkan `css.postcss.plugins` agar tidak membaca `postcss.config.*` dari parent.
- **Dev script**: `composer run dev` menjalankan `php artisan serve` + `queue:listen` + `npm run dev` via `npx concurrently`. Pail dihapus dari script karena butuh ekstensi `pcntl` yang tidak ada di Windows/XAMPP (paket `laravel/pail` tetap ada di require-dev untuk deploy Linux/Mac).
- **Legacy Env**: Akun demo ditanam via seeder. Env vars `WEB3FORMS_ACCESS_KEY`, `CLOUDINARY_*` boleh kosong (fallback local).

## Test invariants

- `php artisan view:clear` setelah mengubah Blade sebelum `php artisan test`.
- `InvoiceController::updateStatus` mencatat income hanya sebesar **delta** (invoice_id FK pada transactions), hindari double-booking (partial→paid hanya sisa 50%).
- Form update portfolio mengirim hidden `galery[]` (URL yang dipertahankan) — tanpa itu controller ikut menghapus galeri lama.