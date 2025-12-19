<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id'], 'reviews_user_product_unique');
            $table->index('product_id', 'reviews_product_id_index');
            $table->index('user_id', 'reviews_user_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
