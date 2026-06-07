<?php

namespace App\Http\Controllers;

use App\Models\Order; // 👈 Memastikan model Order di-import dengan benar
use Illuminate\Http\Request;

class AdminPenjualanController extends Controller
{
    /* ============================================================
       INDEX — Tampilkan halaman utama detail penjualan
       
       Eager load disesuaikan dengan kebutuhan komponen blade baru:
         - items.menu   → Mengambil kuantitas dan nama menu
         - payments     → Mengambil koleksi payment (metode bayar & status)
         - table        → Mengambil nomor meja untuk tipe Dine In
    ============================================================ */
    public function index()
    {
        $transactions = Order::with([
                'items.menu',   // Menghindari N+1 Query saat looping nama menu
                'payments',     // Mengambil data payment sesuai $order->payments->first() di blade
                'table'         // Mengambil data meja sesuai $order->table->number di blade
            ])
            ->latest()          // Mengurutkan dari transaksi terbaru
            ->paginate(10);     // Batasi 10 data per halaman agar pagination aktif

        // Mengirimkan variabel $transactions ke view 'admin.penjualan'
        return view('admin.penjualan', compact('transactions'));
    }
}