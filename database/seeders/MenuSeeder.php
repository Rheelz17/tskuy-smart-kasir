<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Mood;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // PEMBERSIHAN OTOMATIS: Menggunakan nama tabel pivot baru 'menu_mood'
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('menu_moods')->truncate();
        DB::table('menus')->truncate();
        DB::table('moods')->truncate();
        DB::table('categories')->truncate();
        DB::table('tables')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. DATA MEJA KONSOL POS DIGITAL
        DB::table('tables')->insert([
            ['table_number' => '01', 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['table_number' => '02', 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
            ['table_number' => '03', 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['table_number' => '04', 'status' => 'available', 'created_at' => now(), 'updated_at' => now()],
            ['table_number' => '05', 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. MEMBUAT DATA KATEGORI INDUK
        $makanan = Category::create(['name' => 'Makanan']);
        $minuman = Category::create(['name' => 'Minuman']);
        $snack   = Category::create(['name' => 'Snack']);

        // 3. MEMBUAT DATA MOOD
        $chill = Mood::create(['name' => 'Chill', 'icon' => 'chill.png']);
        $lapar = Mood::create(['name' => 'Lapar', 'icon' => 'lapar.png']);
        $hemat = Mood::create(['name' => 'Hemat', 'icon' => 'hemat.png']);
        $segar = Mood::create(['name' => 'Segar', 'icon' => 'segar.png']);

        // 4. MEMBUAT PRODUK KULINER (Data Lama Di-Keep Penuh + Fitur Struktur Baru)

        // ════════ MAKANAN (category_id: 1) ════════
        $nasgor = Menu::create([
            'category_id'    => $makanan->id,
            'name'           => 'Nasi Goreng',
            'description'    => 'Nasi goreng ayam suwir dengan telur mata sapi lezat',
            'price'          => 15000,
            'image'          => 'nasi-goreng.png',
            'stock'          => 50,
            'is_available'   => true,
            'is_recommended' => true,
            'is_new'         => false,
            'is_promo'       => false
        ]);
        $nasgor->moods()->attach([$chill->id, $lapar->id]);

        $mie_bangladesh = Menu::create([
            'category_id'    => $makanan->id,
            'name'           => 'Indomie Bangladesh',
            'description'    => 'Mie instan dengan bumbu rempah khas kental pedas gurih legendaris',
            'price'          => 18000,
            'image'          => 'mie_bangladesh.png',
            'stock'          => 40,
            'is_available'   => true,
            'is_recommended' => false,
            'is_new'         => true,
            'is_promo'       => false
        ]);
        $mie_bangladesh->moods()->attach([$lapar->id]);

        $nasgor_omelet = Menu::create([
            'category_id'    => $makanan->id,
            'name'           => 'Nasi Omelet',
            'description'    => 'Sajian nasi hangat yang dibungkus dengan omelet telur lembut khas Tskuy',
            'price'          => 17000,
            'image'          => 'nasi_omelet.png',
            'stock'          => 35,
            'is_available'   => true,
            'is_recommended' => false,
            'is_new'         => false,
            'is_promo'       => true
        ]);
        $nasgor_omelet->moods()->attach([$lapar->id, $hemat->id]);

        $seblak = Menu::create([
            'category_id'    => $makanan->id,
            'name'           => 'Seblak Bandung',
            'description'    => 'Seblak pedas kuah kental gurih dengan isian kerupuk lumer, makaroni, dan bakso',
            'price'          => 15000,
            'image'          => 'sebalak_bandung.png',
            'stock'          => 30,
            'is_available'   => true,
            'is_recommended' => true,
            'is_new'         => false,
            'is_promo'       => false
        ]);
        $seblak->moods()->attach([$lapar->id, $segar->id]);


        // ════════ MINUMAN (category_id: 2) ════════
        $es_kopi = Menu::create([
            'category_id'    => $minuman->id,
            'name'           => 'Es Kopi Susu Aren',
            'description'    => 'Espresso blend susu segar premium dicampur sirup gula aren murni',
            'price'          => 15000,
            'image'          => 'es-kopi-ABC.png',
            'stock'          => 100,
            'is_available'   => true,
            'is_recommended' => true,
            'is_new'         => false,
            'is_promo'       => false
        ]);
        $es_kopi->moods()->attach([$chill->id, $hemat->id]);

        $kopi_hitam = Menu::create([
            'category_id'    => $minuman->id,
            'name'           => 'Kopi Hitam Tskuy',
            'description'    => 'Seduhan kopi hitam robusta murni yang cocok menemani waktu santai',
            'price'          => 8000,
            'image'          => 'kopi.jpg',
            'stock'          => 120,
            'is_available'   => true,
            'is_recommended' => false,
            'is_new'         => false,
            'is_promo'       => true
        ]);
        $kopi_hitam->moods()->attach([$chill->id, $hemat->id]);


        // ════════ SNACK / CEMILAN (category_id: 3) ════════
        $cireng = Menu::create([
            'category_id'    => $snack->id,
            'name'           => 'Cireng Crispy',
            'description'    => 'Cireng renyah di luar lembut di dalam dicocol bumbu rujak pedas',
            'price'          => 12000,
            'image'          => 'cireng.png',
            'stock'          => 45,
            'is_available'   => true,
            'is_recommended' => true,
            'is_new'         => false,
            'is_promo'       => false
        ]);
        $cireng->moods()->attach([$chill->id, $hemat->id]);

        $pancong_keju = Menu::create([
            'category_id'    => $snack->id,
            'name'           => 'Pancong Keju',
            'description'    => 'Kue pancong setengah matang lumer ditaburi keju parut berlimpah',
            'price'          => 14000,
            'image'          => 'pancong-keju.png',
            'stock'          => 35,
            'is_available'   => true,
            'is_recommended' => false,
            'is_new'         => true,
            'is_promo'       => false
        ]);
        $pancong_keju->moods()->attach([$chill->id]);

        $pancong_coklat = Menu::create([
            'category_id'    => $snack->id,
            'name'           => 'Pancong Coklat lumer',
            'description'    => 'Kue pancong lumer khas warkop dengan toping saus coklat melimpah',
            'price'          => 13000,
            'image'          => 'pancong-coklat.png',
            'stock'          => 40,
            'is_available'   => true,
            'is_recommended' => false,
            'is_new'         => false,
            'is_promo'       => true
        ]);
        $pancong_coklat->moods()->attach([$chill->id, $hemat->id]);

        $tahu_walik = Menu::create([
            'category_id'    => $snack->id,
            'name'           => 'Tahu Walik Gurih',
            'description'    => 'Tahu goreng dibalik dengan isian adonan bakso ayam krispi renyah',
            'price'          => 12000,
            'image'          => 'tahu_walik.png',
            'stock'          => 50,
            'is_available'   => true,
            'is_recommended' => true,
            'is_new'         => false,
            'is_promo'       => false
        ]);
        $tahu_walik->moods()->attach([$chill->id, $hemat->id]);
    }
}