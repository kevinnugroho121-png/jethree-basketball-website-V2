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
        Schema::create('master_materis', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Contoh: KU-10, KU-12
            $table->integer('pertemuan_ke'); // Contoh: 1, 2, 3 (biar materinya urut)
            $table->string('judul_materi'); // Contoh: "Ball Handling Dasar"
            $table->text('deskripsi')->nullable(); // Opsional, buat catetan pelatih
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_materis');
    }
};
