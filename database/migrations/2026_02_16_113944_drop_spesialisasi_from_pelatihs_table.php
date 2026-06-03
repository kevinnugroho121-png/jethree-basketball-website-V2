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
            // Kita hapus kolom spesialisasi
            $table->dropColumn('spesialisasi');
        });
    }

    public function down()
    {
        Schema::table('pelatihs', function (Blueprint $table) {
            // Buat jaga-jaga kalau mau rollback
            $table->string('spesialisasi')->nullable();
        });
    }
};
