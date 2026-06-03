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
    ];

    // Relasi ke User (Supaya bisa ambil Email login)
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}