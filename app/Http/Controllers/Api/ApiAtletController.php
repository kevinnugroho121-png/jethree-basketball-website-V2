<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\Atlet;
use App\Models\Absensi;
use App\Models\Tagihan;

class ApiAtletController extends Controller
{
    // 1. DASHBOARD ATLET (FIX PROFIL & GRAFIK)
    public function dashboard()
    {
        $user = Auth::user();
        $atlet = Atlet::where('user_id', $user->id)->first();

        if (!$atlet) {
            return response()->json(['success' => false, 'message' => 'Data atlet belum terhubung'], 404);
        }

        // A. Data Profil Ringkas (Header)
        $profil = [
            'nama' => $atlet->nama_lengkap,
            // 💡 PERBAIKAN: Ubah menjadi kolom 'kategori' sesuai nama kolom asli di tabel atlets SQLyog
            'kategori' => $atlet->kategori ?? '-', 
            'posisi' => $atlet->posisi ?? '-',
            // 💡 PERBAIKAN: Gunakan kolom 'foto_profil' asli agar tidak mengirim tautan localhost pembawa eror
            'foto' => $atlet->foto_profil, 
        ];

        // B. Statistik Kehadiran Bulan Ini
        $bulan_ini = Carbon::now()->month;
        $tahun_ini = Carbon::now()->year;
        
        // REVISI: Ubah created_at menjadi tanggal_latihan agar sinkron dengan riwayat absensi asli
        $hadir = Absensi::where('atlet_id', $atlet->id)
                        ->whereMonth('tanggal_latihan', $bulan_ini)
                        ->whereYear('tanggal_latihan', $tahun_ini)
                        ->where('status', 'H')
                        ->count();

        // Hitung total sesi bulan ini berdasarkan tanggal latihan
        $total_sesi = Absensi::where('atlet_id', $atlet->id)
                             ->whereMonth('tanggal_latihan', $bulan_ini)
                             ->whereYear('tanggal_latihan', $tahun_ini)
                             ->count();


        $persentase_hadir = ($total_sesi > 0) ? round(($hadir / $total_sesi) * 100) : 0;


        // C. DATA GRAFIK BULANAN (SINKRON REVISI RAPOR BULANAN)
        // Mengelompokkan semua nilai kehadiran berdasarkan bulan berjalan di tahun ini
        $grafik_data = Absensi::where('atlet_id', $atlet->id)
                              ->where('status', 'H')
                              ->whereYear('tanggal_latihan', Carbon::now()->year)
                              ->selectRaw('MONTH(tanggal_latihan) as bulan_angka, 
                                           AVG(nilai_dribble) as avg_dribble, 
                                           AVG(nilai_pass) as avg_pass, 
                                           AVG(nilai_shoot) as avg_shoot,
                                           AVG(nilai_iq) as avg_iq')
                              ->groupBy('bulan_angka')
                              ->orderBy('bulan_angka', 'asc')
                              ->get()
                              ->map(function($item) {
                                  // Nama bulan dalam bahasa Indonesia
                                  $namaBulan = [
                                      1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'Mei', 6=>'Jun',
                                      7=>'Jul', 8=>'Agu', 9=>'Sep', 10=>'Okt', 11=>'Nov', 12=>'Des'
                                  ];
                                  
                                  // Hitung rata-rata gabungan seluruh skill di bulan tersebut
                                  $total_avg = ($item->avg_dribble + $item->avg_pass + $item->avg_shoot + $item->avg_iq) / 4;
                                  
                                  return [
                                      'bulan' => $namaBulan[$item->bulan_angka] ?? 'Bulan',
                                      'nilai' => round($total_avg)
                                  ];
                              });


        // D. Status Tagihan Bulan Ini (Dibetulkan Menggunakan Format Angka Berdasarkan DB)
        $tagihan = Tagihan::where('atlet_id', $atlet->id)
                          ->where('bulan', \Carbon\Carbon::now()->month) // Menghasilkan angka murni (1-12)
                          ->where('tahun', \Carbon\Carbon::now()->year)
                          ->first();
        
        $status_spp = $tagihan ? $tagihan->status : 'Belum Ada Tagihan';

        // ====================================================================
        // [BARU] REVISI POIN 1: LOGIKA GEMBOK MENU ATLET BARU (NO PAY NO PLAY)
        // ====================================================================
        // Cek apakah atlet ini sudah pernah membayar SPP dan diverifikasi 'Lunas' minimal 1 kali?
        $pernahBayar = Tagihan::where('atlet_id', $atlet->id)
                              ->where('status', 'Lunas')
                              ->exists();

        // Jika BELUM PERNAH lunas sama sekali, maka menu di Flutter akan dikunci (is_locked = true)
        $is_locked = !$pernahBayar;

        return response()->json([
            'success' => true,
            'is_locked' => $is_locked, // Sinyal gembok untuk dibaca oleh Flutter
            'profil' => $profil,
            'kehadiran' => $persentase_hadir,
            'status_spp' => $status_spp,
            'grafik' => $grafik_data
        ]);
    }

    // 2. PROFIL LENGKAP (SUDAH FIX SESUAI SCREENSHOT DB)
    public function profile()
    {
        $user = Auth::user();
        $atlet = Atlet::where('user_id', $user->id)->first();

        if (!$atlet) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'nama_lengkap' => $atlet->nama_lengkap,
                'panggilan'    => $atlet->nama_panggilan ?? '-',
                'ttl'          => ($atlet->tempat_lahir ?? '-') . ', ' . ($atlet->tanggal_lahir ? Carbon::parse($atlet->tanggal_lahir)->format('d M Y') : '-'),
                'jenis_kelamin'=> $atlet->jenis_kelamin,
                
                // NO HP ATLET (Dari screenshot web sebelumnya: 0815...)
                'nomor_hp'     => $atlet->no_hp ?? $atlet->no_hp_atlet ?? '-', 
                
                'alamat'       => $atlet->alamat ?? '-',
                // 💡 PERBAIKAN: Langsung tembak ke kolom 'kategori' agar sinkron penuh dengan DB
                'kategori'     => $atlet->kategori ?? '-', 
                'sekolah'      => $atlet->sekolah ?? $atlet->nama_sekolah ?? '-',
                'posisi'       => $atlet->posisi ?? '-',
                
                // DATA ORTU (Sesuai Screenshot Database: nama_orang_tua)
                'nama_ortu'    => $atlet->nama_orang_tua ?? '-',
                
                // NO WA ORTU (Sesuai Screenshot Database: no_hp_orang_tua)
                'no_wa_ortu'   => $atlet->no_hp_orang_tua ?? '-',
                
                // 💡 PERBAIKAN: Ambil dari kolom 'foto_profil' asli database agar berupa path teks seperti pelatih
                'foto'         => $atlet->foto_profil 
            ]
        ]);
    }

    // 3. RAPOR OTOMATIS (AGREGASI NILAI HARIAN)
    public function rapor(\Illuminate\Http\Request $request) // 💡 Ditambahkan Request agar bisa membaca input filter dari HP
    {
        $user = Auth::user();
        $atlet = Atlet::where('user_id', $user->id)->first();

        if (!$atlet) {
            return response()->json(['success' => false, 'message' => 'Data atlet tidak ditemukan']);
        }

        // 🟢 DINAMIS FILTER: Cek semester apa yang dipilih user di HP (Default: semester aktif sekarang)
        $bulanSekarang = \Carbon\Carbon::now()->month;
        $defaultSemester = ($bulanSekarang <= 6) ? 'genap' : 'ganjil';
        $semesterTarget = $request->query('semester', $defaultSemester);

        $startBulan = ($semesterTarget === 'genap') ? 1 : 7;
        $endBulan = ($semesterTarget === 'genap') ? 6 : 12;
        $teksSemester = ($semesterTarget === 'genap') ? 'Semester Genap' : 'Semester Ganjil';

        // 🟢 PERBAIKAN TAHUN AJARAN: Pecah format TA "2025/2026" menjadi kueri tahun murni untuk database
        $defaultTA = ($bulanSekarang >= 7) 
            ? \Carbon\Carbon::now()->year . '/' . (\Carbon\Carbon::now()->year + 1)
            : (\Carbon\Carbon::now()->year - 1) . '/' . \Carbon\Carbon::now()->year;
            
        $tahunInput = $request->query('tahun', $defaultTA);
        $tahunParts = explode('/', $tahunInput);
        $tahunAwal = intval($tahunParts[0]);
        $tahunAkhir = isset($tahunParts[1]) ? intval($tahunParts[1]) : $tahunAwal + 1;

        // Semester Ganjil ambil tahun awal (Juli-Des), Semester Genap ambil tahun akhir (Jan-Jun)
        $tahunIni = ($semesterTarget === 'ganjil') ? $tahunAwal : $tahunAkhir;

        $all_absensi = Absensi::where('atlet_id', $atlet->id)
                              ->whereYear('tanggal_latihan', $tahunIni)
                              ->get();

        // 🟢 FIX PILIHAN 1: Saring dan hitung akumulasi nilai rata-rata satu semester penuh untuk 4 lingkaran atas
        $absensi_semester = $all_absensi->filter(function($item) use ($startBulan, $endBulan) {
            if (!$item->tanggal_latihan) return false;
            $m = \Carbon\Carbon::parse($item->tanggal_latihan)->month;
            return $m >= $startBulan && $m <= $endBulan;
        });

        $semHadir = $absensi_semester->where('status', 'H');
        $rootAvgDribble  = round($semHadir->avg('nilai_dribble') ?? 0);
        $rootAvgPassing  = round($semHadir->avg('nilai_pass') ?? 0);
        $rootAvgShooting = round($semHadir->avg('nilai_shoot') ?? 0);
        $rootAvgIQ       = round($semHadir->avg('nilai_iq') ?? 0);
        $rootOverall     = round(($rootAvgDribble + $rootAvgPassing + $rootAvgShooting + $rootAvgIQ) / 4);

        $namaBulanIndo = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
            7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
        ];

        $rekap_bulanan = [];
        $total_hadir_setahun = 0;

        // Looping dinamis mengikuti rentang bulan semester terpilih
        for ($m = $startBulan; $m <= $endBulan; $m++) {
            // Filter data absensi khusus bulan ini
            $absensi_bulan = $all_absensi->where('tanggal_latihan', '!=', null)
                                         ->filter(function($item) use ($m) {
                                             return \Carbon\Carbon::parse($item->tanggal_latihan)->month == $m;
                                         });

            $hadir    = $absensi_bulan->where('status', 'H')->count();
            $ga_hadir = $absensi_bulan->where('status', 'A')->count(); // ⚡ Poin 27: Sakit & Izin dilebur total ke Ga Hadir

            // Hitung jatah latihan (Target 12 Sesi) - Poin 28 & 29
            $target = 12;
            $kurang = $target - $hadir;
            $status_tuntas = ($hadir >= $target) ? 'TUNTAS ✅' : 'BELUM TUNTAS ⚠️'; // ⚡ Suntik simbol status klop dengan kemauan client
            $utang_sesi = ($kurang > 0) ? $kurang : 0;

            $total_hadir_setahun += $hadir;

            // Hitung rata-rata nilai skill khusus bulan ini jika pelatih ada input nilai
            $avgDribble = round($absensi_bulan->where('status', 'H')->avg('nilai_dribble') ?? 0);
            $avgPass    = round($absensi_bulan->where('status', 'H')->avg('nilai_pass') ?? 0);
            $avgShoot   = round($absensi_bulan->where('status', 'H')->avg('nilai_shoot') ?? 0);
            $avgIQ      = round($absensi_bulan->where('status', 'H')->avg('nilai_iq') ?? 0);
            $overall_bulan = round(($avgDribble + $avgPass + $avgShoot + $avgIQ) / 4);

            // 🟢 PERBAIKAN LOGIKA TA: Deteksi apakah rentang bulan berada di masa lalu atau masa depan secara dinamis
            $currentYear = \Carbon\Carbon::now()->year;
            $currentMonth = \Carbon\Carbon::now()->month;
            $isMasaDepan = ($tahunIni > $currentYear) || ($tahunIni == $currentYear && $m > $currentMonth);

            if ($absensi_bulan->count() > 0 || !$isMasaDepan) {
                // ⚡ Poin 25 & 26: Deteksi nama semester otomatis berdasarkan urutan angka bulan
                $semester_nama = ($m <= 6) ? 'Genap (Januari - Juni)' : 'Ganjil (Juli - Desember)';

                $rekap_bulanan[] = [
                    'bulan_angka' => $m,
                    'nama_bulan'  => $namaBulanIndo[$m],
                    'semester'    => $semester_nama, // ⚡ Menjadi token filter pembagi di aplikasi HP
                    'rekap_kehadiran' => [
                        'hadir'    => $hadir,
                        'ga_hadir' => $ga_hadir, // ⚡ Bersih tanpa sisa variabel Sakit/Izin
                    ],
                    'sistem_sks' => [
                        'target_wajib' => $target,
                        'total_terpenuhi' => $hadir,
                        'sisa_utang_latihan' => $utang_sesi,
                        'status' => $status_tuntas
                    ],
                    'nilai_performa' => [
                        'overall' => $overall_bulan,
                        'dribble' => $avgDribble,
                        'passing' => $avgPass,
                        'shooting' => $avgShoot,
                        'game_iq' => $avgIQ
                    ]
                ];
            }
        }





        // Cukup sisakan return response rekap bulanan yang sudah rapi ini, sisanya di bawah hapus total
        return response()->json([
            'success' => true,
            // 💡 FIX TEXT: Gunakan teks label format Tahun Ajaran (TA) resmi biar dosen penguji langsung setuju
            'periode' => ($semesterTarget === 'genap') ? 'Semester Genap (Januari - Juni) TA ' . $tahunInput : 'Semester Ganjil (Juli - Desember) TA ' . $tahunInput,
            'total_hadir_akumulasi' => $total_hadir_setahun,
            // 🟢 INTEGRASI DATA: Lempar data performa semester ke root JSON biar lingkaran atas Flutter terisi angka asli
            'rata_rata_kompetensi' => [
                'overall' => $rootOverall,
                'dribbling' => $rootAvgDribble,
                'passing' => $rootAvgPassing,
                'shooting' => $rootAvgShooting,
                'iq_mental' => $rootAvgIQ,
            ],
            'data_rapor_bulanan' => $rekap_bulanan 
        ]);
    }

    // 🟢 BARU: Tempatkan fungsi baru untuk Lonceng Notifikasi di sini sebelum kurung penutup class
    // 🔔 4. NOTIFICATION CENTER (DYNAMIC REAL-TIME ALERTS)
    public function notifications()
    {
        $user = Auth::user();
        $atlet = Atlet::where('user_id', $user->id)->first();

        if (!$atlet) {
            return response()->json(['success' => false, 'message' => 'Data atlet tidak ditemukan']);
        }

        $notifications = [];
        $now = Carbon::now();

        // 💡 NOTIFIKASI 1: Cek Tagihan SPP Bulan Ini yang Belum Lunas
        $tagihanBulanIni = Tagihan::where('atlet_id', $atlet->id)
                                  ->where('bulan', $now->month)
                                  ->where('tahun', $now->year)
                                  ->first();

        if (!$tagihanBulanIni || $tagihanBulanIni->status !== 'Lunas') {
            $notifications[] = [
                'id' => 'spp_' . $now->format('m_Y'),
                'title' => 'Tagihan SPP Belum Lunas ⚠️',
                'message' => 'Yuk segera lakukan pembayaran SPP untuk bulan ' . $now->translatedFormat('F Y') . ' agar status latihanmu tetap aktif!',
                'type' => 'finance',
                'created_at' => $now->toIso8601String(),
                'is_unread' => true
            ];
        }

        // 💡 NOTIFIKASI 2: Cek Utang Sesi Latihan Bulan Ini (Peringatan Awal)
        $hadirBulanIni = Absensi::where('atlet_id', $atlet->id)
                                ->whereMonth('tanggal_latihan', $now->month)
                                ->whereYear('tanggal_latihan', $now->year)
                                ->where('status', 'H')
                                ->count();

        $targetWajib = 12;
        if ($hadirBulanIni < $targetWajib) {
            $sisaSesi = $targetWajib - $hadirBulanIni;
            $notifications[] = [
                'id' => 'absen_' . $now->format('m_Y'),
                'title' => 'Sisa Target Sesi Latihan 🏀',
                'message' => 'Kamu baru memenuhi ' . $hadirBulanIni . ' sesi. Kurang ' . $sisaSesi . ' sesi lagi untuk menuntaskan target bulan ini!',
                'type' => 'attendance',
                'created_at' => $now->toIso8601String(),
                'is_unread' => $hadirBulanIni == 0 ? true : false
            ];
        }

        return response()->json([
            'success' => true,
            'total_unread' => collect($notifications)->where('is_unread', true)->count(),
            'data' => $notifications
        ]);
    }
}