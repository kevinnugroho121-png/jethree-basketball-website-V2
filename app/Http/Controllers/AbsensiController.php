<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Atlet;
use App\Models\Absensi;

class AbsensiController extends Controller
{
    /**
     * MENU UTAMA: Menampilkan daftar jadwal MILIK PELATIH (Dilengkapi Filter, Auto-Sort Hari Ini, & Rekap Anak).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = \Carbon\Carbon::today()->toDateString();
        
        // 1. Kueri Dasar: Ambil jadwal milik pelatih ini saja
        if ($user->role === 'pelatih' && $user->pelatih) {
            $query = Jadwal::where('pelatih_id', $user->pelatih->id);
        } else {
            $query = Jadwal::query(); // Fallback untuk admin
        }

        // 2. TERAPKAN FILTER LENGKAP
        // A. Filter Lokasi (Search)
        if ($request->filled('search')) {
            $query->where('lokasi', 'like', '%' . $request->search . '%');
        }
        
        // B. Filter Kategori
        if ($request->filled('kategori') && $request->kategori != 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        // C. Filter Bulan Latihan
        if ($request->filled('bulan') && $request->bulan != 'Semua') {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // 3. Tambahkan "Sihir" Hitungan Anak & Auto-Sort
        $query->addSelect(['jadwals.*', 
            'total_anak' => \App\Models\Atlet::selectRaw('COUNT(*)')
                ->whereColumn('atlets.kategori', 'jadwals.kategori')
                ->where('atlets.status', 'Aktif'),
            
            'total_hadir' => \App\Models\Absensi::selectRaw('COUNT(*)')
                ->whereColumn('absensis.jadwal_id', 'jadwals.id')
                ->where('absensis.status', 'H'),
                
            'total_diabsen' => \App\Models\Absensi::selectRaw('COUNT(*)')
                ->whereColumn('absensis.jadwal_id', 'jadwals.id')
        ])->selectRaw("IF(tanggal = '$today', 1, 0) as is_today");

        // 4. Terapkan Filter Status Absen (ANTI ERROR PAGINATION)
        // Kita gunakan whereHas & whereDoesntHave yang 100% aman untuk paginate
        if ($request->filled('status_absen') && $request->status_absen != 'Semua') {
            if ($request->status_absen == 'Sudah') {
                $query->whereHas('absensis');
            } elseif ($request->status_absen == 'Belum') {
                $query->whereDoesntHave('absensis');
            }
        }

        // 5. Urutan Penampilan (PERBAIKAN ORDER BY BIAR AMAN)
        // Kita masukkan rumusnya langsung ke dalam orderByRaw agar tidak nyangkut alias
        $jadwals = $query->orderByRaw("IF(tanggal = '$today', 1, 0) DESC")
                         ->orderByRaw("CASE WHEN tanggal > '$today' THEN 1 ELSE 2 END")
                         ->orderBy('tanggal', 'desc')
                         ->orderBy('jam_mulai', 'asc')
                         ->paginate(10)
                         ->withQueryString();

        return view('pelatih.absensi.index', compact('jadwals', 'today'));
    }

    /**
     * FORM INPUT: Menggunakan View Admin agar Tampilan SAMA PERSIS.
     */
    public function create($jadwal_id)
    {
        // 1. Ambil Jadwal
        $jadwal = Jadwal::findOrFail($jadwal_id);

        // 2. Ambil Atlet Sesuai Kategori
        $atlets = Atlet::where('kategori', $jadwal->kategori)
            ->where('status', 'Aktif')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // 3. Ambil Absensi Lama
        $existingAbsensi = Absensi::where('jadwal_id', $jadwal_id)
            ->get()
            ->keyBy('atlet_id');

        // 4. LOGIKA KUNCI TOMBOL (Sama seperti Admin)
        $sudahDiabsen = $existingAbsensi->count() > 0;

        // PENTING: Return ke 'admin.absensi.absensi' agar desainnya 100% sama dengan Admin
        return view('admin.absensi.absensi', compact('jadwal', 'atlets', 'existingAbsensi', 'sudahDiabsen'));
    }

    /**
     * SIMPAN DATA: Logika Penyimpanan yang Konsisten (Bisa Satuan/Semua).
     */


    public function store(Request $request, $jadwal_id)
    {
        // REVISI POIN 13: Pelatih wajib mengisi deskripsi menu latihan di web/mobile sebelum data disimpan
        $request->validate([
            'data'   => 'required|array',
            'materi' => 'required|string|min:5',
        ], [
            'materi.required' => 'Gagal! Anda wajib mengisi kotak deskripsi menu latihan hari ini terlebih dahulu.',
        ]);

        $jadwal = Jadwal::findOrFail($jadwal_id);
        
        // REVISI POIN 13 & 15: Kunci materi harian langsung ke record jadwal kelas terkait
        $jadwal->update(['materi' => $request->materi]);

        

        foreach ($request->data as $atlet_id => $val) {
            
            // [KUNCI PERBAIKAN]: Cek apakah status diisi oleh pelatih?
            // Jika kosong (pelatih belum ngeklik H/I/S/A), lewati atlet ini.
            // Ini membuat sistem bisa nyimpan 1, 2, atau semua atlet tanpa error.
            if (!isset($val['status'])) {
                continue; 
            }


            // ==================== KODE YANG BENAR (GANTI DENGAN INI) ====================
            // 1. Deteksi ID Pelatih yang bereksekusi di lapangan
            $userLog = Auth::user();
            $pelatihHadirId = null;

            // Jika Coach Irul memilih pelatih lain/dirinya sendiri dari dropdown request
            if ($request->has('pelatih_id') && $request->pelatih_id != null) {
                $pelatihHadirId = $request->pelatih_id;
            } 
            // Jika pelatih reguler yang login, otomatis pakai ID pelatihnya sendiri
            else {
                $pelatihHadirId = ($userLog->role === 'pelatih' && $userLog->pelatih) ? $userLog->pelatih->id : null;
            }

            // 2. Simpan ke database absensi
            Absensi::updateOrCreate(
                [
                    'jadwal_id' => $jadwal->id,
                    'atlet_id'  => $atlet_id,
                ],
                [
                    'status'          => $val['status'], 
                    'tanggal_latihan' => $jadwal->tanggal, 
                    
                    // SUNTIKAN LOGIKA BARU: Mencatat siapa yang melatih riil di lapangan
                    'pelatih_hadir_id'=> $pelatihHadirId, 
                    
                    'nilai_dribble'   => $val['dribble'] ?? null,
                    'nilai_pass'      => $val['pass'] ?? null,
                    'nilai_shoot'     => $val['shoot'] ?? null,
                    'nilai_iq'        => $val['iq'] ?? null,
                    
                    'catatan'         => $val['catatan'] ?? null, 
                ]
            );
// ============================================================================


        }

        // Redirect Back agar Pelatih tetap di halaman itu dan melihat Pop-up Sukses
        return redirect()->back()->with('success', 'Data Absensi & Penilaian Berhasil Disimpan!');
    }
}