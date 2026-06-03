<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Atlet
            $table->foreignId('atlet_id')->constrained('atlets')->onDelete('cascade');
            
            // PENGGANTI JENIS TAGIHAN MANUAL
            // Kita pecah jadi Bulan dan Tahun agar bisa diurutkan history-nya
            $table->string('jenis_tagihan')->default('SPP'); // Default SPP
            $table->integer('bulan'); // 1 = Januari, 2 = Februari, dst
            $table->integer('tahun'); // 2025, 2026
            
            $table->decimal('nominal', 12, 0); 
            $table->date('tanggal_tagihan'); 
            
            // STATUS & PEMBAYARAN
            $table->enum('status', ['Belum Lunas', 'Lunas'])->default('Belum Lunas');
            $table->date('tanggal_lunas')->nullable();
            
            // METODE & BUKTI (PERMINTAAN DOSEN)
            $table->string('metode_pembayaran')->nullable(); // Cash / Transfer
            $table->string('bukti_pembayaran')->nullable();  // Path Foto Bukti (Struk/Uang)
            
            $table->text('keterangan')->nullable(); // Catatan opsional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};