<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'atlet_id',
        'jenis_tagihan', // Default SPP
        'bulan',         // Angka (1-12)
        'tahun',         // Angka (2026)
        'nominal',
        'tanggal_tagihan',
        'status',
        'tanggal_lunas',
        'metode_pembayaran',
        'bukti_pembayaran', // <--- Wajib Upload
        'keterangan',
    ];

    public function atlet()
    {
        return $this->belongsTo(Atlet::class, 'atlet_id');
    }
    
    // Aksesor Pintar: Biar di tampilan munculnya "SPP Januari 2026" otomatis
    // Cara panggil di blade: {{ $tagihan->judul_tagihan }}
    public function getJudulTagihanAttribute()
    {
        $namaBulan = \Carbon\Carbon::create()->month($this->bulan)->translatedFormat('F');
        return "SPP $namaBulan $this->tahun";
    }
}