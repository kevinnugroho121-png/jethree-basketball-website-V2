<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiNotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // PERBAIKAN LOGIKA: HANYA AMBIL MILIK SENDIRI ATAU GLOBAL
        // [PERUBAHAN]: Kita ganti get() menjadi paginate(10)
        $notif = DB::table('notifikasis')
            ->where(function($query) use ($user) {
                // 1. Ambil yang user_id nya SAMA PERSIS dengan login
                $query->where('user_id', $user->id)
                // 2. ATAU ambil yang user_id nya NULL (Pengumuman Umum Global)
                      ->orWhereNull('user_id');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Menampilkan maksimal 10 pengumuman per halaman

        return response()->json([
            'success' => true,
            'data'    => $notif, // Mengandung data utama dan metadata paginasi
        ]);
    }
}