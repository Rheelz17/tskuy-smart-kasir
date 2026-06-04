<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'super_admin'],
            ['id' => 2, 'name' => 'kasir'],
            ['id' => 3, 'name' => 'pelanggan'],
            ['id' => 4, 'name' => 'koki'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }
    }
}