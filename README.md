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

Login seed (password `password123`): superadmin `superadminpndev@pandev.com`, admin `adminpandev@pandev.com`.

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

## SEO

Infrastruktur SEO halaman publik terpusat di `config/seo.php` + `app/Support/Seo.php`, dirender oleh `resources/views/partials/seo.blade.php` (dipasang di `layouts/app`).

- **Meta & social**: title, description, keywords, robots, canonical, theme-color, Open Graph, dan Twitter Card. Canonical/OG memakai origin request (`URL::to`) sehingga otomatis benar di localhost maupun produksi.
- **Structured data**: graph JSON-LD `Organization` + `WebSite` (situs-wide). Halaman menambah node sendiri lewat `@push('json-ld')` dengan komponen `<x-json-ld :data="\App\Support\Seo::graph([...])" />` (breadcrumb, `CreativeWork`, `ItemList`, `ContactPage`, dst.).
- **sitemap.xml & robots.txt**: dilayani route dinamis `SitemapController` (bukan file statis), jadi baris `Sitemap:` selalu mengikuti domain aktif. Draft portfolio tidak ikut terindeks (`noindex`) dan tidak masuk sitemap. Path admin sengaja tidak ditulis di `robots.txt` agar prefix rahasia tidak bocor.
- **Verifikasi Search Console**: isi `GOOGLE_SITE_VERIFICATION` (dan/atau `BING_SITE_VERIFICATION`) di `.env`, lalu daftarkan `https://<domain>/sitemap.xml` di Google Search Console.

Di produksi pastikan `APP_URL=https://<domain>` (mis. `https://panamdeveloper.com`) agar URL absolut di luar konteks request tetap benar.

## Env opsional

- `WEB3FORMS_ACCESS_KEY` — form kontak (Web3Forms).
- `CLOUDINARY_CLOUD_NAME/API_KEY/API_SECRET` — upload media jarak jauh.
- `GOOGLE_SITE_VERIFICATION` / `BING_SITE_VERIFICATION` — token verifikasi Search Console (metode HTML tag).
- `SEO_DEFAULT_TITLE`, `SEO_DEFAULT_DESCRIPTION`, `SEO_KEYWORDS`, `SEO_IMAGE`, `SEO_TWITTER`, `SEO_LOCALE`, `SEO_THEME_COLOR` — override default SEO tanpa menyentuh kode.
- `SEO_ORG_*` — data `schema.org/Organization` (nama, email, telepon, alamat, logo).