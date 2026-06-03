<?php

namespace App\Http\Controllers\Atlet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan import ini ada untuk cetak PDF

// Import Model
use App\Models\Atlet;
use App\Models\ProgresAtlet;
use App\Models\Absensi;
use App\Models\Tagihan;
use App\Models\Notifikasi;
use App\Models\Jadwal; // <--- [TAMBAHAN] Wajib ada untuk mengecek materi tertinggal

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. CARI DATA ATLET
        $atlet = Atlet::where('user_id', $user->id)->first();

        // Error Handling jika atlet belum connect
        if (!$atlet) {
            return view('atlet.dashboard', [
                'atlet' => null, 'error' => 'Data profil belum terhubung.',
                'hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0,
                'chart_labels' => [], 'data_teknik' => [], 'data_iq' => [], 
                'tagihan_pending' => 0, 'notifikasis' => collect(), 'riwayat_latihan' => collect(),
                'materiTertinggal' => 0 // <--- [TAMBAHAN]
            ]);
        }

        // ========================================================
        // [TAMBAHAN MISI 4] CEK MATERI TERTINGGAL KHUSUS ATLET INI
        // ========================================================
        $tanggalPatokan = $atlet->tanggal_gabung ?? $atlet->created_at;
        $materiTertinggal = Jadwal::where('kategori', $atlet->kategori)
            ->whereDate('tanggal', '<', $tanggalPatokan)
            ->where('status', '!=', 'Dibatalkan')
            ->count();
        // ========================================================

        // 2. STATISTIK ABSENSI
        $hadir = Absensi::where('atlet_id', $atlet->id)->where('status', 'H')->count();
        $sakit = Absensi::where('atlet_id', $atlet->id)->where('status', 'S')->count();
        $izin  = Absensi::where('atlet_id', $atlet->id)->where('status', 'I')->count();
        $alpha = Absensi::where('atlet_id', $atlet->id)->where('status', 'A')->count();

        // 3. DATA GRAFIK (LIVE DARI ABSENSI HARIAN)
        // Kita ambil data dari Absensi biar grafik update tiap hari
        $data_grafik = Absensi::with('jadwal')
                            ->where('atlet_id', $atlet->id)
                            ->where('status', 'H') // Hanya ambil yang hadir
                            ->whereNotNull('nilai_dribble') // Pastikan ada nilainya
                            ->orderBy('created_at', 'asc') // Urutkan dari lama ke baru (ke kanan)
                            ->take(10) 
                            ->get();

        $chart_labels = [];
        $data_teknik = []; // Rata-rata Skill
        $data_iq = [];     // Mental/IQ

        foreach ($data_grafik as $log) {
            // Label Tanggal
            $tgl = $log->jadwal ? Carbon::parse($log->jadwal->tanggal) : $log->created_at;
            $chart_labels[] = $tgl->format('d M');

            // Hitung Rata-rata Teknik (Dribble + Pass + Shoot)
            $total_skill = ($log->nilai_dribble + $log->nilai_pass + $log->nilai_shoot);
            $avg_teknik = $total_skill > 0 ? round($total_skill / 3) : 0;
            
            $data_teknik[] = $avg_teknik;

            // Ambil Nilai IQ
            $data_iq[] = $log->nilai_iq ?? 0;
        }

        // 4. CEK TAGIHAN
        $tagihan_pending = Tagihan::where('atlet_id', $atlet->id)->where('status', 'Belum Lunas')->count();

        // 5. NOTIFIKASI
        $notifikasis = Notifikasi::where(function($q) use ($atlet) {
                                    $q->where('user_id', $atlet->user_id)->orWhere('kategori', 'all');
                                })->orderBy('created_at', 'desc')->take(3)->get();

        // 6. RIWAYAT LATIHAN (TABEL BAWAH)
        $riwayat_latihan = Absensi::with('jadwal')
                                ->where('atlet_id', $atlet->id)
                                ->orderBy('created_at', 'desc') // Urutkan dari baru ke lama
                                ->take(10)
                                ->get();

        return view('atlet.dashboard', compact(
            'atlet', 
            'hadir', 'sakit', 'izin', 'alpha',
            'chart_labels', 'data_teknik', 'data_iq', 
            'tagihan_pending',
            'notifikasis',
            'riwayat_latihan',
            'materiTertinggal' // <--- [TAMBAHAN] Dikirim ke view
        ));
    }

    /**
     * FITUR CETAK RAPOR / PORTFOLIO (PDF)
     */
    public function cetakRapor()
    {
        $user = Auth::user();
        $atlet = Atlet::where('user_id', $user->id)->firstOrFail();

        // 1. Riwayat Harian
        $riwayat_latihan = Absensi::with('jadwal')
                                ->where('atlet_id', $atlet->id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        // 2. Keuangan
        $tagihans = Tagihan::where('atlet_id', $atlet->id)->orderBy('tanggal_tagihan', 'desc')->get();
        // Pakai 'nominal' sesuai perbaikan database kita
        $total_tunggakan = Tagihan::where('atlet_id', $atlet->id)->where('status', 'Belum Lunas')->sum('nominal');

        // 3. Statistik
        $hadir = Absensi::where('atlet_id', $atlet->id)->where('status', 'H')->count();
        $sakit = Absensi::where('atlet_id', $atlet->id)->where('status', 'S')->count();
        $izin  = Absensi::where('atlet_id', $atlet->id)->where('status', 'I')->count();
        $alpha = Absensi::where('atlet_id', $atlet->id)->where('status', 'A')->count();

        // 4. DATA RAPOR BULANAN (EVALUASI PELATIH) - INI YANG BARU
        // Ambil 1 data progres paling baru untuk ditampilkan di PDF bagian bawah
        $last_progres = ProgresAtlet::where('atlet_id', $atlet->id)
                                    ->orderBy('tanggal', 'desc')
                                    ->first();

        // Load View PDF
        $pdf = Pdf::loadView('atlet.pdf_rapor', compact(
            'atlet', 'riwayat_latihan', 'tagihans', 'total_tunggakan', 
            'hadir', 'sakit', 'izin', 'alpha',
            'last_progres' // <-- Wajib dikirim agar PDF tidak error
        ));

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Rapor_Atlet_' . $atlet->nama_lengkap . '.pdf');
    }

    /**
     * MENAMPILKAN HALAMAN PREVIEW (FRAME)
     */
    public function previewRapor()
    {
        // Hanya memanggil view pembungkus
        return view('atlet.preview_rapor');
    }
}