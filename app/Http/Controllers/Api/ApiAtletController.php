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
    public function rapor()
    {
        $user = Auth::user();
        $atlet = Atlet::where('user_id', $user->id)->first();

        if (!$atlet) {
            return response()->json(['success' => false, 'message' => 'Data atlet tidak ditemukan']);
        }

        // AMBIL SEMUA DATA ABSENSI TAHUN INI (Untuk generate laporan kotak bulanan)
        $tahunIni = \Carbon\Carbon::now()->year;
        $all_absensi = Absensi::where('atlet_id', $atlet->id)
                              ->whereYear('tanggal_latihan', $tahunIni)
                              ->get();

        $namaBulanIndo = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
            7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
        ];

        $rekap_bulanan = [];
        $total_hadir_setahun = 0;

        // Looping untuk membuat data kompartemen kotak per bulan (Poin 14 & 16)
        for ($m = 1; $m <= 12; $m++) {
            // Filter data absensi khusus bulan ini
            $absensi_bulan = $all_absensi->where('tanggal_latihan', '!=', null)
                                         ->filter(function($item) use ($m) {
                                             return \Carbon\Carbon::parse($item->tanggal_latihan)->month == $m;
                                         });

            $hadir = $absensi_bulan->where('status', 'H')->count();
            $sakit = $absensi_bulan->where('status', 'S')->count();
            $izin  = $absensi_bulan->where('status', 'I')->count();
            $alpha = $absensi_bulan->where('status', 'A')->count();

            // Hitung jatah latihan (Target 12 Sesi)
            $target = 12;
            $kurang = $target - $hadir;
            $status_tuntas = ($kurang <= 0) ? 'TUNTAS' : 'BELUM TUNTAS';
            $utang_sesi = ($kurang > 0) ? $kurang : 0;

            $total_hadir_setahun += $hadir;

            // Hitung rata-rata nilai skill khusus bulan ini jika pelatih ada input nilai
            $avgDribble = round($absensi_bulan->where('status', 'H')->avg('nilai_dribble') ?? 0);
            $avgPass    = round($absensi_bulan->where('status', 'H')->avg('nilai_pass') ?? 0);
            $avgShoot   = round($absensi_bulan->where('status', 'H')->avg('nilai_shoot') ?? 0);
            $avgIQ      = round($absensi_bulan->where('status', 'H')->avg('nilai_iq') ?? 0);
            $overall_bulan = round(($avgDribble + $avgPass + $avgShoot + $avgIQ) / 4);

            // Hanya masukkan bulan ke dalam list jika sudah ada record absensi atau merupakan bulan berjalan
            if ($absensi_bulan->count() > 0 || $m <= \Carbon\Carbon::now()->month) {
                $rekap_bulanan[] = [
                    'bulan_angka' => $m,
                    'nama_bulan' => $namaBulanIndo[$m],
                    'rekap_kehadiran' => [
                        'hadir' => $hadir,
                        'sakit' => $sakit,
                        'izin' => $izin,
                        'alpha' => $alpha,
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
            'periode' => 'Semester Berjalan - Tahun ' . $tahunIni,
            'total_hadir_akumulasi' => $total_hadir_setahun,
            'data_rapor_bulanan' => $rekap_bulanan 
        ]);
    }
}