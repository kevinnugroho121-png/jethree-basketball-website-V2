<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // 1. Hapus foreign key yang lama (cascade)
            $table->dropForeign(['pelatih_id']);

            // 2. Ubah kolom pelatih_id agar boleh kosong (nullable) 
            // dan pasang aturan baru (set null)
            $table->foreignId('pelatih_id')
                ->nullable()
                ->change()
                ->constrained('pelatihs')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['pelatih_id']);
            $table->foreignId('pelatih_id')
                ->nullable(false)
                ->change()
                ->constrained('pelatihs')
                ->onDelete('cascade');
        });
    }
};