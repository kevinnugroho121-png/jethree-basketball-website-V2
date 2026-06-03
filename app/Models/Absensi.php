<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    // 1. DEFINISI NAMA TABEL (PENTING)
    // Pastikan ini sesuai dengan nama tabel di database kamu.
    // Biasanya Laravel menamainya 'absensis' (jamak), tapi kalau kamu buatnya 'absensi', ubah di sini.
    protected $table = 'absensis'; 

    // 2. IZINKAN SEMUA KOLOM DIISI (MASS ASSIGNMENT)
    // Ini aman karena kita mengisi datanya lewat Controller yang sudah terstruktur.
    protected $guarded = [];

    // 3. RELASI KE JADWAL
    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    // 4. RELASI KE ATLET
    public function atlet()
    {
        return $this->belongsTo(Atlet::class);
    }
}