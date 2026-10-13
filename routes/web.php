<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EbookController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\TransactionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PortfolioMediaController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/layanan', [PageController::class, 'services'])->name('services');
Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::post('/kontak', [PageController::class, 'submitContact'])
    ->middleware('throttle:6,1')
    ->name('contact.submit');

Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');
Route::get('/portfolio/list', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{uuid}', [PortfolioController::class, 'show'])->name('portfolio.show');

Route::get('/buy-ebook', [PageController::class, 'buyEbook'])->name('buy-ebook');

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
|
| The `auth` middleware mirrors the original src/proxy.ts route guard.
| Seluruh grup dilayani di bawah prefix rahasia (config admin.path) supaya
| path-nya tidak mudah ditemukan scanner. Nama route sengaja TIDAK berubah,
| jadi seluruh `route('dashboard.*')` di view/test ikut menyesuaikan.
|
*/

Route::prefix(config('admin.path'))->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Portfolio CMS — every action is scoped to the authenticated author.
    Route::get('/dashboard/portfolio', [PortfolioController::class, 'dashboardIndex'])->name('dashboard.portfolio.index');
    Route::get('/dashboard/portfolio/create', [PortfolioController::class, 'create'])->name('dashboard.portfolio.create');
    Route::post('/dashboard/portfolio', [PortfolioController::class, 'store'])->name('dashboard.portfolio.store');
    Route::get('/dashboard/portfolio/{uuid}/edit', [PortfolioController::class, 'edit'])->name('dashboard.portfolio.edit');
    Route::put('/dashboard/portfolio/{uuid}', [PortfolioController::class, 'update'])->name('dashboard.portfolio.update');

    // Instant media (Dropzone) — static paths must precede the {uuid} routes.
    Route::post('/dashboard/portfolio/media', [PortfolioMediaController::class, 'store'])->name('dashboard.portfolio.media.store');
    Route::delete('/dashboard/portfolio/media', [PortfolioMediaController::class, 'destroy'])->name('dashboard.portfolio.media.destroy');
    Route::delete('/dashboard/portfolio/{uuid}/thumbnail', [PortfolioMediaController::class, 'destroyThumbnail'])->name('dashboard.portfolio.thumbnail.destroy');
    Route::delete('/dashboard/portfolio/{uuid}/galery/{galery}', [PortfolioMediaController::class, 'destroyGalery'])->name('dashboard.portfolio.galery.destroy');

    Route::delete('/dashboard/portfolio/{uuid}', [PortfolioController::class, 'destroy'])->name('dashboard.portfolio.destroy');

    // Settings
    Route::get('/dashboard/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/dashboard/settings', [SettingsController::class, 'updateProfile'])->name('settings.update');
    Route::patch('/dashboard/settings/avatar', [SettingsController::class, 'updateAvatar'])->name('settings.avatar');

    // User management — superadmin only.
    Route::middleware('superadmin')->group(function () {
        Route::get('/dashboard/user-management', [UserManagementController::class, 'index'])->name('dashboard.users.index');
        Route::post('/dashboard/user-management', [UserManagementController::class, 'store'])->name('dashboard.users.store');
        Route::put('/dashboard/user-management/{user}', [UserManagementController::class, 'update'])->name('dashboard.users.update');
        Route::delete('/dashboard/user-management/{user}', [UserManagementController::class, 'destroy'])->name('dashboard.users.destroy');

        // Log aktivitas — superadmin only.
        Route::get('/dashboard/activity-log', [ActivityLogController::class, 'index'])->name('dashboard.activity-log.index');
        Route::delete('/dashboard/activity-log', [ActivityLogController::class, 'clear'])->name('dashboard.activity-log.clear');
        Route::delete('/dashboard/activity-log/{log}', [ActivityLogController::class, 'destroy'])->name('dashboard.activity-log.destroy');
    });

    // Finance & invoice — admin (and superadmin) only.
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard/finance', [TransactionController::class, 'index'])->name('dashboard.finance.index');
        Route::post('/dashboard/finance', [TransactionController::class, 'store'])->name('dashboard.finance.store');
        Route::put('/dashboard/finance/{transaction}', [TransactionController::class, 'update'])->name('dashboard.finance.update');
        Route::delete('/dashboard/finance/{transaction}', [TransactionController::class, 'destroy'])->name('dashboard.finance.destroy');

        Route::get('/dashboard/invoice', [InvoiceController::class, 'index'])->name('dashboard.invoice.index');
        Route::post('/dashboard/invoice', [InvoiceController::class, 'store'])->name('dashboard.invoice.store');
        Route::put('/dashboard/invoice/{invoice}', [InvoiceController::class, 'update'])->name('dashboard.invoice.update');
        Route::patch('/dashboard/invoice/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('dashboard.invoice.status');
        Route::delete('/dashboard/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('dashboard.invoice.destroy');
        Route::get('/dashboard/invoice/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('dashboard.invoice.pdf');

        // Ebook manager — titik link ke katalog Lynk.id.
        Route::get('/dashboard/ebook', [EbookController::class, 'index'])->name('dashboard.ebook.index');
        Route::get('/dashboard/ebook/create', [EbookController::class, 'create'])->name('dashboard.ebook.create');
        Route::post('/dashboard/ebook', [EbookController::class, 'store'])->name('dashboard.ebook.store');
        Route::get('/dashboard/ebook/{ebook}/edit', [EbookController::class, 'edit'])->name('dashboard.ebook.edit');
        Route::put('/dashboard/ebook/{ebook}', [EbookController::class, 'update'])->name('dashboard.ebook.update');
        Route::delete('/dashboard/ebook/{ebook}', [EbookController::class, 'destroy'])->name('dashboard.ebook.destroy');
    });
});

require __DIR__.'/auth.php';
