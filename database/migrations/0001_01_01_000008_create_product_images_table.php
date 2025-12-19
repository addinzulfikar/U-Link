<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TABLE product_images (
                id BIGSERIAL PRIMARY KEY,
                product_id BIGINT NOT NULL,
                image_path VARCHAR(500) NOT NULL,
                is_primary BOOLEAN NOT NULL DEFAULT FALSE,
                created_at TIMESTAMP NULL,
                updated_at TIMESTAMP NULL,
                CONSTRAINT product_images_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            );

            CREATE INDEX product_images_product_id_index ON product_images(product_id);
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
