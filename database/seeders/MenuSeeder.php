<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Mood;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================================
        // 1. AMBIL ATAU BUAT OTOMATIS KATEGORI
        // ========================================================
        $makanan = Category::updateOrCreate(['name' => 'makanan']);
        $minuman = Category::updateOrCreate(['name' => 'minuman']);
        $snack   = Category::updateOrCreate(['name' => 'snack']);

        // 2. Buat atau update Master Data Mood
        $chill = Mood::updateOrCreate(['name' => 'Chill']);
        $lapar = Mood::updateOrCreate(['name' => 'Lapar']);
        $fokus = Mood::updateOrCreate(['name' => 'Fokus Kerja']);

        // Kosongkan tabel pivot terlebih dahulu agar tidak duplikat saat di-seed ulang
        DB::table('menu_moods')->truncate();

        // ==========================================
        // 3. SEEDING DATA MENU & SYNC VIA DB TABLE
        // ==========================================

        // --- SAMPLE CATEGORY: MAKANAN ---
        
        // Nasi Goreng
        $nasgor = Menu::updateOrCreate(
            ['name' => 'Nasi Goreng Tskuy'],
            [
                'category_id'    => $makanan->id,
                'description'    => 'Nasi goreng ayam suwir legendaris dengan bumbu rahasia.',
                'price'          => 15000,
                'stock'          => 50,
                'image'          => 'nasi-goreng.png',
                'is_active'      => true,
                'is_recommended' => true, 
                'is_new'         => false,
                'is_promo'       => false
            ]
        );
        DB::table('menu_moods')->insert([
            ['menu_id' => $nasgor->id, 'mood_id' => $chill->id],
            ['menu_id' => $nasgor->id, 'mood_id' => $lapar->id],
        ]);

        // Mie Instan
        $mieInstan = Menu::updateOrCreate(
            ['name' => 'Mie Instan Inter準 Core i7'],
            [
                'category_id'    => $makanan->id,
                'description'    => 'Mie instan plus telur, sayur sawi segar, dan taburan bawang goreng.',
                'price'          => 12000,
                'stock'          => 100,
                'image'          => 'mie-instan.png',
                'is_active'      => true,
                'is_recommended' => false,
                'is_new'         => false,
                'is_promo'       => true 
            ]
        );
        DB::table('menu_moods')->insert([
            ['menu_id' => $mieInstan->id, 'mood_id' => $lapar->id]
        ]);


        // --- SAMPLE CATEGORY: MINUMAN ---

        // Kopi Susu Aren
        $kopiSusu = Menu::updateOrCreate(
            ['name' => 'Kopi Susu Aren Tskuy'],
            [
                'category_id'    => $minuman->id,
                'description'    => 'Kopi espresso dengan susu segar dan manisnya gula aren murni.',
                'price'          => 15000,
                'stock'          => 60,
                'image'          => 'kopi-susu.png',
                'is_active'      => true,
                'is_recommended' => true,
                'is_new'         => true, 
                'is_promo'       => false
            ]
        );
        DB::table('menu_moods')->insert([
            ['menu_id' => $kopiSusu->id, 'mood_id' => $chill->id],
            ['menu_id' => $kopiSusu->id, 'mood_id' => $fokus->id],
        ]);

        // Matcha Latte
        $matcha = Menu::updateOrCreate(
            ['name' => 'Matcha Latte Premium'],
            [
                'category_id'    => $minuman->id,
                'description'    => 'Matcha jepang pilihan dipadu dengan steam milk yang creamy.',
                'price'          => 18000,
                'stock'          => 40,
                'image'          => 'matcha.png',
                'is_active'      => true,
                'is_recommended' => false,
                'is_new'         => false,
                'is_promo'       => true 
            ]
        );
        DB::table('menu_moods')->insert([
            ['menu_id' => $matcha->id, 'mood_id' => $chill->id]
        ]);


        // --- SAMPLE CATEGORY: SNACK ---

        // Cireng
        $cireng = Menu::updateOrCreate(
            ['name' => 'Cireng Rujak Crispy'],
            [
                'category_id'    => $snack->id,
                'description'    => 'Cireng goreng garing renyah disajikan dengan sambal rujak pedas manis.',
                'price'          => 10000,
                'stock'          => 30,
                'image'          => 'cireng.png',
                'is_active'      => true,
                'is_recommended' => false,
                'is_new'         => true, 
                'is_promo'       => false
            ]
        );
        DB::table('menu_moods')->insert([
            ['menu_id' => $cireng->id, 'mood_id' => $chill->id]
        ]);
    }
}