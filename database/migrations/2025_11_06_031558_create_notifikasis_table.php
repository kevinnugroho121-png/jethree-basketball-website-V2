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
        // Pastikan tabel dibuat baru jika belum ada
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            
            // PENTING: user_id ini adalah PENERIMA Notifikasi (Atlet/Pelatih)
            // Relasi ke tabel users, jika user dihapus notif ikut terhapus (cascade)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('judul');      // Contoh: "Tagihan Baru", "Jadwal Latihan"
            $table->text('pesan');        // Isi pesan lengkap
            
            // KATEGORI: 'tagihan', 'pembayaran', 'jadwal', 'info'
            // Kita pakai nama 'kategori' biar sinkron dengan kodingan Controller nanti
            $table->string('kategori')->default('info'); 
            
            // STATUS BACA: Default false (belum dibaca)
            // Nanti di HP: Kalau false = Tebal/Warna, Kalau true = Biasa
            $table->boolean('is_read')->default(false); 
            
            // LINK (Opsional): 
            // Misal diklik arahnya ke halaman bayar / halaman jadwal
            $table->string('link')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
    }
};