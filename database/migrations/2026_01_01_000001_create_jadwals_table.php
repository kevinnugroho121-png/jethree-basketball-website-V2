<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            
            // Relasi Pelatih
            $table->foreignId('pelatih_id')->constrained('pelatihs')->onDelete('cascade'); 
            
            // KITA PAKAI TANGGAL (Bukan Hari)
            $table->date('tanggal');      // <--- INI KUNCINYA
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('kategori');   
            $table->string('lokasi');     
            $table->string('status')->default('Belum Selesai'); // Tambahan status jadwal
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};