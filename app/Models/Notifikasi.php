<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi oleh sistem
    protected $fillable = [
        'user_id',     // Penerima notif
        'sender_id',   // 💡 TAMBAHAN: Catat ID pengirim (Admin/Owner)
        'target_role', // 💡 TAMBAHAN BARU: Izinkan pengisian target role (semua/atlet/pelatih)
        'judul',       // Judul
        'pesan',       // Isi pesan
        'tipe',        // 'tagihan', 'sukses', 'info'
        'is_read',     // Status sudah dibaca/belum
        'link'         // Link tujuan (opsional)
    ];

    // Relasi: Notifikasi ini milik User siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 💡 TAMBAHAN: Relasi untuk mencari tahu siapa pengirim notifikasi/broadcast ini
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}