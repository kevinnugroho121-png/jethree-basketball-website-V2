<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pelatihs', function (Blueprint $table) {
            $table->string('kategori_fokus')->nullable()->after('lisensi'); // Misal: KU-10, KU-12
            $table->string('gender_fokus')->nullable()->after('kategori_fokus'); // Misal: Putra, Putri
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelatih', function (Blueprint $table) {
            //
        });
    }
};
