<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@ulink.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_SUPER_ADMIN,
        ]);

        // Create Admin Toko (UMKM) Sample
        User::create([
            'name' => 'Admin Toko Contoh',
            'email' => 'admintoko@ulink.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN_TOKO,
        ]);

        // Create Regular User Sample
        User::create([
            'name' => 'User Contoh',
            'email' => 'user@ulink.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_USER,
        ]);
    }
}
