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
        Schema::create('pelatihs', function (Blueprint $table) {
            $table->id();
            
            // 1. RELASI KE AKUN LOGIN (WAJIB ADA)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // 2. Data Pribadi Utama
            $table->string('nama_lengkap');
            $table->string('no_hp')->nullable();

            // [PENTING] Kolom ini WAJIB ada karena dipanggil di Seeder
            $table->string('tempat_lahir')->nullable(); 
            $table->date('tanggal_lahir')->nullable();
            $table->string('lisensi')->nullable(); // Contoh: 'Lisensi B', 'Lisensi A'

            // 3. Data Tambahan (Opsional)
            $table->string('spesialisasi')->nullable(); 
            $table->text('alamat')->nullable(); 
            
            // 4. Status & Foto
            // Foto profil nanti ditambahkan lewat migrasi lain (add_foto_profil), 
            // tapi kalau mau ditaruh sini sekalian biar aman juga boleh (nullable).
            $table->string('foto_profil')->nullable();
            $table->string('status')->default('Aktif'); 
            
            $table->timestamps();
            
            // Supaya support Soft Delete (Tong Sampah) kalau nanti diperlukan
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihs');
    }
};