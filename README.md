# U-LINK - Platform Sharing UMKM

Platform untuk UMKM (Usaha Mikro Kecil Menengah) saling berbagi dan mempromosikan produk serta jasa mereka.

## Fitur Utama

### Sistem Autentikasi Multi-Role
Platform ini mendukung 3 jenis pengguna dengan hak akses berbeda:

1. **User (Pembeli)**
   - Melihat produk dan jasa dari UMKM
   - Mencari UMKM berdasarkan kategori
   - Menyimpan UMKM favorit
   - Memberikan review dan rating

2. **Admin Toko (UMKM)**
   - Mengelola profil toko UMKM
   - Menambah dan mengedit produk/jasa
   - Melihat statistik toko
   - Merespon review dari pelanggan
   - Promosi dagangan

3. **Super Admin**
   - Mengelola semua pengguna (User, Admin Toko)
   - Moderasi konten produk dan jasa
   - Melihat statistik platform secara keseluruhan
   - Mengelola kategori produk/jasa
   - Mengelola pengaturan sistem
   - Verifikasi dan approve UMKM baru

## Instalasi

### Prasyarat
- PHP 8.2 or higher
- Composer
- PostgreSQL
- Node.js & NPM

### Langkah Instalasi

1. Clone repository
```bash
git clone https://github.com/addinzulfikar/U-LINK.git
cd U-LINK
```

2. Install dependencies
```bash
composer install
npm install
```

3. Setup environment
```bash
cp .env.example .env
```

4. Konfigurasi database di file `.env`
```
DB_CONNECTION=pgsql
DB_HOST=your-database-host
DB_PORT=5432
DB_DATABASE=your-database-name
DB_USERNAME=your-username
DB_PASSWORD=your-password
```

5. Generate application key
```bash
php artisan key:generate
```

6. Run migrations
```bash
php artisan migrate
```

7. Seed database dengan akun default
```bash
php artisan db:seed
```

8. Build assets
```bash
npm run build
```

9. Start development server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## Akun Default untuk Testing

Setelah menjalankan seeder, Anda dapat login dengan akun berikut:

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@ulink.com | password123 |
| Admin Toko | admintoko@ulink.com | password123 |
| User | user@ulink.com | password123 |

## Struktur Routing

### Public Routes
- `/` - Halaman utama
- `/login` - Halaman login
- `/register` - Halaman registrasi

### Protected Routes (Memerlukan autentikasi)
- `/dashboard` - Dashboard untuk User (role: user)
- `/dashboard/admin-toko` - Dashboard untuk Admin Toko (role: admin_toko)
- `/dashboard/super-admin` - Dashboard untuk Super Admin (role: super_admin)
- `/logout` - Logout (POST)

## Teknologi yang Digunakan

- **Framework**: Laravel 12
- **Database**: PostgreSQL
- **Frontend**: Blade Templates + Tailwind CSS
- **Authentication**: Laravel's built-in authentication

## Role-Based Access Control

Sistem menggunakan middleware `role` untuk membatasi akses berdasarkan role pengguna:

```php
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('role:user');
```

## Pengembangan Selanjutnya

- [ ] Implementasi CRUD untuk produk/jasa UMKM
- [ ] Sistem pencarian dan filter
- [ ] Upload gambar produk
- [ ] Sistem rating dan review
- [ ] Notifikasi
- [ ] Dashboard analytics
- [ ] Export data

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
