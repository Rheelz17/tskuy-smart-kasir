<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\koki\KokiController;

use App\Http\Controllers\pelanggan\MenuController;
use App\Http\Controllers\pelanggan\RiwayatController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminPenjualanController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminMenuController;

use App\Http\Controllers\KasirController;
use App\Http\Controllers\KasirPenjualanController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==========================================
// RUTE UTAMA (SPLASH SCREEN / REDIRECT)
// ==========================================
Route::get('/', function () {
    if (Auth::check()) {
        $roleId = Auth::user()->role_id;
        if ($roleId == 1) return redirect('/admin/dashboard');
        if ($roleId == 2) return redirect('/kasir/pos');
        if ($roleId == 3) return redirect('/pelanggan/orders');
        if ($roleId == 4) return redirect('/koki');
    }
    return view('splash');
});

Route::get('/tes', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Railway works'
    ]);
});

Route::get('/dashboard', function () {
    $roleId = Auth::user()->role_id;
    if ($roleId == 1) return redirect('/admin/dashboard');
    if ($roleId == 2) return redirect('/kasir/pos');
    if ($roleId == 3) return redirect('/pelanggan/orders');
    if ($roleId == 4) return redirect('/koki');
    return redirect('/');
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
    Route::get('/penjualan', [AdminPenjualanController::class, 'penjualan'])->name('penjualan');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan');
    Route::get('/karyawan/tambah', [KaryawanController::class, 'tambah'])->name('karyawan.tambah');
    Route::get('/karyawan/{id}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
    Route::post('/karyawan/store', [KaryawanController::class, 'store'])->name('karyawan.store');
    Route::put('/karyawan/{id}/update', [KaryawanController::class, 'update'])->name('karyawan.update');
    Route::delete('/karyawan/{id}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');

    Route::get('/menu', [AdminMenuController::class, 'index'])->name('menu');
    Route::get('/menu/tambah', [AdminMenuController::class, 'tambah'])->name('menu.tambah');
    Route::post('/menu/store', [AdminMenuController::class, 'store'])->name('menu.store');
    Route::get('/menu/{id}/edit', [AdminMenuController::class, 'edit'])->name('menu.edit');
    Route::put('/menu/{id}/update', [AdminMenuController::class, 'update'])->name('menu.update');
    Route::delete('/menu/{id}', [AdminMenuController::class, 'destroy'])->name('menu.destroy');
    Route::post('/menu/{id}/toggle-status', [AdminMenuController::class, 'toggleStatus']);
});

// ==========================================
// AREA KASIR
// ==========================================
Route::middleware(['auth', 'role:kasir'])->group(function () {

    // ── Halaman Utama POS ───────────────────────────────────
    Route::get('/kasir/pos', [KasirController::class, 'pos'])->name('kasir.pos');

    // ── Checkout dari POS kasir (AJAX POST dari pos.js) ─────
    Route::post('/kasir/pos-checkout', [KasirController::class, 'posCheckout'])->name('kasir.pos.checkout');

    // ── Aksi Antrian (AJAX dari pos.js) ────────────────────
    Route::post('/kasir/order/{orderId}/selesaikan', [KasirController::class, 'selesaikan'])->name('kasir.order.selesaikan');
    Route::post('/kasir/order/{orderId}/batalkan', [KasirController::class, 'batalkan'])->name('kasir.order.batalkan');

    // ── Pembayaran QRIS ─────────────────────────────────────
    Route::get('/kasir/payment/qris/{orderCode}', [KasirController::class, 'showQris'])->name('kasir.qris');
    Route::post('/kasir/payment/qris/{orderCode}/pay', [KasirController::class, 'simulatePay'])->name('kasir.qris.pay');

    // ── Struk Sukses ────────────────────────────────────────
    Route::get('/kasir/payment/success/{orderCode}', [KasirController::class, 'paymentSuccess'])->name('kasir.payment.success');

    // ── Manajemen Menu & Penjualan ──────────────────────────
    Route::get('/kasir/manajemen-menu', [KasirController::class, 'manajemenMenu'])->name('kasir.manajemen-menu');
    Route::get('/kasir/penjualan', [KasirPenjualanController::class, 'index'])->name('kasir.penjualan');
    Route::patch('/menu/{id}/toggle-status', [AdminMenuController::class, 'toggleStatus']);

    // ── API ─────────────────────────────────────────────────
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

// QR scan meja — tidak perlu login (redirect ke splash/login kalau belum auth)
Route::get('/table/{number}', [MenuController::class, 'initializeTable'])->name('table.init');

// Midtrans callback — tidak perlu auth (dipanggil dari server Midtrans)
Route::post('/midtrans/callback', [PaymentController::class, 'callback']);

Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');
    Route::post('/pelanggan/checkout', [MenuController::class, 'checkout'])->name('pelanggan.checkout');
    Route::post('/pelanggan/checkout/submit', [MenuController::class, 'checkout'])->name('pelanggan.checkout.submit');

    Route::get('/pelanggan/riwayat', [RiwayatController::class, 'riwayat'])->name('pelanggan.riwayat');

    // Open Bill
    Route::get('/pelanggan/order/{orderId}/continue', [MenuController::class, 'continueOrder'])->name('pelanggan.order.continue');
    Route::post('/pelanggan/checkout/{orderId}/add', [MenuController::class, 'addMoreItems'])->name('pelanggan.checkout.add');

    // Pembayaran
    Route::post('/payment/create', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/pelanggan/checkout/pay-now', [PaymentController::class, 'payNow'])->name('pelanggan.pay.now');
    Route::get('/pelanggan/payment/qris/{orderCode}', [MenuController::class, 'showQris'])->name('pelanggan.qris');
    Route::post('/pelanggan/payment/qris/{orderCode}/pay', [MenuController::class, 'simulatePay'])->name('pelanggan.qris.pay');
    Route::get('/pelanggan/payment/success/{orderCode}', [MenuController::class, 'paymentSuccess'])->name('pelanggan.payment.success');
});

// ==========================================
// AREA KOKI (CHEF)
// ==========================================
Route::middleware(['auth', 'role:koki'])->prefix('koki')->name('koki')->group(function () {
    Route::get('/', [KokiController::class, 'index'])->name('index');
    Route::get('/{id}', [KokiController::class, 'detail'])->name('detail');
    Route::get('/{id}/selesai', [KokiController::class, 'selesai'])->name('selesai');
    Route::get('/api/orders', [KokiController::class, 'apiOrders'])->name('api.orders');
    Route::post('/{orderId}/item/{itemId}/toggle', [KokiController::class, 'toggleItem'])->name('item.toggle');
    Route::post('/{id}/mulai-masak', [KokiController::class, 'mulaiMasak'])->name('mulai-masak');
    Route::post('/{id}/selesaikan', [KokiController::class, 'selesaikan'])->name('selesaikan');
    Route::post('/{id}/batalkan', [KokiController::class, 'batalkan'])->name('batalkan');
});