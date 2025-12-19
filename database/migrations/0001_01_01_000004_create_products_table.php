<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained('umkms')->onDelete('cascade');
            $table->string('type', 20)->default('product');
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->text('description')->nullable();
            $table->bigInteger('price')->default(0);
            $table->integer('stock')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['umkm_id', 'slug'], 'products_umkm_id_slug_unique');
            $table->index(['umkm_id', 'type', 'is_active'], 'products_umkm_id_type_is_active_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
