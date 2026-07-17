<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelatih extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pelatihs'; // Pastikan nama tabel benar

    protected $fillable = [
        'user_id', 
        'nama_lengkap',
        'tempat_lahir',  // <--- TAMBAHAN PENTING
        'tanggal_lahir',
        'no_hp',
        'lisensi',       // <--- WAJIB ADA (Biar data lisensi tersimpan)
        'alamat',        // <--- WAJIB ADA (Biar data alamat tersimpan)
        'status',
        'foto_profil',
        'kategori_fokus', // ⚡ BARU: Izinkan simpan kategori ke database
        'gender_fokus', 
    ];

    // Relasi ke User (Supaya bisa ambil Email login)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // =========================================================================
    // [BARU] RELASI KE TABEL JEMBATAN KATEGORI (Untuk Pembatasan Akses Absensi KU)
    // =========================================================================
    public function kategoris()
    {
        // Menghubungkan Model Pelatih ke data Kategori yang diampunya
        return $this->hasMany(\App\Models\PelatihKategori::class, 'pelatih_id');
    }
}