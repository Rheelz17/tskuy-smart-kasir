<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Mood;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $makanan = Category::create(['name' => 'Makanan']);
        $minuman = Category::create(['name' => 'Minuman']);

        $chill = Mood::create(['name' => 'Chill', 'icon' => 'chill.png']);
        $lapar = Mood::create(['name' => 'Lapar', 'icon' => 'lapar.png']);

        $nasgor = Menu::create([
            'category_id' => $makanan->id,
            'name' => 'Nasi Goreng',
            'description' => 'Nasi goreng ayam suwir',
            'price' => 15000,
            'image' => 'nasi-goreng.png'
        ]);
        // Hubungkan nasgor ke mood chill dan lapar
        $nasgor->moods()->attach([$chill->id, $lapar->id]);
    }
}
