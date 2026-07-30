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
        Schema::table('jadwals', function (Blueprint $table) {
            // ⚡ Alur 2: Kolom video referensi & Status perilisan digital (default Draft)
            $table->string('link_youtube')->nullable()->after('materi');
            $table->enum('status_rilis', ['Draft', 'Rilis'])->default('Draft')->after('lokasi');

            // ⚡ Alur 3: Penampung rangkuman review wajib Coach di Hari-H
            $table->text('review_latihan')->nullable()->after('link_youtube');

            // ⚡ Alur 4: Kunci penanda intervensi Owner (Takeover)
            $table->boolean('is_takeover')->default(false)->after('status_rilis');
            $table->unsignedBigInteger('pelatih_asli_id')->nullable()->after('pelatih_id');

            // Set foreign key pengaman untuk mencatat pelatih asli
            $table->foreign('pelatih_asli_id')->references('id')->on('pelatihs')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['pelatih_asli_id']);
            $table->dropColumn(['link_youtube', 'status_rilis', 'review_latihan', 'is_takeover', 'pelatih_asli_id']);
        });
    }
};
