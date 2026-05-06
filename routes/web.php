<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ==========================================
// AREA SUPER ADMIN
// ==========================================
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return 'Selamat datang di Dashboard Super Admin, Bos Dzaky!';
    });
});

// ==========================================
// AREA KASIR
// ==========================================
Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/pos', function () {
        return 'Selamat bertugas! Ini halaman Point of Sales Kasir.';
    });
});

// ==========================================
// AREA PELANGGAN
// ==========================================
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/pelanggan/orders', function () {
        return 'Ini halaman History Pesanan Pelanggan.';
    });
});
