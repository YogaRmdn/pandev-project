<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary
    |--------------------------------------------------------------------------
    |
    | Used by App\Services\MediaService for the portfolio thumbnails, portfolio
    | galleries and user avatars. Leave the values blank locally and the service
    | falls back to the local `public` disk so uploads still work in dev.
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Media constraints
    |--------------------------------------------------------------------------
    |
    | Mirrors the dropzone limits from the original React forms: 5 MB for
    | portfolio media, 2 MB for avatars.
    |
    */

    'portfolio_disk' => env('MEDIA_DISK', 'public'),
    'portfolio_folder' => 'pandev/portfolio',
    'avatar_folder' => 'pandev/avatars',

    'portfolio_max_kb' => 5120,
    'avatar_max_kb' => 2048,

    'mimes' => ['jpg', 'jpeg', 'png', 'webp'],

];
