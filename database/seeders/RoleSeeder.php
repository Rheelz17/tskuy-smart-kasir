<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat role sesuai urutan ID
        Role::create(['name' => 'super_admin']); // ID: 1
        Role::create(['name' => 'kasir']);       // ID: 2
        Role::create(['name' => 'pelanggan']);   // ID: 3
    }
}