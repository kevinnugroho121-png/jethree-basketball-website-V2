<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Atlet;
use App\Models\Pelatih;
use App\Models\Jadwal;
use App\Models\Tagihan; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // [PENTING] Untuk fungsi grafik

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 🟢 SUNTIKAN BARU: Alat bantu tangkap filter waktu & pelatih dinamis untuk grafik keaktifan
        $periode = $request->input('periode', date('Y-m'));
        $filter_tahun = substr($periode, 0, 4);
        $filter_bulan = substr($periode, 5, 2);
        
        $selected_pelatih = $request->input('pelatih_id');
        $list_pelatih = DB::table('pelatihs')->select('id', 'nama_lengkap')->get();

        // 1. Statistik Utama 
        $totalAtlet = Atlet::where('status', 'Aktif')->count(); 
        $totalPelatih = Pelatih::where('status', 'Aktif')->count();
        // Menghitung jadwal yang belum lewat (hari ini ke depan)
        $jadwalMendatang = Jadwal::whereDate('tanggal', '>=', now())->count();
        
        // ========================================================
        // [TAMBAHAN MISI 4] REKAP ATLET (Sesuai Saran Dosen)
        // ========================================================
        // A. Atlet Kurang Aktif (Status selain Aktif, misal Non-Aktif)
        $atletKurangAktif = Atlet::where('status', '!=', 'Aktif')->count();

        // B. Kalkulasi Otomatis Jumlah Atlet Telat Materi
        $atletTelatMateri = 0;
        $semuaAtletAktif = Atlet::where('status', 'Aktif')->get();
        
        foreach ($semuaAtletAktif as $atlet) {
            $tanggalPatokan = $atlet->tanggal_gabung ?? $atlet->created_at;
            
            // Cek apakah ada jadwal materi SEBELUM anak ini gabung
            $ketinggalan = Jadwal::where('kategori', $atlet->kategori)
                ->whereDate('tanggal', '<', $tanggalPatokan)
                ->where('status', '!=', 'Dibatalkan')
                ->exists(); // exists() lebih ringan & cepat dari get()

            if ($ketinggalan) {
                $atletTelatMateri++;
            }
        }
        // ========================================================

        // 2. Pendapatan Bulan Ini (Hanya yang Lunas)
        $pendapatanBulanIni = 0;
        try {
            // Cek apakah kolom tanggal_lunas ada, jika tidak pakai updated_at
            $pendapatanBulanIni = Tagihan::where('status', 'Lunas')
                                   ->whereMonth('updated_at', now()->month)
                                   ->whereYear('updated_at', now()->year)
                                   ->sum('nominal');
        } catch (\Exception $e) {
            $pendapatanBulanIni = 0;
        }

        // 3. Statistik Gender (Untuk Grafik Lingkaran)
        $cowok = Atlet::where('jenis_kelamin', 'Laki-laki')->count();
        $cewek = Atlet::where('jenis_kelamin', 'Perempuan')->count();
        
        // 4. Statistik Kelompok Umur (Untuk Grafik Batang)
        // [LOGIKA ANDA TETAP DIPERTAHANKAN]
        $ku_stats = [
            'KU-10' => Atlet::where('kategori', 'KU-10')->count(),
            'KU-12' => Atlet::where('kategori', 'KU-12')->count(),
            'KU-14' => Atlet::where('kategori', 'KU-14')->count(),
            'KU-16' => Atlet::where('kategori', 'KU-16')->count(),
            'KU-18' => Atlet::where('kategori', 'KU-18')->count(),
        ];

        // Pisahkan Label dan Value biar bisa dibaca Chart.js
        $kuLabels = array_keys($ku_stats);   // ['KU-10', 'KU-12', ...]
        $kuValues = array_values($ku_stats); // [5, 10, ...]

        // 5. [TAMBAHAN] Data Grafik Pendapatan 6 Bulan Terakhir (Line Chart)
        // Ini wajib ada biar grafik garis tidak error
        try {
            $incomeData = Tagihan::select(
                DB::raw('DATE_FORMAT(updated_at, "%Y-%m") as bulan'), 
                DB::raw('SUM(nominal) as total')
            )
            ->where('status', 'Lunas')
            ->groupBy('bulan')
            ->orderBy('bulan', 'asc')
            ->limit(6)
            ->get();

            $incomeLabels = $incomeData->map(function($item) {
                return Carbon::parse($item->bulan . '-01')->format('M Y');
            });
            $incomeValues = $incomeData->pluck('total');
        } catch (\Exception $e) {
            $incomeLabels = [];
            $incomeValues = [];
        }

        // 6. Jadwal Hari Ini & Mendatang (Untuk Tabel Widget)
        $upcomingSchedules = Jadwal::with('pelatih')
                             ->whereDate('tanggal', '>=', Carbon::today())
                             ->orderBy('tanggal', 'asc')
                             ->orderBy('jam_mulai', 'asc')
                             ->take(5) // Ambil 5 saja buat preview
                             ->get();

        // 🟢 SUNTIKAN BARU: Logika data grafik & tabel kontribusi mengajar pelatih khusus untuk Admin Web
        $coach_attendance_data = [];
        for ($m = 1; $m <= 12; $m++) {
            $query_grafik = DB::table('absensis')
                ->whereYear('tanggal_latihan', $filter_tahun)
                ->whereMonth('tanggal_latihan', $m);

            if ($selected_pelatih) {
                $query_grafik->join('jadwals', 'absensis.jadwal_id', '=', 'jadwals.id')
                             ->where('jadwals.pelatih_id', $selected_pelatih);
            }
                
            $coach_attendance_data[] = $query_grafik->count(DB::raw('DISTINCT absensis.jadwal_id'));
        }

        $coach_breakdown = DB::table('absensis')
            ->join('jadwals', 'absensis.jadwal_id', '=', 'jadwals.id')
            ->join('pelatihs', 'jadwals.pelatih_id', '=', 'pelatihs.id')
            ->select('pelatihs.nama_lengkap', DB::raw('COUNT(DISTINCT absensis.jadwal_id) as total_mengajar'))
            ->whereYear('absensis.tanggal_latihan', $filter_tahun)
            ->whereMonth('absensis.tanggal_latihan', $filter_bulan)
            ->when($selected_pelatih, function($q) use ($selected_pelatih) {
                return $q->where('jadwals.pelatih_id', $selected_pelatih);
            })
            ->groupBy('pelatihs.id', 'pelatihs.nama_lengkap')
            ->get();

        // Kirim ke View (Termasuk variabel baru)
        return view('admin.dashboard', compact(
            'totalAtlet', 
            'totalPelatih', 
            'jadwalMendatang', 
            'pendapatanBulanIni',
            'cowok', 
            'cewek', 
            'kuLabels', 
            'kuValues',
            'incomeLabels', 
            'incomeValues',
            'upcomingSchedules',
            'atletKurangAktif', 
            'atletTelatMateri',  
            'periode',               // 🟢 SUNTIKAN BARU
            'filter_tahun',          // 🟢 SUNTIKAN BARU
            'filter_bulan',          // 🟢 SUNTIKAN BARU
            'coach_attendance_data', // 🟢 SUNTIKAN BARU
            'coach_breakdown',       // 🟢 SUNTIKAN BARU
            'list_pelatih'           // 🟢 SUNTIKAN BARU
        ));
    }
}