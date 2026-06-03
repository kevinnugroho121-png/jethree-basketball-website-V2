<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Atlet;
use App\Models\ProgresAtlet; 

class RaporController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil User yang login
        $user = $request->user();

        // 2. Cari data Atlet berdasarkan User ID
        $atlet = Atlet::where('user_id', $user->id)->first();

        if (!$atlet) {
            return response()->json([
                'success' => false,
                'message' => 'Data atlet tidak ditemukan.',
                'data'    => []
            ], 404);
        }

        // 3. Ambil Data Nilai / Progres milik atlet ini
        // Kita urutkan dari yang terbaru (tanggal desc)
        $rapors = ProgresAtlet::where('atlet_id', $atlet->id)
                              ->orderBy('tanggal', 'desc')
                              ->get();

        // [REVISI DOSEN] 4. Konversi Angka ke Huruf (A, B, C, D, E)
        // Kita tambahkan kolom baru (teknik_huruf, fisik_huruf, dll) ke dalam data JSON
        $rapors->transform(function($item) {
            $item->teknik_huruf = $this->konversiHuruf($item->teknik);
            $item->fisik_huruf  = $this->konversiHuruf($item->fisik);
            $item->mental_huruf = $this->konversiHuruf($item->mental);
            
            // Jaga-jaga kalau kolom taktik null/kosong, kita anggap 0
            $item->taktik_huruf = $this->konversiHuruf($item->taktik ?? 0);
            
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'List Data Rapor & Nilai',
            'data'    => $rapors
        ]);
    }

    // === FUNGSI TAMBAHAN (PRIVATE) ===
    // Fungsi ini mengubah angka menjadi Huruf (Sesuai Standar Rapor)
    private function konversiHuruf($angka) {
        // Pastikan angka tidak null
        $nilai = intval($angka); 

        if ($nilai >= 85) return 'A'; // Sangat Baik
        if ($nilai >= 75) return 'B'; // Baik
        if ($nilai >= 60) return 'C'; // Cukup
        if ($nilai >= 40) return 'D'; // Kurang
        return 'E';                   // Sangat Kurang
    }
}