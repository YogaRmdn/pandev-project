<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication routes
|--------------------------------------------------------------------------
|
| Mirrors the original Next.js app: there is no public registration, users
| are provisioned by an administrator from the dashboard's user management
| screen.
|
*/

Route::middleware('guest')->group(function () {
    Route::get('auth/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('auth/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Titik denyut dari komponen idleTimeout: menyegarkan stempel aktivitas
    // sesi server selama pengguna benar-benar aktif, tanpa memuat ulang halaman.
    Route::post('auth/heartbeat', [AuthenticatedSessionController::class, 'heartbeat'])
        ->name('auth.heartbeat');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
});
