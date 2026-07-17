<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Atlet;
use App\Models\ProgresAtlet; 
use App\Models\Pelatih; // <--- TAMBAHAN PENTING (Biar bisa cari ID Pelatih)

class InputNilaiController extends Controller
{
    // 1. AMBIL DAFTAR ATLET
    public function getAtlets()
    {
        $atlets = Atlet::orderBy('nama_lengkap', 'asc')->get();

        return response()->json([
            'success' => true,
            'data'    => $atlets
        ]);
    }

    // 2. SIMPAN NILAI
    public function store(Request $request)
    {
        $request->validate([
            'atlet_id' => 'required',
            'teknik'   => 'required|numeric',
            'fisik'    => 'required|numeric',
            'mental'   => 'required|numeric',
            'catatan'  => 'nullable|string',
        ]);

        try {

        
            // ==================== KODE YANG BENAR (GANTI DENGAN INI) ====================
            // CARI ID PELATIH YANG VALID (Dinamis Mengikuti Dropdown Flutter / Session Login)
            $user = $request->user();
            $pelatihId = null;

            // 1. Jika ada kiriman 'pelatih_id' dari dropdown Flutter (Akses khusus Coach Irul)
            if ($request->has('pelatih_id') && $request->pelatih_id != null) {
                $pelatihId = $request->pelatih_id;
            } 
            // 2. Jika pelatih biasa yang input (Dropdown tersembunyi), otomatis ambil ID-nya dari session login
            else {
                $pelatihAsli = \App\Models\Pelatih::where('user_id', $user->id)->first();
                $pelatihId = $pelatihAsli ? $pelatihAsli->id : null;
            }

            // Jaga-jaga kalau pelatih tidak ditemukan di sistem
            if (!$pelatihId) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error: Profil Pelatih tidak ditemukan atau belum dipilih.'
                ], 404);
            }

            // SIMPAN DATA
            $nilai = ProgresAtlet::create([
                'pelatih_id' => $pelatihId,    // <--- SEKARANG SUDAH 100% DINAMIS DI LAPANGAN
                
                
                
                'atlet_id'   => $request->atlet_id,
                
                'teknik'     => $request->teknik,
                'fisik'      => $request->fisik,
                'mental'     => $request->mental,
                'taktik'     => $request->teknik, // Isi otomatis samakan dengan teknik
                
                'catatan'    => $request->catatan,
                'tanggal'    => now(),                
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Nilai tersimpan!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}