<?php

namespace App\Http\Controllers\pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function riwayat()
    {
        // 1. Ambil nomor meja dari cookie atau default ke Meja 4 untuk testing
        $tableNumber = \Cookie::get('tskuy_table_number') ?? '4';

        // 2. Buat objek dummy Sesi Aktif Open Bill
        $activeBill = (object) [
            'nota_id' => 'TSK-0291-04',
            'status' => 'Open Bill',
            'grand_total' => 57000,
            'items' => collect([
                (object) ['name' => '☕ Es Kopi Susu Tskuy', 'qty' => 2, 'price' => 15000, 'total' => 30000],
                (object) ['name' => '🍳 Indomie Bangladesh', 'qty' => 1, 'price' => 18000, 'total' => 18500],
                (object) ['name' => '🍟 Cireng Isi Ayam Suwir', 'qty' => 1, 'price' => 9000, 'total' => 9000],
            ])
        ];

        // 3. Buat array dummy riwayat transaksi lama yang sudah PAID (Lunas)
        $pastOrders = collect([
            (object) ['nota_id' => 'TSK-0102', 'date' => '03 Juni 2026', 'total' => 35000, 'method' => 'QRIS'],
            (object) ['nota_id' => 'TSK-0085', 'date' => '31 Mei 2026', 'total' => 22000, 'method' => 'Tunai'],
        ]);

        return view('pelanggan.riwayatPelanggan', compact('tableNumber', 'activeBill', 'pastOrders'));
    }
}