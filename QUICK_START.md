# 🎯 Quick Start Guide - U-LINK Authentication System

## 📦 Apa yang Sudah Dibuat?

Sistem login lengkap untuk 3 jenis pengguna dengan fitur role-based access control.

---

## 🚀 Cara Cepat Mulai Menggunakan

### Step 1: Install Dependencies
```bash
composer install
```

### Step 2: Setup Environment
```bash
# Copy .env.example to .env
cp .env.example .env

# Update database credentials di .env jika perlu
# Sudah terisi dengan PostgreSQL dari requirement Anda
```

### Step 3: Setup Database
```bash
# Run migrations
php artisan migrate

# Seed database dengan akun default
php artisan db:seed
```

### Step 4: Start Server
```bash
php artisan serve
```

### Step 5: Test Login
Buka browser: `http://localhost:8000`

---

## 🔑 Akun Default untuk Testing

### Super Admin
```
Email: superadmin@ulink.com
Password: password123
Dashboard: http://localhost:8000/dashboard/super-admin
```
**Dapat mengakses**: Semua fitur admin platform

### Admin Toko (UMKM)
```
Email: admintoko@ulink.com
Password: password123
Dashboard: http://localhost:8000/dashboard/admin-toko
```
**Dapat mengakses**: Kelola toko dan produk/jasa

### User (Pembeli)
```
Email: user@ulink.com
Password: password123
Dashboard: http://localhost:8000/dashboard
```
**Dapat mengakses**: Browse produk/jasa, review, favorit

---

## 🎨 Halaman yang Tersedia

### Halaman Public (Tidak Perlu Login)
- `/` - Welcome page dengan deskripsi platform
- `/login` - Form login
- `/register` - Form pendaftaran akun baru

### Halaman Protected (Perlu Login)
- `/dashboard` - Dashboard User (hanya untuk role: user)
- `/dashboard/admin-toko` - Dashboard Admin Toko (hanya untuk role: admin_toko)
- `/dashboard/super-admin` - Dashboard Super Admin (hanya untuk role: super_admin)

---

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Authentication Tests Only
```bash
php artisan test --filter AuthenticationTest
```

**Expected Result:**
```
PASS  Tests\Feature\AuthenticationTest
  ✓ login page can be displayed
  ✓ register page can be displayed
  ✓ user can login with correct credentials
  ✓ admin toko redirected to correct dashboard
  ✓ super admin redirected to correct dashboard
  ✓ user cannot login with incorrect password
  ✓ user can register
  ✓ user cannot access admin toko dashboard
  ✓ admin toko cannot access super admin dashboard
  ✓ user can logout

Tests:    10 passed (20 assertions)
```

---

## 📖 Dokumentasi Lengkap

1. **README.md** - Project overview, instalasi, teknologi
2. **SETUP_GUIDE.md** - Panduan detail penggunaan semua fitur
3. **IMPLEMENTATION_SUMMARY.md** - Detail implementasi teknis
4. **QUICK_START.md** (file ini) - Cara cepat mulai

---

## ❓ FAQ

### Q: Bagaimana cara membuat akun baru?
A: Klik "Daftar" di navbar, isi form, pilih role (User atau Admin Toko)

### Q: Bagaimana cara membuat Super Admin baru?
A: Super Admin hanya bisa dibuat melalui database seeder atau oleh Super Admin lain (fitur ini bisa dikembangkan)

### Q: Apakah bisa ganti password?
A: Fitur change password bisa ditambahkan nanti di profile settings

### Q: Bagaimana cara logout?
A: Klik tombol "Logout" di navbar (hanya terlihat saat sudah login)

### Q: Apa yang terjadi jika User mencoba akses dashboard Admin Toko?
A: Akan muncul error 403: "Anda tidak memiliki akses ke halaman ini."

---

## 🎯 Next Steps - Fitur yang Bisa Ditambahkan

### 1. Profile Management
- [ ] Edit profile user
- [ ] Upload foto profile
- [ ] Change password
- [ ] Delete account

### 2. UMKM Management
- [ ] CRUD produk dan jasa
- [ ] Upload foto produk
- [ ] Kategori produk
- [ ] Harga dan stok

### 3. Search & Filter
- [ ] Search bar produk/jasa
- [ ] Filter kategori
- [ ] Filter lokasi
- [ ] Sort by rating/harga

### 4. Review System
- [ ] User beri review
- [ ] Rating bintang 1-5
- [ ] Admin toko balas review
- [ ] Moderasi review (super admin)

### 5. Favorites
- [ ] Save UMKM favorit
- [ ] List favorit di profile
- [ ] Notifikasi produk baru

---

## 🛠️ Troubleshooting

### Error: "could not translate host name"
**Solusi:** Database PostgreSQL tidak bisa diakses. Untuk testing lokal:
```bash
# Edit .env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Buat file database
touch database/database.sqlite

# Run ulang migrations
php artisan migrate:fresh --seed
```

### Error: "Class 'CheckRole' not found"
**Solusi:** 
```bash
composer dump-autoload
```

### Error: "CSRF token mismatch"
**Solusi:**
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Pastikan SESSION_DRIVER=file di .env
```

### Tests Failing
**Solusi:**
```bash
# Clear semua cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Run composer autoload
composer dump-autoload

# Run tests lagi
php artisan test
```

---

## 📞 Need Help?

Lihat dokumentasi lengkap:
- **SETUP_GUIDE.md** untuk panduan detail
- **IMPLEMENTATION_SUMMARY.md** untuk detail teknis
- **README.md** untuk overview project

---

## ✅ Checklist Implementasi

- [x] ✅ Database migration dengan role field
- [x] ✅ User model dengan role helpers
- [x] ✅ Authentication controller
- [x] ✅ Dashboard controllers
- [x] ✅ Role-based middleware
- [x] ✅ Login & register views
- [x] ✅ Dashboard views (3 roles)
- [x] ✅ Routes dengan protection
- [x] ✅ Database seeder
- [x] ✅ Test suite (10 tests)
- [x] ✅ Documentation (4 files)
- [x] ✅ Security improvements
- [x] ✅ Code review passed

**Status: ✅ SELESAI & SIAP DIGUNAKAN**

---

Selamat menggunakan U-LINK! 🎉
