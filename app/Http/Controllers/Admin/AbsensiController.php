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

        // 2. VALIDASI LOGIKA (WAJIB ISI NILAI JIKA HADIR)
        foreach ($request->data as $atlet_id => $val) {
            // CEK 1: Jika status tidak diisi (misal atlet dilewati), lewati pengecekan ini
            if (!isset($val['status'])) {
                continue;
            }

            // Jika statusnya HADIR (H), pastikan nilai tidak kosong
            if ($val['status'] == 'H') {
                if (empty($val['dribble']) || empty($val['pass']) || empty($val['shoot']) || empty($val['iq'])) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Gagal Simpan! Atlet yang HADIR wajib diisi nilai Dribble, Pass, Shoot, dan IQ.');
                }
            }
        }

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