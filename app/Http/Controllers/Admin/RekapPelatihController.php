<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Pelatih;
use App\Models\Jadwal;
use App\Models\Absensi;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF

class RekapPelatihController extends Controller
{
    // Siapkan array bulan untuk dipakai di semua fungsi
    private $namaBulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    // Fungsi Pembantu untuk menghitung data rekap
    private function hitungRekap($bulan, $tahun, $pelatih_id = null)
    {
        $pelatihs = $pelatih_id ? Pelatih::where('id', $pelatih_id)->get() : Pelatih::where('status', 'Aktif')->get();
        $rekapData = [];
        $hariIni = Carbon::today();

        foreach ($pelatihs as $pelatih) {
            $jadwals = Jadwal::where('pelatih_id', $pelatih->id)
                             ->whereMonth('tanggal', $bulan)
                             ->whereYear('tanggal', $tahun)
                             ->get();

            $hadir = 0; $hangus = 0; $belumMulai = 0;

            foreach ($jadwals as $jadwal) {
                if (Absensi::where('jadwal_id', $jadwal->id)->exists()) {
                    $hadir++;
                } else {
                    Carbon::parse($jadwal->tanggal)->isBefore($hariIni) ? $hangus++ : $belumMulai++;
                }
            }

            $totalKewajiban = $hadir + $hangus;
            $persentase = $totalKewajiban > 0 ? round(($hadir / $totalKewajiban) * 100) : 0;

            $rekapData[] = [
                'id'           => $pelatih->id, // PENTING: Untuk parameter URL cetak PDF
                'nama'         => $pelatih->nama_lengkap,
                'total_jadwal' => $jadwals->count(),
                'hadir'        => $hadir,
                'hangus'       => $hangus,
                'belum_mulai'  => $belumMulai,
                'persentase'   => $persentase
            ];
        }
        return $rekapData;
    }

    // 1. TAMPILAN HALAMAN INDEX
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        $rekapData = $this->hitungRekap($bulan, $tahun);
        $namaBulan = $this->namaBulan;

        return view('admin.rekap_pelatih.index', compact('rekapData', 'bulan', 'tahun', 'namaBulan'));
    }

    // 2. CETAK PDF SEMUA PELATIH
    public function cetakPdfSemua(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        $rekapData = $this->hitungRekap($bulan, $tahun);
        $namaBln = $this->namaBulan[$bulan];

        $pdf = Pdf::loadView('admin.rekap_pelatih.pdf_semua', compact('rekapData', 'namaBln', 'tahun'));
        
        // STREAM = PREVIEW DI BROWSER
        return $pdf->stream('Rekap_Kehadiran_Semua_Pelatih_'.$namaBln.'_'.$tahun.'.pdf'); 
    }

    // 3. CETAK PDF PER PELATIH
    public function cetakPdfPelatih(Request $request, $id)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        $rekapData = $this->hitungRekap($bulan, $tahun, $id);
        
        if(empty($rekapData)) abort(404);

        $dataPelatih = $rekapData[0]; // Ambil data index pertama
        $namaBln = $this->namaBulan[$bulan];

        $pdf = Pdf::loadView('admin.rekap_pelatih.pdf_pelatih', compact('dataPelatih', 'namaBln', 'tahun'));
        
        return $pdf->stream('Rekap_Kinerja_'.$dataPelatih['nama'].'_'.$namaBln.'_'.$tahun.'.pdf');
    }

    // 4. HALAMAN PREVIEW PDF SEMUA (DI DALAM ADMIN)
    public function previewSemua(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        // URL asli stream PDF-nya
        $pdfUrl = route('admin.rekap-pelatih.cetak-semua', ['bulan' => $bulan, 'tahun' => $tahun]);
        $title = "Preview Rekap Keseluruhan";

        return view('admin.rekap_pelatih.preview', compact('pdfUrl', 'title', 'bulan', 'tahun'));
    }

    // 5. HALAMAN PREVIEW PDF PER PELATIH (DI DALAM ADMIN)
    public function previewPelatih(Request $request, $id)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));
        
        // URL asli stream PDF-nya
        $pdfUrl = route('admin.rekap-pelatih.cetak-pelatih', ['id' => $id, 'bulan' => $bulan, 'tahun' => $tahun]);
        $title = "Preview Kinerja Pelatih";

        return view('admin.rekap_pelatih.preview', compact('pdfUrl', 'title', 'bulan', 'tahun'));
    }
}