<?php

namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function riwayat()
    {
        $tableNumber = Cookie::get('tskuy_table_number') ?? '4';
        $userId = auth()->id();

        // 1. Ambil Sesi Open Bill Aktif milik user ini + Join ke tabel tables untuk nomor meja
        $activeBill = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->where('orders.created_by', $userId)
            ->where('orders.is_open_bill', 1)
            ->whereIn('orders.status', ['PENDING', 'COOKING', 'READY'])
            ->orderBy('orders.created_at', 'desc')
            ->select('orders.*', 'tables.table_number')
            ->first();

        if ($activeBill) {
            $activeBill->items = DB::table('orders_item')
                ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
                ->where('orders_item.order_id', $activeBill->id)
                ->select('orders_item.*', 'menus.name as menu_name')
                ->get();
        }

        // 2. Ambil Riwayat Transaksi Lunas / Selesai + Join ke tabel tables
        $pastOrders = DB::table('orders')
            ->leftJoin('tables', 'orders.table_id', '=', 'tables.id')
            ->where('orders.created_by', $userId)
            ->where(function($query) {
                $query->where('orders.is_open_bill', 0)
                      ->orWhere('orders.status', 'COMPLETED');
            })
            ->orderBy('orders.created_at', 'desc')
            ->select('orders.*', 'tables.table_number')
            ->get();

        // Tarik data hidangan untuk setiap item di riwayat masa lalu agar detail popup berfungsi penuh
        foreach ($pastOrders as $order) {
            $order->items = DB::table('orders_item')
                ->join('menus', 'orders_item.menu_id', '=', 'menus.id')
                ->where('orders_item.order_id', $order->id)
                ->select('orders_item.*', 'menus.name as menu_name')
                ->get();
        }

        return view('pelanggan.riwayatPesanan', compact('tableNumber', 'activeBill', 'pastOrders'));
    }
}