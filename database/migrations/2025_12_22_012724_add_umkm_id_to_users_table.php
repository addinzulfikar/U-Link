<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS umkm_id bigint NULL');
            DB::statement('CREATE INDEX IF NOT EXISTS users_umkm_id_index ON users (umkm_id)');

            // If the column already existed, make sure existing values won't break FK creation.
            DB::statement(
                'UPDATE users u '
                .'SET umkm_id = NULL '
                .'WHERE umkm_id IS NOT NULL '
                .'AND NOT EXISTS (SELECT 1 FROM umkms x WHERE x.id = u.umkm_id)'
            );

            DB::unprepared(
                "DO $$\n"
                ."BEGIN\n"
                ."  IF to_regclass('umkms') IS NOT NULL AND NOT EXISTS (\n"
                ."    SELECT 1 FROM pg_constraint WHERE conname = 'users_umkm_id_foreign'\n"
                ."  ) THEN\n"
                ."    ALTER TABLE users\n"
                ."      ADD CONSTRAINT users_umkm_id_foreign\n"
                ."      FOREIGN KEY (umkm_id) REFERENCES umkms(id) ON DELETE SET NULL;\n"
                ."  END IF;\n"
                ."END\n"
                ."$$;"
            );

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'umkm_id')) {
                $table->foreignId('umkm_id')->nullable()->after('role');
            }
        });

        // Add FK separately to avoid partial failures on some drivers.
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('umkm_id')->references('id')->on('umkms')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_umkm_id_foreign');
            DB::statement('DROP INDEX IF EXISTS users_umkm_id_index');
            DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS umkm_id');
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['umkm_id']);
            $table->dropColumn('umkm_id');
        });
    }
};
