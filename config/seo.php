<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Dipakai halaman publik ketika sebuah view tidak mengisi section
    | `title` / `description` sendiri. Semua nilai bisa ditimpa lewat .env
    | supaya teks pemasaran tidak perlu diedit di kode saat musim kampanye.
    |
    | Operator `?:` dipakai sengaja: kalau variabel .env ada tapi dikosongkan
    | (mis. `SEO_DEFAULT_TITLE=` di .env.example), nilainya jatuh ke default
    | alih-alih menjadi string kosong.
    |
    */

    'default_title' => env('SEO_DEFAULT_TITLE') ?: 'PanDev | Software Development Agency in Indonesia',

    'default_description' => env('SEO_DEFAULT_DESCRIPTION') ?: 'PanDev is a professional software development agency in Indonesia. We design, build, and scale web, mobile, desktop, IoT, and data products for startups and growing businesses.',

    'keywords' => env('SEO_KEYWORDS') ?: implode(', ', [
        // Inti bisnis (layanan utama + lokasi).
        'software house indonesia',
        'jasa pembuatan website',
        'jasa pembuatan aplikasi',
        'jasa pembuatan aplikasi mobile',
        'jasa pengembangan software',
        'web development indonesia',
        'perusahaan pengembang software indonesia',
        'panam developer',
        'pandev',
        // Platform & stack (dicari klien yang sudah tahu teknologinya).
        'laravel developer',
        'react developer',
        'next.js developer',
        'flutter developer',
        'node.js developer',
        'backend developer indonesia',
        'mobile app development',
        'jasa pembuatan website company profile',
        'jasa pembuatan toko online',
        // Layanan pendukung.
        'it consultant indonesia',
        'jasa konsultan it',
        'outsourcing developer indonesia',
        'jasa migrasi website',
        'jasa maintenance website',
        'jasa pembuatan dashboard',
        // Spesialisasi yang membedakan PanDev.
        'cyber security indonesia',
        'penetration testing',
        'jasa audit keamanan website',
        'iot development',
        'jasa pembuatan iot',
        'embedded system indonesia',
        'data analytics indonesia',
        'gis indonesia',
        'jasa pembuatan aplikasi gis',
        'ui ux design',
        'desain grafis',
        'video editing',
    ]),

    /*
    |--------------------------------------------------------------------------
    | Open Graph / Twitter
    |--------------------------------------------------------------------------
    |
    | `image` adalah gambar fallback untuk kartu pratinjau saat halaman tidak
    | menyediakan `og_image` sendiri. Harus berukuran lebar (ideal 1200x630)
    | dan bisa berupa URL absolut (mis. Cloudinary) atau path lokal.
    |
    */

    'image' => env('SEO_IMAGE') ?: 'assets/common/new-hero-image.png',

    'twitter' => env('SEO_TWITTER') ?: null,

    'locale' => env('SEO_LOCALE') ?: 'id_ID',

    'theme_color' => env('SEO_THEME_COLOR') ?: '#ffffff',

    /*
    |--------------------------------------------------------------------------
    | Search Console Verification
    |--------------------------------------------------------------------------
    |
    | Tempel token verifikasi dari Google Search Console / Bing Webmaster di
    | .env; meta tag-nya hanya dirender kalau nilainya diisi. Ini metode
    | "HTML tag" sehingga tidak perlu mengunggah file ke public/.
    |
    */

    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION') ?: null,

    'bing_site_verification' => env('BING_SITE_VERIFICATION') ?: null,

    /*
    |--------------------------------------------------------------------------
    | Organization (JSON-LD)
    |--------------------------------------------------------------------------
    |
    | Data entitas untuk schema.org/Organization. Alamat lengkap sengaja
    | dibiarkan opsional; kirim lewat .env kalau kantor fisiknya sudah pasti.
    |
    */

    'organization' => [
        'name' => env('SEO_ORG_NAME') ?: env('APP_NAME', 'PanDev'),
        'email' => env('SEO_ORG_EMAIL') ?: 'info@pandev.dev',
        'phone' => env('SEO_ORG_PHONE') ?: null,
        'street' => env('SEO_ORG_STREET') ?: null,
        'city' => env('SEO_ORG_CITY') ?: null,
        'region' => env('SEO_ORG_REGION') ?: null,
        'country' => env('SEO_ORG_COUNTRY') ?: 'ID',
        'logo' => env('SEO_ORG_LOGO') ?: 'assets/common/logo.png',
    ],

];
