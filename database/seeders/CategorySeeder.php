<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data master kategori sesuai kebutuhan filter kasir
        $categories = [
            ['name' => 'makanan'],
            ['name' => 'minuman'],
            ['name' => 'snack'],
        ];

        // Looping untuk menyimpan data ke database
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']], // Mencegah data duplikat jika di-seed ulang
                $category
            );
        }
    }
}