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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans')->onDelete('cascade');
            $table->foreignId('atlet_id')->constrained('atlets')->onDelete('cascade');
            $table->string('metode'); // Misal: Manual Transfer, Qris
            $table->string('bukti_pembayaran')->nullable(); // Path/link ke file gambar
            $table->timestamp('tanggal_pembayaran');
            $table->integer('jumlah_dibayar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
