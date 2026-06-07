<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin (Dzaky Julian Putranto)
        User::updateOrCreate(
            ['email' => 'admin@tskuy.com'],
            [
                'employee_id' => 'ADM-001',
                'name' => 'Dzaky Julian Putranto',
                'username' => 'dzaky_admin',
                'phone' => '082125697616',
                'password' => Hash::make('password123'),
                'role_id' => 1,
                'is_active' => 1,
            ]
        );

        // 2. Akun Kasir (Rafli Ahmad Fauzi)
        User::updateOrCreate(
            ['email' => 'rafli@tskuy.com'],
            [
                'employee_id' => 'KSR-001',
                'name' => 'Rafli Ahmad Fauzi',
                'username' => 'rafli_kasir',
                'phone' => '08298765333',
                'password' => Hash::make('password123'),
                'role_id' => 2,
                'is_active' => 1,
            ]
        );

        // 3. Akun Koki (chef tskuy)
        User::updateOrCreate(
            ['email' => 'koki@tskuy.com'],
            [
                'employee_id' => 'CUST-XNVNWN',
                'name' => 'chef tskuy',
                'username' => 'chef_tskuy',
                'phone' => '08123456789',
                'password' => Hash::make('password123'),
                'role_id' => 4,
                'is_active' => 1,
            ]
        );

        // 4. Sampel Akun Pelanggan (Budi Santoso)
        User::updateOrCreate(
            ['email' => 'pembeli@gmail.com'],
            [
                'employee_id' => 'PLG-BUDI01',  
                'username' => 'budi_santoso',    
                'name' => 'Budi Santoso',
                'phone' => '085712345678',
                'password' => Hash::make('password123'),
                'role_id' => 3, 
                'is_active' => 1,
            ]
        );

        User::updateOrCreate(
            ['email' => 'junpuy@gmail.com'],
            [
                'employee_id' => 'PLG-00010',  
                'username' => 'junpuy',    
                'name' => 'junpuy',
                'phone' => '082345345678',
                'password' => Hash::make('password123'),
                'role_id' => 3, 
                'is_active' => 1,
            ]
        );
    }
}