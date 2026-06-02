<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- Tambahan wajib untuk ngecek sesi login

// ==========================================
// RUTE UTAMA (SPLASH SCREEN / REDIRECT)
// ==========================================
Route::get('/', function () {
    // Kalau user sudah login, arahkan ke halaman jabatannya
    if (Auth::check()) {
        $roleId = Auth::user()->role_id;
        
        if ($roleId == 1) {
            return redirect('/admin/dashboard');
        } elseif ($roleId == 2) {
            return redirect('/kasir/pos');
        } else {
            return redirect('/pelanggan/orders');
        }
    }

    // Kalau belum login, tampilkan Splash Screen
    return view('splash');
});

// Timpa rute dashboard bawaan Breeze biar nggak nyasar
Route::get('/dashboard', function () {
    $roleId = Auth::user()->role_id;
    
    if ($roleId == 1) {
        return redirect('/admin/dashboard');
    } elseif ($roleId == 2) {
        return redirect('/kasir/pos');
    } else {
        return redirect('/pelanggan/orders');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

// ==========================================
// RUTE PROFILE BAWAAN BREEZE
// ==========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ==========================================
// AREA SUPER ADMIN
// ==========================================
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Halaman Utama & Penjualan (Dihandle AdminController)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/penjualan', [AdminController::class, 'penjualan'])->name('penjualan');
    Route::get('/menu', [AdminController::class, 'menu'])->name('menu');

    // Halaman Karyawan (Dihandle KaryawanController)
    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan');
    Route::get('/karyawan/tambah', [KaryawanController::class, 'tambah'])->name('karyawan.tambah');
    Route::get('/karyawan/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
});

// ==========================================
// AREA KASIR
// ==========================================
Route::middleware(['auth', 'role:kasir'])->group(function () {
// 1. Rute Halaman Utama POS Kasir (Ditambahkan Name)
    Route::get('/kasir/pos', function () {
        return view('kasir.pos');
    })->name('kasir.pos');

    // 2. Rute Baru Halaman Manajemen Menu (Sesuai Struktur Folder Baru)
    Route::get('/kasir/manajemen-menu', function () {
        return view('kasir.manajemenMenu');
    })->name('kasir.manajemen-menu');

    // 3. Rute Placeholder untuk Detail penjualan
    Route::get('/kasir/penjualan', function () {
        return view('kasir.penjualan');
    })->name('kasir.penjualan');
});

// ==========================================
// AREA PELANGGAN
// ==========================================
use App\Http\Controllers\pelanggan\MenuController;

// Rute buat di-scan di QR Code Meja (contoh: tskuy.com/table/4)
Route::get('/table/{number}', [MenuController::class, 'initializeTable'])->name('table.init');

// Timpa rute order lu yang lama jadi memanggil MenuController
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    // Hapus rute Closure yang lama, ganti pakai ini
    Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');
});

// PENTING: Karena pelanggan belum login harus bisa liat menu, 
// pindahkan rute pelanggan.orders KELUAR dari middleware auth!
// Jadinya taruh rute ini di luar/bebas:
Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');