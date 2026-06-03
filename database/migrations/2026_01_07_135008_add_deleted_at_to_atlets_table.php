<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * (Menambahkan kolom soft delete saat migrate dijalankan)
     */
    public function up(): void
    {
        Schema::table('atlets', function (Blueprint $table) {
            // Ini akan membuat kolom 'deleted_at' (nullable)
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     * (Menghapus kolom soft delete jika migrate di-rollback/dibatalkan)
     */
    public function down(): void
    {
        Schema::table('atlets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};