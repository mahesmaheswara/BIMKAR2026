<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/

// Admin Controllers
use App\Http\Controllers\Admin\{
    DashboardController,
    CategoryController,
    EventController,
    TiketController,
    HistoriesController,
    TipeTiketController
};

// User / Public Controllers
use App\Http\Controllers\{
    HomeController,
    UserEventController,
    PemesananController,
    ProfileController
};

use App\Http\Controllers\User\OrderController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (TANPA LOGIN)
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Detail event (user)
Route::get('/events/{event}', [UserEventController::class, 'show'])
    ->name('user.events.show');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // =========================
    // USER ORDER / PEMESANAN
    // =========================
    Route::post('/events/{event}/tickets/{tiket}', [UserEventController::class, 'store'])
        ->name('user.events.store');

    Route::get('/pemesanan', [PemesananController::class, 'index'])
        ->name('pemesanan.index');

    Route::post('/pemesanan', [PemesananController::class, 'store'])
        ->name('pemesanan.store');

    Route::get('/riwayat-pemesanan', [PemesananController::class, 'riwayat'])
        ->name('pemesanan.riwayat');

    Route::get('/riwayat-pemesanan/{order}', [PemesananController::class, 'detail'])
        ->name('pemesanan.detail');

    // =========================
    // USER ORDERS (ALT VIEW)
    // =========================
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{order}', [OrderController::class, 'show'])
        ->name('orders.show');

    Route::post('/orders', [OrderController::class, 'store'])
        ->name('orders.store');

    // =========================
    // PROFILE
    // =========================
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            // Dashboard
            Route::get('/', [DashboardController::class, 'index'])
                ->name('dashboard');

            // =========================
            // MASTER DATA
            // =========================
            Route::resource('categories', CategoryController::class);
            Route::resource('tipe-tiket', TipeTiketController::class);

            // =========================
            // EVENT & TICKET
            // =========================
            Route::resource('events', EventController::class);
            Route::resource('tickets', TiketController::class);

            // =========================
            // HISTORIES
            // =========================
            Route::get('/histories', [HistoriesController::class, 'index'])
                ->name('histories.index');

            Route::get('/histories/{id}', [HistoriesController::class, 'show'])
                ->name('histories.show');
        });
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES (LOGIN, REGISTER, dll)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
