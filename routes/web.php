<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\koki\KokiController; // Jalur subfolder koki yang benar
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\pelanggan\MenuController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
        } elseif ($roleId == 3) {
            return redirect('/pelanggan/orders'); // Role 3 masuk Pelanggan
        } elseif ($roleId == 4) {
            return redirect('/koki');             // Role 4 masuk Koki
        } else {
            return redirect('/');
        }
    }

    return view('splash');
});

// Timpa rute dashboard bawaan Breeze biar nggak nyasar
Route::get('/dashboard', function () {
    $roleId = Auth::user()->role_id;
    
    if ($roleId == 1) {
        return redirect('/admin/dashboard');
    } elseif ($roleId == 2) {
        return redirect('/kasir/pos');
    } elseif ($roleId == 3) {
        return redirect('/pelanggan/orders'); // Role 3 masuk Pelanggan
    } elseif ($roleId == 4) {
        return redirect('/koki');             // Role 4 masuk Koki
    } else {
        return redirect('/');
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
    // 1. Rute Halaman Utama POS Kasir
    Route::get('/kasir/pos', function () {
        return view('kasir.pos');
    })->name('kasir.pos');

    // 2. Rute Baru Halaman Manajemen Menu
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
// Rute buat di-scan di QR Code Meja (contoh: tskuy.com/table/4)
Route::get('/table/{number}', [MenuController::class, 'initializeTable'])->name('table.init');

// Karena pelanggan belum login harus bisa liat menu, taruh rute ini di luar/bebas middleware:
Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');

// ==========================================
// AREA KOKI (CHEF)
// ==========================================
Route::middleware(['auth', 'role:koki'])
    ->prefix('koki')
    ->name('koki.')
    ->group(function () {
 
        // ── Halaman Utama Antrian ──────────────────────────────
        Route::get('/', [KokiController::class, 'index'])->name('index');
 
        // ── API Polling (auto-refresh tanpa reload) ───────────
        // GET /koki/api/orders → JSON daftar pesanan aktif
        Route::get('/api/orders', [KokiController::class, 'apiOrders'])->name('api.orders');
 
        // ── Aksi Tombol Status Order ───────────────────────────
        // POST /koki/{id}/mulai-masak → PENDING → COOKING
        Route::post('/{id}/mulai-masak', [KokiController::class, 'mulaiMasak'])->name('mulai-masak');
 
        // POST /koki/{id}/selesaikan → PENDING/COOKING → READY
        Route::post('/{id}/selesaikan', [KokiController::class, 'selesaikan'])->name('selesaikan');
 
        // POST /koki/{id}/batalkan → PENDING → CANCELLED
        Route::post('/{id}/batalkan', [KokiController::class, 'batalkan'])->name('batalkan');
 
        // ── Aksi Update Status Per Item (opsional) ─────────────
        // POST /koki/{orderId}/item/{itemId}/update
        Route::post('/{orderId}/item/{itemId}/update', [KokiController::class, 'updateItem'])
            ->name('item.update');
    });
 