<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\TiketController;
use App\Http\Controllers\Admin\HistoriesController;
use App\Http\Controllers\UserEventController;

use App\Models\Kategori;
use App\Models\Event;


//Di atas jangan lupa sertakan HomeController dulu
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PemesananController;


// home routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Event routes
Route::get('/events/{event}', [UserEventController::class, 'show'])->name('user.events.show');


Route::middleware('auth')->group(function () {

// User Routes
    Route::post('/events/{event}/tickets/{tiket}', [UserEventController::class, 'store'])->name('user.events.store');
    Route::get('/pemesanan', [PemesananController::class, 'index'])->name('pemesanan.index');
    Route::post('/pemesanan', [PemesananController::class, 'store'])->name('pemesanan.store');
    Route::get('/riwayat-pemesanan', [PemesananController::class, 'riwayat'])->name('pemesanan.riwayat');
    Route::get('/riwayat-pemesanan/{order}', [PemesananController::class, 'detail'])->name('pemesanan.detail');

// Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Category Management
        Route::resource('categories', CategoryController::class);

        // Event Management
        Route::resource('events', EventController::class);

        // Tiket Management 
        Route::resource('tickets', TiketController::class);

        // Histories
        Route::get('/histories', [HistoriesController::class, 'index'])->name('histories.index');
        Route::get('/histories/{id}', [HistoriesController::class, 'show'])->name('histories.show');
    });


    // profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
