<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // WAJIB DITAMBAHKAN untuk fungsi query Kategori

// Import Model yang dibutuhkan
use App\Models\Atlet;
use App\Models\Tagihan;
use App\Models\Pembayaran; 

class DashboardController extends Controller
{
    // WAJIB tambahkan Request $request di sini untuk menangkap aksi Filter
    public function index(Request $request)
    {
        /**
         * ============================================
         * DASHBOARD OWNER (EXECUTIVE MODE)
         * Fokus: Monitoring, Grafik Tren, & Keuangan
         * ============================================
         */

        // ---------------------------------------------------------
        // 0. TANGKAP INPUT FILTER DARI VIEW (Menggunakan Input Month)
        // ---------------------------------------------------------
        // Format input type="month" adalah "YYYY-MM" (contoh: "2026-05")
        $periode = $request->input('periode');
        
        // Jika periode kosong (misal baru pertama buka / tombol reset diklik), pakai bulan ini
        if (!$periode) {
            $periode = date('Y-m');
        }

        // Pecah periode menjadi Tahun dan Bulan untuk digunakan di query bawahnya
        $filter_tahun = substr($periode, 0, 4); // Ambil 4 digit pertama (Tahun)
        $filter_bulan = substr($periode, 5, 2); // Ambil 2 digit terakhir (Bulan)

        // ---------------------------------------------------------
        // 1. KARTU STATISTIK (ANGKA RINGKASAN)
        // ---------------------------------------------------------
        
        // Total Atlet Aktif
        $atlet_aktif = Atlet::where('status', 'Aktif')->count();

        // Total Pemasukan (Diambil dari tabel Tagihan yang Lunas agar akurat)
        $total_pemasukan = Tagihan::where('status', 'Lunas')->sum('nominal');

        // Total Piutang (Uang yang belum masuk)
        $tagihan_belum_lunas = Tagihan::whereIn('status', ['Belum Lunas', 'Menunggu Verifikasi'])
                                      ->sum('nominal');

        // ---------------------------------------------------------
        // 2. DATA UNTUK GRAFIK "TREN PEMASUKAN BULANAN" (Line Chart)
        // ---------------------------------------------------------
        $income_data = [];
        $bulan_label = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $bulan_label[] = Carbon::create()->month($i)->translatedFormat('F');
            
            // PERBAIKAN: Gunakan $filter_tahun agar grafik berubah saat difilter
            $total_bulan_ini = Tagihan::where('status', 'Lunas')
                                ->whereYear('updated_at', $filter_tahun)
                                ->whereMonth('updated_at', $i)
                                ->sum('nominal');
            
            $income_data[] = $total_bulan_ini;
        }

        // ---------------------------------------------------------
        // 3. RINCIAN TAGIHAN BULANAN (Sesuai Filter) untuk di bawah Line Chart
        // ---------------------------------------------------------
        // Hitung berapa yang SUDAH bayar (Lunas) di bulan & tahun filter
        $sudah_bayar = Tagihan::whereMonth('created_at', $filter_bulan)
                              ->whereYear('created_at', $filter_tahun)
                              ->where('status', 'Lunas')
                              ->count();

        // PERBAIKAN LOGIKA: Patokan Total Atlet Bulan ini adalah jumlah Atlet Aktif (agar sinkron)
        $total_tagihan_bulan = $atlet_aktif;

        // Belum bayar adalah sisanya (Total Atlet Aktif - Yang Sudah Bayar)
        // max(0, ...) digunakan agar angka tidak minus jika ada error data
        $belum_bayar = max(0, $total_tagihan_bulan - $sudah_bayar);

        // ---------------------------------------------------------
        // 4. DATA UNTUK GRAFIK "DISTRIBUSI KATEGORI" (Donut Chart)
        // ---------------------------------------------------------
        // Menghitung jumlah atlet aktif berdasarkan kolom 'kategori'
        // Catatan: Jika namakolom di database adalah 'kategori_umur', sesuaikan tulisan 'kategori' di bawah ini
        $kategori_data = Atlet::select('kategori', DB::raw('count(*) as total'))
                              ->where('status', 'Aktif')
                              ->groupBy('kategori')
                              ->get();

        // Dipecah menjadi array agar bisa dibaca oleh Chart.js di View
        $donut_labels = $kategori_data->pluck('kategori')->toArray();
        $donut_data = $kategori_data->pluck('total')->toArray();

        // ---------------------------------------------------------
        // 5. RIWAYAT TRANSAKSI TERBARU (Tabel Bawah)
        // ---------------------------------------------------------
        // Ambil dari Tagihan yang sudah lunas, urutkan dari yang terbaru diverifikasi
        $riwayat_terbaru = Tagihan::with(['atlet.user']) // Pastikan relasi atlet dan user dimuat
                            ->where('status', 'Lunas')
                            ->orderBy('updated_at', 'desc')
                            ->take(5)
                            ->get();

        // ---------------------------------------------------------
        // 6. KIRIM SEMUA VARIABEL KE VIEW
        // ---------------------------------------------------------
        return view('owner.dashboard', compact(
            'periode',              // Variabel BARU untuk value input form filter tipe Month
            'filter_bulan',         // Wajib untuk select option form filter (meski sekarang pakai input month)
            'filter_tahun',         // Wajib untuk select option form filter
            'atlet_aktif', 
            'total_pemasukan', 
            'tagihan_belum_lunas',
            'income_data',          // Wajib untuk Grafik Garis
            'bulan_label',          // Wajib untuk Grafik Garis
            'sudah_bayar',          // Rincian kotak sudah bayar
            'belum_bayar',          // Rincian kotak belum bayar
            'total_tagihan_bulan',  // Rincian kotak total tagihan
            'donut_labels',         // Label kategori (KU-10, KU-12, dll)
            'donut_data',           // Angka total per kategori
            'riwayat_terbaru'       // Wajib untuk Tabel Riwayat
        ));
    }
}