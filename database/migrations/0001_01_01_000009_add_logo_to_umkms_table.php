<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('umkms', 'logo')) {
            Schema::table('umkms', function (Blueprint $table) {
                $table->string('logo', 500)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('umkms', 'logo')) {
            Schema::table('umkms', function (Blueprint $table) {
                $table->dropColumn('logo');
            });
        }
    }
};
