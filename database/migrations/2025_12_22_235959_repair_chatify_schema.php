<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $withinTransaction = false;

    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS active_status boolean NOT NULL DEFAULT false');
            DB::statement("ALTER TABLE users ADD COLUMN IF NOT EXISTS avatar varchar(255) NOT NULL DEFAULT 'avatar.png'");
            DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS dark_mode boolean NOT NULL DEFAULT false');
            DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS messenger_color varchar(255) NULL');

            DB::statement(
                'CREATE TABLE IF NOT EXISTS ch_messages ('
                .'id uuid PRIMARY KEY, '
                .'from_id bigint NOT NULL, '
                .'to_id bigint NOT NULL, '
                .'body varchar(5000) NULL, '
                .'attachment varchar(255) NULL, '
                .'seen boolean NOT NULL DEFAULT false, '
                .'created_at timestamp(0) without time zone NULL, '
                .'updated_at timestamp(0) without time zone NULL'
                .')'
            );

            DB::statement(
                'CREATE TABLE IF NOT EXISTS ch_favorites ('
                .'id uuid PRIMARY KEY, '
                .'user_id bigint NOT NULL, '
                .'favorite_id bigint NOT NULL, '
                .'created_at timestamp(0) without time zone NULL, '
                .'updated_at timestamp(0) without time zone NULL'
                .')'
            );

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'active_status')) {
                $table->boolean('active_status')->default(false);
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->default('avatar.png');
            }
            if (!Schema::hasColumn('users', 'dark_mode')) {
                $table->boolean('dark_mode')->default(false);
            }
            if (!Schema::hasColumn('users', 'messenger_color')) {
                $table->string('messenger_color')->nullable();
            }
        });

        if (!Schema::hasTable('ch_messages')) {
            Schema::create('ch_messages', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->bigInteger('from_id');
                $table->bigInteger('to_id');
                $table->string('body', 5000)->nullable();
                $table->string('attachment')->nullable();
                $table->boolean('seen')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ch_favorites')) {
            Schema::create('ch_favorites', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->bigInteger('user_id');
                $table->bigInteger('favorite_id');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS active_status');
            DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS avatar');
            DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS dark_mode');
            DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS messenger_color');

            DB::statement('DROP TABLE IF EXISTS ch_favorites');
            DB::statement('DROP TABLE IF EXISTS ch_messages');

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'active_status')) {
                $table->dropColumn('active_status');
            }
            if (Schema::hasColumn('users', 'avatar')) {
                $table->dropColumn('avatar');
            }
            if (Schema::hasColumn('users', 'dark_mode')) {
                $table->dropColumn('dark_mode');
            }
            if (Schema::hasColumn('users', 'messenger_color')) {
                $table->dropColumn('messenger_color');
            }
        });

        Schema::dropIfExists('ch_favorites');
        Schema::dropIfExists('ch_messages');
    }
};
