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
        Schema::table('jadwals', function (Blueprint $table) {
            // KITA TAMBAHKAN PENGECEKAN
            // Hanya tambahkan kolom jika kolom 'materi' BELUM ada
            if (!Schema::hasColumn('jadwals', 'materi')) {
                $table->text('materi')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Hanya hapus jika kolomnya ada
            if (Schema::hasColumn('jadwals', 'materi')) {
                $table->dropColumn('materi');
            }
        });
    }
};