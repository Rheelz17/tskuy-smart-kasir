<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin (Tetap)
        User::updateOrCreate(
            ['email' => 'admin@tskuy.com'],
            [
                'employee_id' => 'ADM-001',
                'name' => 'Dzaky Julian Putranto',
                'username' => 'dzaky_admin',
                'phone' => '082125697616',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'is_active' => true,
            ]
        );

        // 2. Akun Kasir (Tetap)
        User::updateOrCreate(
            ['email' => 'rafli@tskuy.com'],
            [
                'employee_id' => 'KSR-001',
                'name' => 'Rafli Ahmad Fauzi',
                'username' => 'rafli_kasir',
                'phone' => '08298765333',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'is_active' => true,
            ]
        );

        // 3. Akun Koki (Baru)
        User::updateOrCreate(
            ['email' => 'koki@tskuy.com'],
            [
                'employee_id' => 'KKI-001',
                'name' => 'Chef Tskuy',
                'username' => 'chef_tskuy',
                'phone' => '08123456789',
                'password' => Hash::make('password123'),
                'role_id' => 4, // Role Koki
                'is_active' => true,
            ]
        );

        // 4. Sampel Akun Pelanggan untuk Testing History & Split Bill
        User::updateOrCreate(
            ['email' => 'pembeli@gmail.com'],
            [
                'employee_id' => null,  // Kosong karena pelanggan bukan karyawan
                'username' => null,     // Kosong karena login via Email/No HP
                'name' => 'Budi Santoso',
                'phone' => '085712345678',
                'password' => Hash::make('password123'),
                'role_id' => 3, // Role Pelanggan
                'is_active' => true,
            ]
        );
    }
}