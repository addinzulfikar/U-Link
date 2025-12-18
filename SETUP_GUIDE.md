# Panduan Penggunaan Sistem Login U-LINK

## Daftar Isi
1. [Akses Halaman Login](#akses-halaman-login)
2. [Registrasi Akun Baru](#registrasi-akun-baru)
3. [Login dengan Akun Default](#login-dengan-akun-default)
4. [Dashboard Berdasarkan Role](#dashboard-berdasarkan-role)

---

## Akses Halaman Login

### URL Login
```
http://localhost:8000/login
```

### Cara Login
1. Masukkan email
2. Masukkan password
3. Opsional: centang "Ingat saya" untuk tetap login
4. Klik tombol "Login"

Setelah login berhasil, Anda akan diarahkan ke dashboard sesuai role:
- **User** → `/dashboard`
- **Admin Toko** → `/dashboard/admin-toko`
- **Super Admin** → `/dashboard/super-admin`

---

## Registrasi Akun Baru

### URL Registrasi
```
http://localhost:8000/register
```

### Form Registrasi
1. **Nama Lengkap** - Masukkan nama lengkap Anda
2. **Email** - Email harus unik dan valid
3. **Password** - Minimal 8 karakter
4. **Konfirmasi Password** - Harus sama dengan password
5. **Tipe Akun** - Pilih salah satu:
   - **User (Pembeli)** - Untuk pengguna yang ingin browse produk/jasa UMKM
   - **Admin Toko (UMKM)** - Untuk pemilik UMKM yang ingin mempromosikan produk/jasa

**Catatan:** Super Admin tidak bisa dibuat melalui registrasi, hanya dapat dibuat melalui seeder atau oleh Super Admin lain.

---

## Login dengan Akun Default

Setelah menjalankan database seeder (`php artisan db:seed`), tersedia 3 akun default:

### 1. Super Admin
```
Email: superadmin@ulink.com
Password: password123
Dashboard: /dashboard/super-admin
```

**Hak Akses:**
- Mengelola semua pengguna (User, Admin Toko)
- Moderasi konten produk dan jasa
- Melihat statistik platform
- Mengelola kategori produk/jasa
- Verifikasi dan approve UMKM baru

### 2. Admin Toko (UMKM)
```
Email: admintoko@ulink.com
Password: password123
Dashboard: /dashboard/admin-toko
```

**Hak Akses:**
- Mengelola profil toko UMKM
- Menambah dan mengedit produk/jasa
- Melihat statistik toko
- Merespon review dari pelanggan

### 3. User (Pembeli)
```
Email: user@ulink.com
Password: password123
Dashboard: /dashboard
```

**Hak Akses:**
- Melihat produk dan jasa dari UMKM
- Mencari UMKM berdasarkan kategori
- Menyimpan UMKM favorit
- Memberikan review dan rating

---

## Dashboard Berdasarkan Role

### Dashboard User (`/dashboard`)
Menampilkan:
- Selamat datang dengan nama user
- Fitur yang tersedia untuk user
- 3 card: Produk UMKM, Jasa UMKM, Favorit Saya
- Akses untuk browse dan search produk/jasa

### Dashboard Admin Toko (`/dashboard/admin-toko`)
Menampilkan:
- Selamat datang dengan nama admin toko
- Fitur yang tersedia untuk admin toko
- 4 statistik card: Produk Saya, Jasa Saya, Pengunjung, Rating
- Tombol "Tambah Produk/Jasa"

### Dashboard Super Admin (`/dashboard/super-admin`)
Menampilkan:
- Selamat datang dengan nama super admin
- Fitur yang tersedia untuk super admin
- 4 statistik card: Total User, Total UMKM, Total Produk, UMKM Pending
- Tombol: Kelola User, Kelola UMKM, Moderasi Konten

---

## Role-Based Access Control (RBAC)

Sistem menggunakan middleware `role` untuk membatasi akses:

### Aturan Akses:
- User **TIDAK BISA** mengakses dashboard Admin Toko atau Super Admin
- Admin Toko **TIDAK BISA** mengakses dashboard Super Admin
- Super Admin **BISA** mengakses semua dashboard

### Error 403
Jika user mencoba mengakses halaman yang tidak sesuai role-nya, akan muncul error:
```
403 | Anda tidak memiliki akses ke halaman ini.
```

---

## Testing

### Menjalankan Test Suite
```bash
php artisan test --filter AuthenticationTest
```

### Test Cases yang Dicakup:
1. ✓ Login page dapat ditampilkan
2. ✓ Register page dapat ditampilkan
3. ✓ User dapat login dengan kredensial yang benar
4. ✓ Admin Toko diarahkan ke dashboard yang benar
5. ✓ Super Admin diarahkan ke dashboard yang benar
6. ✓ User tidak dapat login dengan password salah
7. ✓ User dapat melakukan registrasi
8. ✓ User tidak dapat mengakses dashboard Admin Toko
9. ✓ Admin Toko tidak dapat mengakses dashboard Super Admin
10. ✓ User dapat logout

**Hasil:** 10 passed (20 assertions)

---

## Troubleshooting

### 1. Error "CSRF token mismatch"
**Solusi:** Pastikan session driver diset dengan benar di `.env`:
```
SESSION_DRIVER=file
```

### 2. Error "could not translate host name"
**Solusi:** Pastikan database PostgreSQL dapat diakses. Untuk testing lokal, ubah di `.env`:
```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### 3. Error "Class 'CheckRole' not found"
**Solusi:** Jalankan:
```bash
composer dump-autoload
```

### 4. Error "Route not found"
**Solusi:** Clear cache:
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## Keamanan

### Password Hashing
Semua password di-hash menggunakan bcrypt dengan 12 rounds (sesuai konfigurasi di `.env`).

### Session Management
- Session menggunakan file driver
- Session lifetime: 120 menit
- Session regenerate setelah login/logout untuk mencegah session fixation

### CSRF Protection
Semua form POST dilindungi dengan CSRF token Laravel.

### Role Verification
Middleware `CheckRole` memverifikasi role user sebelum mengakses protected routes.

---

## Endpoint API

| Method | URL | Middleware | Role | Keterangan |
|--------|-----|------------|------|------------|
| GET | `/login` | guest | - | Tampilkan form login |
| POST | `/login` | guest | - | Proses login |
| GET | `/register` | guest | - | Tampilkan form registrasi |
| POST | `/register` | guest | - | Proses registrasi |
| POST | `/logout` | auth | all | Logout user |
| GET | `/dashboard` | auth, role:user | user | Dashboard user |
| GET | `/dashboard/admin-toko` | auth, role:admin_toko | admin_toko | Dashboard admin toko |
| GET | `/dashboard/super-admin` | auth, role:super_admin | super_admin | Dashboard super admin |

---

## Pengembangan Selanjutnya

Setelah sistem autentikasi selesai, berikut fitur yang dapat dikembangkan:

1. **Profile Management**
   - Edit profil user
   - Upload foto profil
   - Ubah password

2. **UMKM/Toko Management**
   - CRUD produk dan jasa
   - Upload foto produk
   - Kategori produk
   - Harga dan deskripsi

3. **Search & Filter**
   - Pencarian produk/jasa
   - Filter berdasarkan kategori
   - Filter berdasarkan lokasi
   - Sort berdasarkan rating/harga

4. **Review & Rating**
   - User dapat memberikan review
   - Rating bintang 1-5
   - Admin toko dapat membalas review

5. **Favorites**
   - User dapat menyimpan UMKM favorit
   - Notifikasi produk baru dari favorit

6. **Analytics**
   - Dashboard statistik untuk admin toko
   - Total views, rating, review
   - Grafik penjualan/views

7. **Notification System**
   - Notifikasi review baru
   - Notifikasi produk baru
   - Email notification
