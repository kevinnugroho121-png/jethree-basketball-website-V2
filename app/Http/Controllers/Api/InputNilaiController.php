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
            // CARI ID PELATIH YANG VALID (Otomatis)
            // Ambil data pelatih pertama yang ditemukan di database
            $pelatih = Pelatih::first();

            // Jaga-jaga kalau tabel pelatih kosong melompong
            if (!$pelatih) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Error: Data Pelatih Kosong di Database. Tambahkan 1 pelatih dulu.'
                ], 500);
            }

            // SIMPAN DATA
            $nilai = ProgresAtlet::create([
                'pelatih_id' => $pelatih->id,    // <--- PAKAI ID ASLI (Bukan tebakan angka 1)
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