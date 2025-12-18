<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TABLE umkms (
                id BIGSERIAL PRIMARY KEY,
                owner_user_id BIGINT NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description TEXT NULL,
                phone VARCHAR(50) NULL,
                address VARCHAR(255) NULL,
                city VARCHAR(100) NULL,
                province VARCHAR(100) NULL,
                status VARCHAR(50) NOT NULL DEFAULT 'pending',
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                CONSTRAINT umkms_slug_unique UNIQUE (slug),
                CONSTRAINT umkms_owner_user_id_foreign FOREIGN KEY (owner_user_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT umkms_status_check CHECK (status IN ('pending', 'approved', 'rejected'))
            );

            CREATE INDEX umkms_owner_user_id_status_index ON umkms(owner_user_id, status);
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};