<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Hapus foreign key yang punya aturan cascade
            $table->dropForeign(['pelatih_id']);

            // Pasang aturan baru: Bisa kosong (nullable) dan jadi NULL kalau pelatih dihapus
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