<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi oleh sistem
    protected $fillable = [
        'user_id',  // Penerima notif
        'judul',    // Judul
        'pesan',    // Isi pesan
        'tipe',     // 'tagihan', 'sukses', 'info'
        'is_read',  // Status sudah dibaca/belum
        'link'      // Link tujuan (opsional)
    ];

    // Relasi: Notifikasi ini milik User siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}