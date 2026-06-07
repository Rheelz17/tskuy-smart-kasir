<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\koki\KokiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\pelanggan\MenuController;
use App\Http\Controllers\pelanggan\RiwayatController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Auth;

// ==========================================
// RUTE UTAMA (SPLASH SCREEN / REDIRECT)
// ==========================================
Route::get('/', function () {
    if (Auth::check()) {
        $roleId = Auth::user()->role_id;
        if ($roleId == 1) return redirect('/admin/dashboard');
        elseif ($roleId == 2) return redirect('/kasir/pos');
        elseif ($roleId == 3) return redirect('/pelanggan/orders');
        elseif ($roleId == 4) return redirect('/koki');
        else return redirect('/');
    }
    return view('splash');
});

Route::get('/dashboard', function () {
    $roleId = Auth::user()->role_id;
    if ($roleId == 1) return redirect('/admin/dashboard');
    elseif ($roleId == 2) return redirect('/kasir/pos');
    elseif ($roleId == 3) return redirect('/pelanggan/orders');
    elseif ($roleId == 4) return redirect('/koki');
    else return redirect('/');
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
    Route::get('/karyawan/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
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
// AREA PELANGGAN (DATABASE INTEGRATED)
// ==========================================

// QR Table init — tidak perlu auth (pelanggan scan QR sebelum login)
Route::get('/table/{number}', [MenuController::class, 'initializeTable'])->name('table.init');

Route::middleware(['auth', 'role:pelanggan'])->group(function () {

    // ── Menu & Pesanan ──────────────────────────────────────
    Route::get('/pelanggan/orders', [MenuController::class, 'index'])->name('pelanggan.orders');
    Route::post('/pelanggan/checkout', [MenuController::class, 'checkout'])->name('pelanggan.checkout');

    // ── Riwayat ─────────────────────────────────────────────
    Route::get('/pelanggan/riwayat', [RiwayatController::class, 'riwayat'])->name('pelanggan.riwayat');

    // ── Open Bill: lanjut tambah pesanan ────────────────────
    // Redirect ke halaman menu dengan membawa active_order_id di session
    Route::get('/pelanggan/order/{orderId}/continue', [MenuController::class, 'continueOrder'])
        ->name('pelanggan.order.continue');

    // Submit item tambahan ke order open bill yang sudah ada
    Route::post('/pelanggan/checkout/{orderId}/add', [MenuController::class, 'addMoreItems'])
        ->name('pelanggan.checkout.add');

    // ── Pembayaran QRIS ─────────────────────────────────────
    // Halaman scan QRIS (pay_now baru & open bill yang mau bayar lunas)
    Route::get('/pelanggan/payment/qris/{orderCode}', [MenuController::class, 'showQris'])
        ->name('pelanggan.qris');

    // Konfirmasi / simulasi pembayaran → set status COMPLETED
    Route::post('/pelanggan/payment/qris/{orderCode}/pay', [MenuController::class, 'simulatePay'])
        ->name('pelanggan.qris.pay');

    // Struk sukses (hanya accessible jika status = COMPLETED)
    Route::get('/pelanggan/payment/success/{orderCode}', [MenuController::class, 'paymentSuccess'])
        ->name('pelanggan.payment.success');

    // ── Midtrans (jika masih dipakai) ───────────────────────
    Route::post('/midtrans/notification', [MenuController::class, 'midtransNotification'])
        ->withoutMiddleware([ValidateCsrfToken::class]);
});

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