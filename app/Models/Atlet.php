<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon; // <--- 1. PENTING (Agar bisa hitung umur)

class Atlet extends Model
{
    // 2. Menggunakan fitur SoftDeletes
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Data User Login
        'user_id',

        // Biodata
        'nama_lengkap',
        'nama_panggilan',
        'tempat_lahir',
        'tanggal_lahir', 
        'jenis_kelamin',
        'foto_profil',
        'alamat',
        'no_hp_atlet',
        
        // DATA SEKOLAH
        'jenjang_sekolah',
        'nama_sekolah',

        // Data Akademi
        'kategori', 
        'posisi',
        'status',

        // Orang Tua
        'nama_orang_tua',
        'no_hp_orang_tua',
    ];

    // ==========================================================
    // FITUR BARU: HITUNG KATEGORI OTOMATIS (Tanpa Senior)
    // ==========================================================
    // Cara panggil di View nanti: {{ $atlet->kategori_hitung }}
    public function getKategoriHitungAttribute()
    {
        // Jika tanggal lahir kosong, kembalikan tanda strip
        if (!$this->tanggal_lahir) {
            return '-';
        }

        // Hitung umur atlet saat ini juga
        $usia = Carbon::parse($this->tanggal_lahir)->age;

        // Logika penentuan Kelompok Umur (KU)
        // Maksimal KU-18, tidak ada Senior.
        
        if ($usia <= 10) return 'KU-10 Mix';
        if ($usia <= 12) return 'KU-12';
        if ($usia <= 14) return 'KU-14';
        if ($usia <= 16) return 'KU-16';
        
        // Usia 17, 18, 19, 20, dst akan masuk ke sini:
        return 'KU-18'; 
    }

    // ==========================================================

    // Relasi ke Tagihan
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'atlet_id');
    }

    // Relasi ke Absensi
    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    // ==========================================================
    // TAMBAHAN BARU: RELASI KE USER (Untuk ambil Email)
    // ==========================================================
    public function user()
    {
        // Menghubungkan kolom user_id di tabel atlets ke id di tabel users
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}