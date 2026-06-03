<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Atlet;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    public function index($jadwal_id)
    {
        // 1. Cari jadwal yang diklik berdasarkan ID
        $jadwal = Jadwal::findOrFail($jadwal_id);

        // 2. Cari semua atlet yang kategorinya SAMA dengan kategori jadwal
        //    (Misal: cari semua atlet 'SMP' jika jadwalnya 'SMP')
        $atlets = Atlet::where('kategori', $jadwal->kategori)
                       ->where('status', 'Aktif') // Hanya ambil atlet yang aktif
                       ->get();

        // 3. Tampilkan view 'absensi.index' dan kirim data jadwal & atlet
        return view('pelatih.absensi.index', compact('jadwal', 'atlets'));
    }

    // Fungsi store biarkan kosong dulu
    public function store(Request $request)
    {
        // 1. Ambil data dari form
        $dataAbsensi = $request->input('absensi'); // Ini adalah array [atlet_id => status]
        $jadwal_id = $request->input('jadwal_id');
        $tanggal_absensi = $request->input('tanggal_absensi');
        $pelatih_id = Auth::id(); // Ambil ID pelatih yang sedang login
        
        // 2. Loop setiap data absensi yang dikirim
        foreach ($dataAbsensi as $atlet_id => $status_kehadiran) {

        // 3. Simpan ke database
        Absensi::create([
            'atlet_id' => $atlet_id,
            'jadwal_id' => $jadwal_id,
            'pelatih_id' => $pelatih_id,
            'tanggal_absensi' => $tanggal_absensi,
            'status_kehadiran' => $status_kehadiran,
        ]);
    }

    // 4. Kembali ke dashboard pelatih dengan pesan sukses
    return redirect()->route('pelatih.dashboard')
                     ->with('success', 'Absensi berhasil disimpan.');
    }
}
