<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Secret Admin Path Prefix
    |--------------------------------------------------------------------------
    |
    | Seluruh area admin — halaman login dan dashboard — dilayani di bawah
    | prefix ini. Nilainya berupa kombinasi angka rahasia (mis. "42904104")
    | supaya tidak mudah ditemukan path scanner.
    |
    | PENTING: ini hanya lapisan obscurity, BUKAN pengganti autentikasi.
    | Rate limit login, otorisasi role, dan scoping kepemilikan data tetap
    | penjaga utamanya.
    |
    | Diisi lewat .env (ADMIN_PATH). Kosongkan untuk menonaktifkan prefix
    | (route kembali ke /login dan /dashboard). Karena prefix ikut ter-cache
    | bersama route, setelah mengubahnya di produksi jalankan:
    |   php artisan optimize:clear && php artisan optimize
    |
    */

    'path' => trim((string) env('ADMIN_PATH', ''), '/'),

];
