<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Atlet;
use App\Models\Pelatih;
use App\Models\ProgresAtlet;
use App\Models\Absensi; // [PENTING] Tambahkan Model Absensi untuk hitung nilai

class ProgresAtletController extends Controller
{
    // 1. HALAMAN DAFTAR ATLET (INDEX - OTOMATIS TERSORTIR SESUAI KU PELATIH)
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Cari dulu Pelatih ini pegang Jadwal dengan KU (Kategori) apa saja
        if ($user->role === 'pelatih' && $user->pelatih) {
            $kategoriPelatih = \App\Models\Jadwal::where('pelatih_id', $user->pelatih->id)
                                                 ->pluck('kategori')
                                                 ->unique()
                                                 ->toArray(); // Contoh hasil: ['KU-10', 'KU-12']
            
            // 2. Ambil Atlet yang HANYA masuk di kategori milik pelatih tersebut
            $query = Atlet::where('status', 'Aktif')
                          ->whereIn('kategori', $kategoriPelatih);
        } else {
            // Jika admin yang buka, tampilkan semua
            $query = Atlet::where('status', 'Aktif');
        }

        // 3. Filter Pencarian (Search)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nama_panggilan', 'like', "%{$search}%");
            });
        }

        if ($request->has('kategori') && $request->kategori != '') {
            $query->where('kategori', $request->kategori);
        }

        $atlets = $query->orderBy('nama_lengkap', 'asc')
                        ->paginate(10)
                        ->withQueryString();

        return view('pelatih.progres.index', compact('atlets'));
    }

    // 2. HALAMAN FORMULIR INPUT (SEKARANG JADI AUTO-FILL)
    public function create($id)
    {
        // A. Data Dasar
        $atlet = Atlet::findOrFail($id);
        $user = Auth::user();
        $pelatih = Pelatih::where('user_id', $user->id)->first();

        if (!$pelatih) {
            return redirect()->back()->with('error', 'Profil Pelatih belum lengkap.');
        }

        // B. LOGIKA OTOMATIS (AUTO-CALCULATE DARI ABSENSI)
        // REVISI FASE 2: Filter dikunci hanya pada Bulan & Tahun berjalan agar menjadi rekap bulanan murni mumpung target 12 sesi
        $absensis = Absensi::where('atlet_id', $atlet->id)
                           ->where('status', 'H') // Hanya yang hadir
                           ->whereMonth('tanggal_latihan', date('m')) // Mengunci bulan saat ini (Contoh: Juli)
                           ->whereYear('tanggal_latihan', date('Y'))  // Mengunci tahun saat ini (Contoh: 2026)
                           ->get();

        // Nilai Default (Jika belum ada latihan)
        $nilai = [
            'teknik' => 0, // Dribble & Shoot
            'fisik'  => 0, // Passing (Sementara kita mapping ke fisik atau ambil rata2)
            'mental' => 0, // Disiplin/Attitude (Jika ada kolomnya)
            'taktik' => 0, // IQ
            'total_sesi' => $absensis->count()
        ];

        if ($absensis->count() > 0) {
            // RUMUS MAPPING (Sesuaikan dengan kolom di tabel Absensi vs Tabel Rapor)
            // Di tabel Absensi kita punya: nilai_dribble, nilai_pass, nilai_shoot, nilai_iq
            
            // 1. Teknik = Rata-rata (Dribble + Shoot)
            $avgDribble = $absensis->avg('nilai_dribble') ?? 0;
            $avgShoot   = $absensis->avg('nilai_shoot') ?? 0;
            $nilai['teknik'] = round(($avgDribble + $avgShoot) / 2);

            // 2. Fisik = Kita ambil dari Passing (Sementara, atau bisa tambah kolom fisik di absen nanti)
            // Asumsi: Passing butuh fisik/power
            $nilai['fisik'] = round($absensis->avg('nilai_pass') ?? 0);

            // 3. Taktik = Game IQ
            $nilai['taktik'] = round($absensis->avg('nilai_iq') ?? 0);

            // 4. Mental = Kita set default tinggi jika rajin hadir (Bonus Kerajinan)
            // Atau ambil dari rata-rata nilai keseluruhan sebagai baseline
            $nilai['mental'] = round(($nilai['teknik'] + $nilai['taktik']) / 2); 
        }

        // Kirim data perhitungan ($nilai) ke View
        return view('pelatih.progres.create', compact('atlet', 'pelatih', 'nilai'));
    }

    // 3. SIMPAN DATA (SNAPSHOT)
    // Pelatih tetap perlu klik "Simpan" agar data tersimpan permanen sebagai Rapor Bulan Ini
    public function store(Request $request)
    {
        $request->validate([
            'atlet_id'   => 'required',
            'pelatih_id' => 'required',
            'tanggal'    => 'required|date',
            'teknik'     => 'required|numeric|min:0|max:100',
            'fisik'      => 'required|numeric|min:0|max:100',
            'mental'     => 'required|numeric|min:0|max:100',
            'taktik'     => 'required|numeric|min:0|max:100',
        ]);

        ProgresAtlet::create([
            'pelatih_id' => $request->pelatih_id,
            'atlet_id'   => $request->atlet_id,
            'tanggal'    => $request->tanggal,
            'teknik'     => $request->teknik,
            'fisik'      => $request->fisik,
            'mental'     => $request->mental,
            'taktik'     => $request->taktik,
            'catatan'    => $request->catatan,
        ]);

        return redirect()->route('pelatih.progres.index')
                         ->with('success', 'Rapor nilai berhasil disimpan! Data diambil dari rata-rata latihan.');
    }
}