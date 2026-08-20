<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    $user = auth()->user();

    if ($user->is_admin) {
        return redirect()->route('admin.index');
    }

    return redirect()->route('reservations.index');
})->middleware('auth')->name('home');

// '/reservations' for users
Route::get('/reservations', [ReservationController::class, 'index'])
    ->name('reservations.index')
    ->middleware(['auth', 'prevent-back-history-cache']);
Route::post('/reservations', [ReservationController::class, 'store'])
    ->name('reservations.store')
    ->middleware('auth');

// '/admin' for admin
Route::get('/admin', [AdminDashboardController::class, 'index'])
    ->name('admin.index')
    ->middleware(['auth', 'admin']);
Route::post('/admin/boats/{boat}/outside', [AdminDashboardController::class, 'moveOutside'])
    ->name('admin.boats.move-outside')
    ->middleware(['auth', 'admin']);
Route::post('/admin/boats/{boat}/inside', [AdminDashboardController::class, 'moveInside'])
    ->name('admin.boats.move-inside')
    ->middleware(['auth', 'admin']);

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'dashboard')->name('dashboard');
// });

require __DIR__ . '/settings.php';
