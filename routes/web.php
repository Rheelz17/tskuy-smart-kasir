<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\koki\KokiController; // PERBAIKAN: Jalur subfolder koki yang benar
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KokiController;
use App\Http\Controllers\CashierOrderController;
use App\Http\Controllers\AdminMenuController;
use App\Http\Controllers\pelanggan\RiwayatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Auth; // <-- Tambahan wajib untuk ngecek sesi login

// ==========================================
// RUTE UTAMA (SPLASH SCREEN / REDIRECT)
// ==========================================
Route::get('/', function () {
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
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/penjualan', [AdminController::class, 'penjualan'])->name('penjualan');
    Route::get('/menu', [AdminController::class, 'menu'])->name('menu');

    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan');
    Route::get('/karyawan/tambah', [KaryawanController::class, 'tambah'])->name('karyawan.tambah');
    Route::get('/karyawan/{id}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit'); 
    
    // Proses Edit Karyawan (Passing ID lewat URL parameter lebih aman & rapi)
    Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::put('/karyawan/{id}/update', [KaryawanController::class, 'update'])->name('karyawan.update'); //  Untuk proses update data
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy'); // Untuk proses hapus data

    // 1. Halaman Utama Manajemen Menu (Desktop Table & Mobile Cards)
    Route::get('/menu', [AdminMenuController::class, 'index'])->name('menu');
    // 2. Halaman Tambah Menu Khusus Mobile full-page
    Route::get('/menu/tambah', [AdminMenuController::class, 'tambah'])->name('menu.tambah');
    // 3. Proses Simpan Menu Baru (AJAX POST dari Desktop Popup / Mobile Page)
    Route::post('/menu', [AdminMenuController::class, 'store'])->name('menu.store');
    // 4. Halaman Edit Menu Khusus Mobile full-page
    Route::get('/menu/{id}/edit', [AdminMenuController::class, 'edit'])->name('menu.edit');
    // 5. Proses Update Menu (AJAX PUT dari Desktop Popup / Mobile Page)
    Route::put('/menu/{id}', [AdminMenuController::class, 'update'])->name('menu.update');
    // 6. Proses Hapus Menu (AJAX DELETE dengan proteksi transaksi)
    Route::delete('/menu/{id}', [AdminMenuController::class, 'destroy'])->name('menu.destroy');
    Route::patch('/menu/{id}/toggle-status', [AdminMenuController::class, 'toggleStatus']);
});

// ==========================================
// AREA KASIR
// ==========================================
Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/pos', function () { return view('kasir.pos'); })->name('kasir.pos');
    Route::get('/kasir/manajemen-menu', function () { return view('kasir.manajemenMenu'); })->name('kasir.manajemen-menu');
    Route::get('/kasir/penjualan', function () { return view('kasir.penjualan'); })->name('kasir.penjualan');
});

// ==========================================
// WEBHOOK NOTIFIKASI MIDTRANS (Wajib di luar Auth Middleware)
// ==========================================
Route::post('/api/midtrans/notification', [CashierOrderController::class, 'handleNotification']);

// ==========================================
// AREA PELANGGAN (DATABASE INTEGRATED)
// ==========================================

// QR Table init — tidak perlu auth (pelanggan scan QR sebelum login)
Route::get('/table/{number}', [MenuController::class, 'initializeTable'])->name('table.init');

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
    Route::get('/', [KokiController::class, 'index'])->name('index');

    // GET  /koki/{id}      → detail satu pesanan
    Route::get('/{id}', [KokiController::class, 'detail'])->name('detail');

    // GET  /koki/{id}/selesai → halaman sukses setelah pesanan selesai
    Route::get('/{id}/selesai', [KokiController::class, 'selesai'])->name('selesai');

    // POST /koki/{orderId}/item/{itemId}/toggle → toggle centang satu item (AJAX)
    Route::post('/{orderId}/item/{itemId}/toggle', [KokiController::class, 'toggleItem'])->name('item.toggle');

    // POST /koki/{id}/selesaikan → tandai seluruh pesanan selesai (AJAX)
    Route::post('/{id}/selesaikan', [KokiController::class, 'selesaikan'])->name('selesaikan');

    // POST /koki/{id}/batalkan  → batalkan pesanan (AJAX, hanya saat 'menunggu')
    Route::post('/{id}/batalkan', [KokiController::class, 'batalkan'])->name('batalkan');
});