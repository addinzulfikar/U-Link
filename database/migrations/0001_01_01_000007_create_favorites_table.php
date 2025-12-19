<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TABLE favorites (
                id BIGSERIAL PRIMARY KEY,
                user_id BIGINT NOT NULL,
                umkm_id BIGINT NOT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                CONSTRAINT favorites_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT favorites_umkm_id_foreign FOREIGN KEY (umkm_id) REFERENCES umkms(id) ON DELETE CASCADE,
                CONSTRAINT favorites_user_umkm_unique UNIQUE (user_id, umkm_id)
            );

            CREATE INDEX favorites_user_id_index ON favorites(user_id);
            CREATE INDEX favorites_umkm_id_index ON favorites(umkm_id);
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
