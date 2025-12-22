<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateChatifyFavoritesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
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

        if (Schema::hasTable('ch_favorites')) {
            return;
        }

        Schema::create('ch_favorites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->bigInteger('user_id');
            $table->bigInteger('favorite_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ch_favorites');
    }
}
