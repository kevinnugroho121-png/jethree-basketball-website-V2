<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresAtlet extends Model
{
    use HasFactory;

    // KITA SESUAIKAN DENGAN DATABASE BARU (VERSI RAPOR)
    protected $fillable = [
        'atlet_id',
        'pelatih_id',
        'tanggal',      // Dulu 'tanggal_progres', sekarang kita pakai 'tanggal' biar singkat
        'teknik',       // Nilai Teknik (0-100)
        'fisik',        // Nilai Fisik (0-100)
        'mental',       // Nilai Mental (0-100)
        'taktik',       // Nilai Taktik (0-100)
        'catatan',
    ];

    // Relasi ke Atlet (Supaya nanti bisa memanggil nama atlet)
    public function atlet()
    {
        return $this->belongsTo(Atlet::class);
    }

    // Relasi ke Pelatih
    public function pelatih()
    {
        return $this->belongsTo(Pelatih::class);
    }
}