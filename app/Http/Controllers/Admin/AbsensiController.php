<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Atlet;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    /**
     * Menampilkan daftar jadwal untuk dipilih mana yang mau diabsen.
     */
    public function index()
    {
        $jadwals = Jadwal::with('pelatih')
            ->orderBy('tanggal', 'desc')
            ->paginate(5); 

        return view('admin.absensi.index', compact('jadwals'));
    }

    /**
     * Menampilkan Form Absensi & Penilaian.
     */
    public function create($jadwal_id)
    {
        $jadwal = Jadwal::findOrFail($jadwal_id);

        $atlets = Atlet::where('kategori', $jadwal->kategori)
            ->where('status', 'Aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        $existingAbsensi = Absensi::where('jadwal_id', $jadwal_id)
            ->get()
            ->keyBy('atlet_id');

        $sudahDiabsen = $existingAbsensi->count() > 0;

        return view('admin.absensi.absensi', compact('jadwal', 'atlets', 'existingAbsensi', 'sudahDiabsen'));
    }

    /**
     * Menyimpan Data Absensi & Nilai.
     */
    public function store(Request $request, $jadwal_id)
    {
        // 1. Validasi Dasar
        $request->validate([
            'data' => 'required|array', 
        ]);

        $jadwal = Jadwal::findOrFail($jadwal_id);



        // REVISI POIN 13 & 14: Hapus pemaksaan nilai angka harian, ganti wajib isi materi latihan
        $request->validate([
            'materi' => 'required|string|min:5',
        ], [
            'materi.required' => 'Gagal Simpan! Deskripsi menu latihan hari ini wajib diisi sebelum menutup absensi.',
        ]);

        $jadwal = Jadwal::findOrFail($jadwal_id);
        
        // REVISI POIN 15: Admin mem-backup pengisian materi latihan dari pelatih
        $jadwal->update(['materi' => $request->materi]);

        

        // 3. LOOPING SIMPAN DATA
        foreach ($request->data as $atlet_id => $val) {
            
            // CEK 2: JIKA STATUS TIDAK ADA, JANGAN PROSES BARIS INI
            // Ini adalah kunci agar error "Undefined array key status" hilang
            if (!isset($val['status'])) {
                continue;
            }

            $isHadir = ($val['status'] == 'H');

            \App\Models\Absensi::updateOrCreate(
                [
                    'jadwal_id' => $jadwal->id,
                    'atlet_id'  => $atlet_id,
                ],
                [
                    'status'          => $val['status'], 
                    'tanggal_latihan' => $jadwal->tanggal, 
                    
                    // Simpan nilai hanya jika Hadir, gunakan ?? null untuk keamanan ekstra
                    'nilai_dribble'   => $isHadir ? ($val['dribble'] ?? null) : null,
                    'nilai_pass'      => $isHadir ? ($val['pass'] ?? null) : null,
                    'nilai_shoot'     => $isHadir ? ($val['shoot'] ?? null) : null,
                    'nilai_iq'        => $isHadir ? ($val['iq'] ?? null) : null,
                    
                    'catatan'         => $val['catatan'] ?? null, 
                ]
            );
        }

        return redirect()->back()->with('success', 'Data Absensi & Penilaian Berhasil Disimpan!');
    }
}