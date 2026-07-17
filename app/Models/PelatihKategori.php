<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PelatihKategori extends Model
{
    use HasFactory;

    // Mengunci agar model ini membaca tabel pivot jembatan kita
    protected $table = 'pelatih_kategori';

    // Daftarkan kolom yang boleh diisi
    protected $fillable = [
        'pelatih_id',
        'kategori',
    ];

    // Relasi balik ke Pelatih (Opsional, tapi bagus untuk jaga-jaga)
    public function pelatih()
    {
        return $this->belongsTo(Pelatih::class, 'pelatih_id');
    }
}