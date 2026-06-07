<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil menu yang stoknya kritis (<= 5) untuk Peringatan Stok
        $lowStockMenus = DB::table('menus')
            ->where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->take(5) // Batasi maksimal 5 data yang tampil di dashboard
            ->get();

        // Data penjualan sementara di-hardcode dulu seperti permintaan Anda
        return view('admin.dashboard', compact('lowStockMenus'));
    }
}