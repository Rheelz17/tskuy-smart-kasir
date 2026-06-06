<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matikan proteksi foreign key biar bebas tabrak
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Bersihkan data lama
        DB::table('orders_item')->truncate();
        DB::table('orders')->truncate();
        DB::table('menus')->truncate();
        DB::table('categories')->truncate();
        DB::table('tables')->truncate(); // 👈 Tambah bersihin tabel meja
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ── 0. DATA MASTER MANUAL (PAKSA ID) ──────────────────
        
        // Buat Meja Dummy biar table_id ga NULL (Paksa ID = 1)
        DB::table('tables')->insert([
            'id' => 1,
            'table_number' => '03', // Nanti di controller kebaca sebagai nomor_meja
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('categories')->insert([
            'id' => 1, 
            'name' => 'Makanan', 
            'created_at' => now(), 
            'updated_at' => now()
        ]);

        DB::table('menus')->insert([
            'id' => 1,
            'category_id' => 1,
            'name' => 'Nasi Goreng Tskuy',
            'description' => 'Nasi goreng ayam suwir',
            'price' => 15000,
            'image' => 'nasi-goreng.png',
            'created_at' => now(),
            'updated_at' => now()
        ]);


        // ── 1. DATA PESANAN UTAMA (PAKSA TABLE_ID => 1) ──────────────────
        
        // Pesanan 1 (Mas Budi)
        $orderId1 = 1;
        DB::table('orders')->insert([
            'id'            => $orderId1,
            'order_code'    => 'TSK-060601',
            'customer_name' => 'Mas Budi',
            'table_id'      => 1, // 👈 Diisi ID Meja 1 (Ga boleh NULL lagi)
            'order_type'    => 'self order',
            'source'        => 'qr',
            'status'        => 'PENDING',   
            'is_open_bill'  => false,
            'subtotal'      => 15000.00,
            'tax'           => 1500.00,
            'total'         => 16500.00,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Pesanan 2 (Mbak Jessica)
        $orderId2 = 2;
        DB::table('orders')->insert([
            'id'            => $orderId2,
            'order_code'    => 'TSK-060602',
            'customer_name' => 'Mbak Jessica',
            'table_id'      => 1, // 👈 Diisi ID Meja 1 juga biar aman
            'order_type'    => 'cashier',   
            'source'        => 'kasir',
            'status'        => 'COOKING',   
            'is_open_bill'  => false,
            'subtotal'      => 15000.00,
            'tax'           => 1500.00,
            'total'         => 16500.00,
            'created_at'    => now()->subMinutes(6), 
            'updated_at'    => now(),
        ]);


        // ── 2. DATA DETAIL MAKANAN (TABEL orders_item) ──────────────────
        
        DB::table('orders_item')->insert([
            'id'         => 1,
            'order_id'   => $orderId1,
            'menu_id'    => 1, 
            'quantity'   => 1,
            'price'      => 15000.00,
            'note'       => 'Pedas karet dua ya koki',
            'status'     => 'PENDING',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('orders_item')->insert([
            'id'         => 2,
            'order_id'   => $orderId2,
            'menu_id'    => 1, 
            'quantity'   => 1,
            'price'      => 15000.00,
            'note'       => 'Tidak pakai sawi',
            'status'     => 'COOKING',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}