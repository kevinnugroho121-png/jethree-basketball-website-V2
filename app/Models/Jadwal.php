<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;
    
    // Kolom yang boleh diisi oleh Admin (Mass Assignment)
    protected $fillable = [
        'tanggal',      // Pengganti 'hari'
        'kategori',
        'pelatih_id',   // Wajib ada biar bisa disimpan
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'status',       // Tambahan status (Aktif/Dibatalkan)
        'materi',       
        
        // 💡 TAMBAHKAN BARIS DI BAWAH INI:
        'status_rilis',
        'link_youtube',
        'review_latihan',
        'is_takeover',
        'pelatih_asli_id',
    ];

    // Memberitahu Laravel bahwa kolom ini adalah format Tanggal
    // Ini berguna nanti di View biar bisa diformat 'd M Y' dengan mudah
    protected $casts = [
        'tanggal' => 'date',
    ];

    // Relasi ke Model Pelatih
    // Artinya: Jadwal ini milik siapa? Milik Pelatih X.
    public function pelatih()
    {
        return $this->belongsTo(Pelatih::class, 'pelatih_id');
    }

    // Relasi ke Absensi
    // Artinya: Satu jadwal latihan punya BANYAK data absensi
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}