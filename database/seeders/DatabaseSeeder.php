<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil semua seeder di sini sesuai urutan yang bener
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            MenuSeeder::class,
        ]);
    }
}