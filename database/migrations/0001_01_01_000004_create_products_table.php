<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TABLE products (
                id BIGSERIAL PRIMARY KEY,
                umkm_id BIGINT NOT NULL,
                type VARCHAR(20) NOT NULL DEFAULT 'product',
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description TEXT NULL,
                price BIGINT NOT NULL DEFAULT 0,
                stock INTEGER NULL,
                is_active BOOLEAN NOT NULL DEFAULT TRUE,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                CONSTRAINT products_umkm_id_foreign FOREIGN KEY (umkm_id) REFERENCES umkms(id) ON DELETE CASCADE,
                CONSTRAINT products_umkm_id_slug_unique UNIQUE (umkm_id, slug),
                CONSTRAINT products_type_check CHECK (type IN ('product', 'service'))
            );

            CREATE INDEX products_umkm_id_type_is_active_index ON products(umkm_id, type, is_active);
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};