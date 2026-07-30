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
        Schema::create('histori_takeovers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_id');
            $table->unsignedBigInteger('owner_id'); // Mengikat ke users(id) milik Owner
            $table->unsignedBigInteger('pelatih_id'); // Mengikat ke pelatihs(id) pelatih asli
            $table->date('tanggal_takeover');
            $table->timestamps();

            // Deklarasi Kunci Hubungan Antar Tabel
            $table->foreign('jadwal_id')->references('id')->on('jadwals')->onDelete('cascade');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pelatih_id')->references('id')->on('pelatihs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_takeovers');
    }
};
