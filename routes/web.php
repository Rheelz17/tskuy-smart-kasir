<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\KokiController;
use App\Http\Controllers\CashierOrderController;
use App\Http\Controllers\AdminMenuController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- Tambahan wajib untuk ngecek sesi login
// use App\Http\Controllers\koki\KokiController;
use App\Http\Controllers\pelanggan\MenuController;
use App\Http\Controllers\pelanggan\RiwayatController;
use App\Http\Controllers\KasirController;

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
            return redirect('/koki');
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
    } elseif ($roleId == 3) {
        return redirect('/koki');
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
    Route::get('/penjualan', [AdminController::class, 'penjualan'])->name('penjualan');
    Route::get('/menu', [AdminController::class, 'menu'])->name('menu');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Halaman Karyawan (Dihandle KaryawanController)
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
    Route::post('/menu/{id}/toggle-status', [AdminMenuController::class, 'toggleStatus']);
});

// ==========================================
// AREA KASIR
// ==========================================
Route::middleware(['auth', 'role:kasir'])->group(function () {
    Route::get('/kasir/pos', [KasirController::class, 'pos']) ->name('kasir.pos');
    Route::get('/kasir/manajemen-menu', [KasirController::class, 'manajemenMenu'])->name('kasir.manajemen-menu');
    Route::get('/kasir/penjualan', function () { return view('kasir.penjualan'); })->name('kasir.penjualan');
    Route::patch('/menu/{id}/toggle-status', [AdminMenuController::class, 'toggleStatus']);
    Route::get('/kasir/api/antrian', [KasirController::class, 'apiAntrian'])->name('kasir.api.antrian');
    Route::get('/kasir/api/menu/{id}', function ($id) {
        $menu = \App\Models\Menu::with([
            'category',
            'options',
            'options.values',
        ])->where('is_available', true)->findOrFail($id);
    
        return response()->json($menu);
    })->name('kasir.api.menu.detail');
});

// ==========================================
// AREA PELANGGAN
// ==========================================
// Rute buat di-scan di QR Code Meja (contoh: tskuy.com/table/4)
Route::get('/table/{number}', [MenuController::class, 'initializeTable'])->name('table.init');

Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');
    Route::post('/pelanggan/checkout', [MenuController::class, 'checkout'])->name('pelanggan.checkout');
    Route::get('/pelanggan/riwayat', [RiwayatController::class, 'riwayat'])->name('pelanggan.riwayat');
});

Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');


// ==========================================
// AREA KOKI (CHEF)
// ==========================================
Route::middleware(['auth', 'role:koki'])->prefix('koki')->name('koki.')->group(function () {
    Route::get('/',                  [KokiController::class, 'index'])     ->name('index');
    Route::get('/{id}',              [KokiController::class, 'detail'])    ->name('detail');
    Route::get('/{id}/selesai',      [KokiController::class, 'selesai'])   ->name('selesai');
    Route::post('/{orderId}/item/{itemId}/toggle', [KokiController::class, 'toggleItem'])->name('item.toggle');
    Route::post('/{id}/selesaikan',  [KokiController::class, 'selesaikan'])->name('selesaikan');
    Route::post('/{id}/batalkan',    [KokiController::class, 'batalkan'])  ->name('batalkan');
});