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
            'kategori' => $atlet->kategori_hitung ?? '-', // Pastikan kolom ini diisi di DB
            'posisi' => $atlet->posisi ?? '-',
            'foto' => $atlet->foto_url,
        ];

        // B. Statistik Kehadiran Bulan Ini
        $bulan_ini = Carbon::now()->month;
        $tahun_ini = Carbon::now()->year;
        
        $hadir = Absensi::where('atlet_id', $atlet->id)
                        ->whereMonth('created_at', $bulan_ini)
                        ->whereYear('created_at', $tahun_ini)
                        ->where('status', 'H')
                        ->count();
        // Hitung total sesi (misal total jadwal bulan ini) - disederhanakan:
        $total_sesi = Absensi::where('atlet_id', $atlet->id)
                             ->whereMonth('created_at', $bulan_ini)
                             ->count();
        $persentase_hadir = ($total_sesi > 0) ? round(($hadir / $total_sesi) * 100) : 0;


        // C. DATA GRAFIK (SINKRON DENGAN WEB)
        // Mengambil 5 latihan terakhir
        $grafik_data = Absensi::where('atlet_id', $atlet->id)
                              ->where('status', 'H')
                              ->whereNotNull('nilai_dribble')
                              ->orderBy('created_at', 'asc') // Urutkan tanggal
                              ->take(5)
                              ->get()
                              ->map(function($item) {
                                  // Hitung Rata-rata Skill (Sama seperti Web)
                                  $avg = ($item->nilai_dribble + $item->nilai_pass + $item->nilai_shoot) / 3;
                                  return [
                                      'tanggal' => Carbon::parse($item->created_at)->format('d M'),
                                      'nilai' => round($avg)
                                  ];
                              });

        // D. Status Tagihan Bulan Ini
        $tagihan = Tagihan::where('atlet_id', $atlet->id)
                          ->where('bulan', date('F')) // January, February
                          ->where('tahun', date('Y'))
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
                'kategori'     => $atlet->kategori_hitung ?? $atlet->kategori_umur ?? '-', 
                'sekolah'      => $atlet->sekolah ?? $atlet->nama_sekolah ?? '-', 
                'posisi'       => $atlet->posisi ?? '-',
                
                // DATA ORTU (Sesuai Screenshot Database: nama_orang_tua)
                'nama_ortu'    => $atlet->nama_orang_tua ?? '-',
                
                // NO WA ORTU (Sesuai Screenshot Database: no_hp_orang_tua)
                'no_wa_ortu'   => $atlet->no_hp_orang_tua ?? '-',
                
                'foto'         => $atlet->foto_url
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

        // AMBIL DATA ABSENSI (Hanya yang Statusnya 'H' / Hadir)
        // Kita ambil data tahun ini agar relevan
        $absensis = Absensi::where('atlet_id', $atlet->id)
                           ->where('status', 'H')
                           ->whereYear('created_at', date('Y')) // Filter Tahun Ini
                           ->get();

        $totalLatihan = $absensis->count();

        if ($totalLatihan == 0) {
            return response()->json([
                'success' => true,
                'message' => 'Belum ada data latihan',
                'summary' => null
            ]);
        }

        // HITUNG RATA-RATA (AUTOMATIC CALCULATION)
        // avg() adalah fungsi bawaan Laravel Collection yang canggih
        $avgDribble = round($absensis->avg('nilai_dribble') ?? 0);
        $avgPass    = round($absensis->avg('nilai_pass') ?? 0);
        $avgShoot   = round($absensis->avg('nilai_shoot') ?? 0);
        $avgIQ      = round($absensis->avg('nilai_iq') ?? 0);

        // Menghitung Rata-rata Total Skill (Overall)
        $overall = round(($avgDribble + $avgPass + $avgShoot + $avgIQ) / 4);

        return response()->json([
            'success' => true,
            'data' => [
                'total_kehadiran' => $totalLatihan,
                'periode' => 'Tahun ' . date('Y'),
                'overall_score' => $overall,
                'detail' => [
                    // Kita format untuk grafik di Flutter nanti
                    ['kategori' => 'Dribble', 'nilai' => $avgDribble],
                    ['kategori' => 'Passing', 'nilai' => $avgPass],
                    ['kategori' => 'Shooting', 'nilai' => $avgShoot],
                    ['kategori' => 'Game IQ', 'nilai' => $avgIQ],
                ],
                'catatan_pelatih' => "Tingkatkan terus latihanmu! Nilai ini diambil dari rata-rata $totalLatihan sesi latihan terakhir."
            ]
        ]);
    }
}