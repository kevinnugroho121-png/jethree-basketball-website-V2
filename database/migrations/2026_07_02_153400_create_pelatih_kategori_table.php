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
        Schema::create('pelatih_kategori', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel pelatihs, jika pelatih dihapus, jembatan ini otomatis ikut terhapus (cascade)
            $table->foreignId('pelatih_id')->constrained('pelatihs')->onDelete('cascade');
            // Menyimpan nama kategori (contoh: 'KU-10 Cowok', 'KU-12')
            $table->string('kategori'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatih_kategori');
    }
};
