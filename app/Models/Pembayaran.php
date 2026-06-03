<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    // TAMBAHKAN INI
    protected $fillable = [
        'tagihan_id',
        'atlet_id',
        'metode',
        'bukti_pembayaran',
        'tanggal_pembayaran',
        'jumlah_dibayar',
    ];
}