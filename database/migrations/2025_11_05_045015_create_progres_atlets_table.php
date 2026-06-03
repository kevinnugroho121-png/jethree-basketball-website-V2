<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel lama kalau ada, biar bersih
        Schema::dropIfExists('progres_atlets');

        Schema::create('progres_atlets', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('atlet_id')->constrained('atlets')->onDelete('cascade');
            // KITA HUBUNGKAN KE PELATIHS (Bukan Users lagi)
            $table->foreignId('pelatih_id')->constrained('pelatihs')->onDelete('cascade');
            
            $table->date('tanggal');
            
            // ASPEK PENILAIAN (RAPOR)
            $table->integer('teknik'); // 0-100
            $table->integer('fisik');
            $table->integer('mental');
            $table->integer('taktik');
            
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_atlets');
    }
};