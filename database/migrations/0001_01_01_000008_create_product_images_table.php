<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('image_path', 500);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->index('product_id', 'product_images_product_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
