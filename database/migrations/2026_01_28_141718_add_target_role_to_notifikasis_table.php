<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            // Kita tambahkan kolom 'target_role'
            // default('all') artinya jika tidak diisi, otomatis dianggap untuk semua role
            // after('id') artinya posisi kolomnya ditaruh setelah kolom id
            $table->string('target_role')->default('all')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasis', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('target_role');
        });
    }
};