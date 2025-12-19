<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Umkm;
use App\Models\Product;
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
        $adminToko = User::create([
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

        // Create Categories
        $categories = [
            ['name' => 'Makanan & Minuman', 'description' => 'Produk makanan dan minuman'],
            ['name' => 'Fashion', 'description' => 'Pakaian, sepatu, dan aksesoris'],
            ['name' => 'Kerajinan Tangan', 'description' => 'Produk handmade dan kerajinan'],
            ['name' => 'Kecantikan', 'description' => 'Produk kecantikan dan perawatan'],
            ['name' => 'Elektronik', 'description' => 'Produk elektronik dan gadget'],
            ['name' => 'Jasa', 'description' => 'Berbagai layanan jasa'],
            ['name' => 'Lainnya', 'description' => 'Kategori lainnya'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Sample UMKM
        $umkm = Umkm::create([
            'owner_user_id' => $adminToko->id,
            'name' => 'Toko Contoh',
            'slug' => 'toko-contoh',
            'description' => 'Ini adalah toko contoh untuk demo U-LINK',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 123',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'status' => Umkm::STATUS_APPROVED,
        ]);

        // Create Sample Products
        $makananCategory = Category::where('name', 'Makanan & Minuman')->first();
        
        Product::create([
            'umkm_id' => $umkm->id,
            'type' => Product::TYPE_PRODUCT,
            'name' => 'Kue Kering Spesial',
            'slug' => 'kue-kering-spesial',
            'description' => 'Kue kering dengan berbagai varian rasa yang lezat',
            'price' => 50000,
            'stock' => 100,
            'is_active' => true,
            'category_id' => $makananCategory->id,
        ]);

        Product::create([
            'umkm_id' => $umkm->id,
            'type' => Product::TYPE_SERVICE,
            'name' => 'Catering Event',
            'slug' => 'catering-event',
            'description' => 'Layanan catering untuk berbagai acara',
            'price' => 100000,
            'stock' => null,
            'is_active' => true,
            'category_id' => $makananCategory->id,
        ]);
    }
}
