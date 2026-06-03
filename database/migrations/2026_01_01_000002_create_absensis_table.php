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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Jadwal & Atlet
            $table->foreignId('jadwal_id')->constrained('jadwals')->onDelete('cascade');
            $table->foreignId('atlet_id')->constrained('atlets')->onDelete('cascade');
            
            $table->date('tanggal_latihan');
            $table->string('status');           // Hadir, Sakit, Izin, Alpha
            $table->text('catatan')->nullable(); // Boleh kosong (nullable)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};