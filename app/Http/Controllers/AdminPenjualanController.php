<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AdminPenjualanController extends Controller
{
    public function penjualan()
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