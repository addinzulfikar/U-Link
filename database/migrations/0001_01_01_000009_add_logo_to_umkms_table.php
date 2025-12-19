<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            ALTER TABLE umkms 
            ADD COLUMN IF NOT EXISTS logo VARCHAR(500) NULL;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
            ALTER TABLE umkms 
            DROP COLUMN IF EXISTS logo;
        SQL);
    }
};
