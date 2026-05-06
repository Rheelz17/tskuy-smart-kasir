<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'employee_id' => 'ADM-001',
            'name'        => 'Dzaky Julian Putranto',
            'username'    => 'dzaky_admin',
            'email'       => 'admin@tskuy.com',
            'phone'       => '082125697616',
            'password'    => Hash::make('password123'),
            'role_id'     => 1, // Terhubung ke super_admin
            'is_active'   => true,
        ]);

        User::create([
            'employee_id' => 'KSR-001',
            'name'        => 'Rafli Ahmad Fauzi',
            'username'    => 'rafli_kasir',
            'email'       => 'rafli@tskuy.com',
            'phone'       => '08298765333',
            'password'    => Hash::make('password123'),
            'role_id'     => 2, // Terhubung ke kasir
            'is_active'   => true,
        ]);
    }
}