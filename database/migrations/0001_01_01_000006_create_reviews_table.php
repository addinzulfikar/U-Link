<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TABLE reviews (
                id BIGSERIAL PRIMARY KEY,
                product_id BIGINT NOT NULL,
                user_id BIGINT NOT NULL,
                rating INTEGER NOT NULL,
                comment TEXT NULL,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                CONSTRAINT reviews_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                CONSTRAINT reviews_user_id_foreign FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                CONSTRAINT reviews_rating_check CHECK (rating >= 1 AND rating <= 5),
                CONSTRAINT reviews_user_product_unique UNIQUE (user_id, product_id)
            );

            CREATE INDEX reviews_product_id_index ON reviews(product_id);
            CREATE INDEX reviews_user_id_index ON reviews(user_id);
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
