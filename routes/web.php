<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\koki\KokiController; // PERBAIKAN: Jalur subfolder koki yang benar
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
            return redirect('/pelanggan/orders'); // PERBAIKAN: Role 3 masuk Pelanggan
        } elseif ($roleId == 4) {
            return redirect('/koki');             // PERBAIKAN: Role 4 masuk Koki
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
        return redirect('/pelanggan/orders'); // PERBAIKAN: Role 3 masuk Pelanggan
    } elseif ($roleId == 4) {
        return redirect('/koki');             // PERBAIKAN: Role 4 masuk Koki
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

// Timpa rute order lu yang lama jadi memanggil MenuController
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');
});

// Karena pelanggan belum login harus bisa liat menu, taruh rute ini di luar/bebas:
Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');

// ==========================================
// AREA KOKI (CHEF)
// ==========================================
Route::middleware(['auth', 'role:koki'])->prefix('koki')->name('koki.')->group(function () {

    // GET  /koki           → daftar antrian
    Route::get('/',                  [KokiController::class, 'index'])     ->name('index');

    // GET  /koki/{id}      → detail satu pesanan
    Route::get('/{id}',              [KokiController::class, 'detail'])    ->name('detail');

    // GET  /koki/{id}/selesai → halaman sukses setelah pesanan selesai
    Route::get('/{id}/selesai',      [KokiController::class, 'selesai'])   ->name('selesai');

    // POST /koki/{orderId}/item/{itemId}/toggle → toggle centang satu item (AJAX)
    Route::post('/{orderId}/item/{itemId}/toggle', [KokiController::class, 'toggleItem'])->name('item.toggle');

    // POST /koki/{id}/selesaikan → tandai seluruh pesanan selesai (AJAX)
    Route::post('/{id}/selesaikan',  [KokiController::class, 'selesaikan'])->name('selesaikan');

    // POST /koki/{id}/batalkan  → batalkan pesanan (AJAX, hanya saat 'menunggu')
    Route::post('/{id}/batalkan',    [KokiController::class, 'batalkan'])  ->name('batalkan');
});