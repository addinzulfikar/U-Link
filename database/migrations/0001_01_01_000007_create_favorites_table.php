<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('umkm_id')->constrained('umkms')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['user_id', 'umkm_id'], 'favorites_user_umkm_unique');
            $table->index('user_id', 'favorites_user_id_index');
            $table->index('umkm_id', 'favorites_umkm_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
