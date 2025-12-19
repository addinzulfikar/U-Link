<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            ALTER TABLE products 
            ADD COLUMN IF NOT EXISTS category_id BIGINT NULL,
            ADD COLUMN IF NOT EXISTS image VARCHAR(500) NULL;

            ALTER TABLE products
            DROP CONSTRAINT IF EXISTS products_category_id_foreign;

            ALTER TABLE products
            ADD CONSTRAINT products_category_id_foreign 
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

            CREATE INDEX IF NOT EXISTS products_category_id_index ON products(category_id);
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            ALTER TABLE products 
            DROP CONSTRAINT IF EXISTS products_category_id_foreign;
            
            DROP INDEX IF EXISTS products_category_id_index;
            
            ALTER TABLE products 
            DROP COLUMN IF EXISTS category_id,
            DROP COLUMN IF EXISTS image;
        SQL);
    }
};
