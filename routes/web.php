<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\InvoiceController;
use App\Http\Controllers\Finance\TransactionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PortfolioController;
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
Route::post('/kontak', [PageController::class, 'submitContact'])->name('contact.submit');

Route::get('/portfolio', [PageController::class, 'portfolio'])->name('portfolio');
Route::get('/portfolio/list', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/portfolio/{uuid}', [PortfolioController::class, 'show'])->name('portfolio.show');

/*
|--------------------------------------------------------------------------
| Authenticated
|--------------------------------------------------------------------------
|
| The `auth` middleware mirrors the original src/proxy.ts route guard, and
| `admin` covers the screens the original only hid behind a UI check.
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Portfolio CMS — every action is scoped to the authenticated author.
    Route::get('/dashboard/portfolio', [PortfolioController::class, 'dashboardIndex'])->name('dashboard.portfolio.index');
    Route::get('/dashboard/portfolio/create', [PortfolioController::class, 'create'])->name('dashboard.portfolio.create');
    Route::post('/dashboard/portfolio', [PortfolioController::class, 'store'])->name('dashboard.portfolio.store');
    Route::get('/dashboard/portfolio/{uuid}/edit', [PortfolioController::class, 'edit'])->name('dashboard.portfolio.edit');
    Route::put('/dashboard/portfolio/{uuid}', [PortfolioController::class, 'update'])->name('dashboard.portfolio.update');
    Route::delete('/dashboard/portfolio/{uuid}', [PortfolioController::class, 'destroy'])->name('dashboard.portfolio.destroy');

    // Settings
    Route::get('/dashboard/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/dashboard/settings', [SettingsController::class, 'updateProfile'])->name('settings.update');
    Route::patch('/dashboard/settings/avatar', [SettingsController::class, 'updateAvatar'])->name('settings.avatar');

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard/user-management', [UserManagementController::class, 'index'])->name('dashboard.users.index');
        Route::post('/dashboard/user-management', [UserManagementController::class, 'store'])->name('dashboard.users.store');
        Route::put('/dashboard/user-management/{user}', [UserManagementController::class, 'update'])->name('dashboard.users.update');
        Route::delete('/dashboard/user-management/{user}', [UserManagementController::class, 'destroy'])->name('dashboard.users.destroy');

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
    });
});

require __DIR__.'/auth.php';
