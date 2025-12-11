<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // buat atau ambil role admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // buat user admin
        $user = User::firstOrCreate(
            ['email' => 'admin@gunungsarisedana.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('JunKaivan-20222'),
            ]
        );

        // assign role
        $user->assignRole($adminRole);
    }
}
