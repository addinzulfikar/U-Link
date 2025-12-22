<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateChatifyMessagesTable extends Migration
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
            return;
        }

        if (Schema::hasTable('ch_messages')) {
            return;
        }

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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ch_messages');
    }
}
