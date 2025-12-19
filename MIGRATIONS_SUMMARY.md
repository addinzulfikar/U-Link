# Database Migrations Summary

## Migrations yang Dibuat untuk U-LINK

Berikut adalah daftar lengkap migrations yang telah dibuat untuk platform U-LINK:

### 1. `0001_01_01_000000_create_users_table.php` (Existing - Modified)
**Table:** `users`
**Purpose:** Menyimpan data pengguna dengan sistem role
**Columns:**
- id (BIGSERIAL PRIMARY KEY)
- name (VARCHAR 255)
- email (VARCHAR 255, UNIQUE)
- password (VARCHAR 255)
- role (VARCHAR 50) - user, admin_toko, super_admin
- remember_token (VARCHAR 100)
- email_verified_at (TIMESTAMP)
- created_at, updated_at (TIMESTAMP)

**Constraints:**
- CHECK constraint untuk role values
- UNIQUE email

---

### 2. `0001_01_01_000003_create_umkms_table.php` (Existing)
**Table:** `umkms`
**Purpose:** Menyimpan data UMKM yang terdaftar
**Columns:**
- id (BIGSERIAL PRIMARY KEY)
- owner_user_id (BIGINT, FOREIGN KEY → users.id)
- name (VARCHAR 255)
- slug (VARCHAR 255, UNIQUE)
- description (TEXT)
- phone (VARCHAR 50)
- address (VARCHAR 255)
- city (VARCHAR 100)
- province (VARCHAR 100)
- status (VARCHAR 50) - pending, approved, rejected
- created_at, updated_at (TIMESTAMP)

**Constraints:**
- FOREIGN KEY: owner_user_id → users(id) ON DELETE CASCADE
- CHECK constraint: status IN ('pending', 'approved', 'rejected')
- UNIQUE: slug

**Indexes:**
- INDEX on (owner_user_id, status)

---

### 3. `0001_01_01_000004_create_products_table.php` (Existing)
**Table:** `products`
**Purpose:** Menyimpan produk dan jasa dari UMKM
**Columns:**
- id (BIGSERIAL PRIMARY KEY)
- umkm_id (BIGINT, FOREIGN KEY → umkms.id)
- type (VARCHAR 20) - product, service
- name (VARCHAR 255)
- slug (VARCHAR 255)
- description (TEXT)
- price (BIGINT)
- stock (INTEGER)
- is_active (BOOLEAN)
- created_at, updated_at (TIMESTAMP)

**Constraints:**
- FOREIGN KEY: umkm_id → umkms(id) ON DELETE CASCADE
- UNIQUE: (umkm_id, slug)
- CHECK constraint: type IN ('product', 'service')

**Indexes:**
- INDEX on (umkm_id, type, is_active)

---

### 4. `0001_01_01_000005_create_categories_table.php` ✨ NEW
**Table:** `categories`
**Purpose:** Kategori untuk produk dan jasa
**Columns:**
- id (BIGSERIAL PRIMARY KEY)
- name (VARCHAR 255)
- slug (VARCHAR 255, UNIQUE)
- description (TEXT)
- icon (VARCHAR 255)
- created_at, updated_at (TIMESTAMP)

**Constraints:**
- UNIQUE: slug

**Indexes:**
- INDEX on slug

---

### 5. `0001_01_01_000006_create_reviews_table.php` ✨ NEW
**Table:** `reviews`
**Purpose:** Review dan rating produk dari user
**Columns:**
- id (BIGSERIAL PRIMARY KEY)
- product_id (BIGINT, FOREIGN KEY → products.id)
- user_id (BIGINT, FOREIGN KEY → users.id)
- rating (INTEGER) - 1 to 5
- comment (TEXT)
- created_at, updated_at (TIMESTAMP)

**Constraints:**
- FOREIGN KEY: product_id → products(id) ON DELETE CASCADE
- FOREIGN KEY: user_id → users(id) ON DELETE CASCADE
- CHECK constraint: rating >= 1 AND rating <= 5
- UNIQUE: (user_id, product_id) - one review per user per product

**Indexes:**
- INDEX on product_id
- INDEX on user_id

---

### 6. `0001_01_01_000007_create_favorites_table.php` ✨ NEW
**Table:** `favorites`
**Purpose:** UMKM favorit user
**Columns:**
- id (BIGSERIAL PRIMARY KEY)
- user_id (BIGINT, FOREIGN KEY → users.id)
- umkm_id (BIGINT, FOREIGN KEY → umkms.id)
- created_at, updated_at (TIMESTAMP)

**Constraints:**
- FOREIGN KEY: user_id → users(id) ON DELETE CASCADE
- FOREIGN KEY: umkm_id → umkms(id) ON DELETE CASCADE
- UNIQUE: (user_id, umkm_id) - prevent duplicate favorites

**Indexes:**
- INDEX on user_id
- INDEX on umkm_id

---

### 7. `0001_01_01_000008_create_product_images_table.php` ✨ NEW
**Table:** `product_images`
**Purpose:** Multiple images untuk produk (future enhancement)
**Columns:**
- id (BIGSERIAL PRIMARY KEY)
- product_id (BIGINT, FOREIGN KEY → products.id)
- image_path (VARCHAR 500)
- is_primary (BOOLEAN)
- created_at, updated_at (TIMESTAMP)

**Constraints:**
- FOREIGN KEY: product_id → products(id) ON DELETE CASCADE

**Indexes:**
- INDEX on product_id

---

### 8. `0001_01_01_000009_add_logo_to_umkms_table.php` ✨ NEW
**Table:** Alter `umkms`
**Purpose:** Menambah kolom logo untuk UMKM
**Changes:**
- ADD COLUMN logo (VARCHAR 500) NULL

---

### 9. `0001_01_01_000010_add_category_and_image_to_products_table.php` ✨ NEW
**Table:** Alter `products`
**Purpose:** Menambah kolom category_id dan image
**Changes:**
- ADD COLUMN category_id (BIGINT) NULL
- ADD COLUMN image (VARCHAR 500) NULL
- ADD FOREIGN KEY category_id → categories(id) ON DELETE SET NULL
- CREATE INDEX on category_id

---

## Database Relationships

```
users
  └─ umkms (one-to-one: owner_user_id)
  └─ reviews (one-to-many)
  └─ favorites (one-to-many)

umkms
  ├─ products (one-to-many)
  ├─ favorites (one-to-many)
  └─ owner → users (many-to-one)

products
  ├─ reviews (one-to-many)
  ├─ product_images (one-to-many)
  ├─ umkm → umkms (many-to-one)
  └─ category → categories (many-to-one)

categories
  └─ products (one-to-many)

reviews
  ├─ product → products (many-to-one)
  └─ user → users (many-to-one)

favorites
  ├─ user → users (many-to-one)
  └─ umkm → umkms (many-to-one)

product_images
  └─ product → products (many-to-one)
```

## Total Tables

- **Total Tables Created**: 7
  - users (existing, modified)
  - umkms (existing)
  - products (existing, modified)
  - categories (NEW)
  - reviews (NEW)
  - favorites (NEW)
  - product_images (NEW)

## Total Migrations

- **Total Migration Files**: 10
  - 3 existing (users, umkms, products)
  - 4 new tables (categories, reviews, favorites, product_images)
  - 2 alter tables (add logo to umkms, add category_id & image to products)
  - 1 cache table (Laravel default)

## How to Run Migrations

```bash
# Run all migrations
php artisan migrate

# Run migrations with seeder
php artisan migrate --seed

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Refresh migrations (drop all and re-run)
php artisan migrate:fresh

# Refresh with seeder
php artisan migrate:fresh --seed
```

## Database Setup for PostgreSQL

1. Create database:
```sql
CREATE DATABASE ulink_db;
```

2. Configure `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ulink_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

3. Run migrations:
```bash
php artisan migrate
php artisan db:seed
```

## Notes

- Semua foreign keys menggunakan CASCADE delete untuk menjaga integritas data
- Indexes ditambahkan untuk meningkatkan performa query
- CHECK constraints memastikan data validity
- UNIQUE constraints mencegah duplikasi data
- Semua migrations menggunakan PostgreSQL native SQL untuk performa optimal

---

**Created for U-LINK Platform - UMKM Indonesia** 🇮🇩
