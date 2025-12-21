<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * PostgreSQL in this environment is intermittently failing DDL inside
     * transactions (SQLSTATE[25P02]). Run this migration outside a transaction.
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained('umkms')->onDelete('cascade');
            $table->foreignId('spreadsheet_upload_id')->nullable()->constrained('spreadsheet_uploads')->onDelete('set null');
            $table->date('transaction_date');
            $table->enum('transaction_type', ['Pemasukan', 'Pengeluaran']);
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('source_file')->nullable(); // Track which file this came from
            $table->integer('row_number')->nullable(); // Track which row in the spreadsheet
            $table->json('validation_errors')->nullable(); // Store any validation errors
            $table->timestamps();
            
            // Indexes for performance
            $table->index('umkm_id');
            $table->index('transaction_date');
            $table->index('transaction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
