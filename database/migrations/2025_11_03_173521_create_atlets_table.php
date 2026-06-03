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
        Schema::create('atlets', function (Blueprint $table) {
            $table->id();
            
            // --- 1. RELASI KE USER ---
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); 

            // --- 2. BIODATA ---
            $table->string('nama_lengkap');
            $table->string('nama_panggilan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->text('alamat')->nullable();
            
            // [PENTING] Sesuai Logic Mas Kevin (no_hp_atlet)
            $table->string('no_hp_atlet')->nullable(); 
            
            // --- 3. DATA SEKOLAH (DIPISAH SESUAI LOGIKA MAS KEVIN) ---
            $table->string('jenjang_sekolah')->nullable(); // SD, SMP, SMA
            $table->string('nama_sekolah')->nullable();    // SMAN 2 Kediri, dll

            // --- 4. DATA AKADEMI ---
            $table->string('kategori'); // Sesuai Logic Mas Kevin (kategori)
            $table->string('posisi')->nullable(); 
            $table->string('status')->default('Aktif'); 
            
            // [TAMBAHAN WAJIB] Karena di Controller ada 'tanggal_gabung'
            $table->date('tanggal_gabung')->nullable(); 

            // --- 5. ORANG TUA ---
            // [PENTING] Sesuai Logic Mas Kevin (nama_orang_tua & no_hp_orang_tua)
            $table->string('nama_orang_tua')->nullable();
            $table->string('no_hp_orang_tua')->nullable();

            // [TAMBAHAN WAJIB] Karena di Controller ada upload foto
            $table->string('foto_profil')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atlets');
    }
};