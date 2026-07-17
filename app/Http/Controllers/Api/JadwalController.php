<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon; 

use App\Models\Jadwal;
use App\Models\User; 
use App\Models\Atlet;
use App\Models\Absensi;

class JadwalController extends Controller
{
    // [PERBAIKAN 1]: Tambahkan Request $request di dalam kurung
    public function index(Request $request)
    {
        $user = Auth::user();
        $atlet = null;
        
        if ($user && $user->role === 'atlet') {
            $atlet = Atlet::where('user_id', $user->id)->first();
        }




        // Cek apakah ada request minta "Semua Data" untuk Kalender
        $isAll = $request->query('all') == 'true';

        $today = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();
        
        $query = Jadwal::query();

        // --- FILTER KATEGORI USIA (KU) KHUSUS ATLET ---
        // Memastikan atlet hanya melihat jadwal latihan sesuai kelompok usianya sendiri
        
        
        if ($atlet) {
            // Kita ambil dari kolom "kategori" sesuai dengan struktur tabel di databasemu
            $kategoriAtlet = $atlet->kategori; 
            
            if ($kategoriAtlet) {
                $query->where('kategori', $kategoriAtlet);
            }
        }

        // --- ATURAN BARU: FILTER ROLE PELATIH VS ROLE BYPASS (OWNER/ADMIN) ---
        if ($user && $user->role === 'pelatih') {
            // Jika pelatih biasa, hanya bisa melihat jadwal miliknya sendiri
            $query->where('pelatih_id', $user->id);
        }
        // Jika $user->role adalah 'owner' atau 'admin' (Coach Irul), 
        // baris di atas akan dilewati (Bypass), sehingga semua jadwal otomatis lolos tampil semua.

        // Sihir Urutan: Jadwal Hari Ini paling atas, diikuti jadwal masa depan, lalu riwayat masa lalu
        $query->selectRaw("jadwals.*, IF(DATE(tanggal) = '$today', 1, 0) as is_today")


            ->orderByRaw("IF(DATE(tanggal) = '$today', 1, 0) DESC")
            ->orderByRaw("CASE WHEN DATE(tanggal) > '$today' THEN 1 ELSE 2 END")
            ->orderBy('tanggal', 'desc');

        // Jika minta semua, pakai get(). Jika tidak, pakai paginate(2)
        $jadwals = $isAll ? $query->get() : $query->paginate(2);

        

        // Kita simpan logika pencarian pelatih & satpam absen ke dalam Variabel Fungsi
        $mapFunction = function($item) use ($atlet) {
            
            $pelatihName = 'Tim Pelatih';
            $idDiTabelJadwal = $item->pelatih_id ?? $item->user_id;

            if ($idDiTabelJadwal) {
                $dataPelatih = DB::table('pelatihs')->where('id', $idDiTabelJadwal)->first();

                if ($dataPelatih) {
                    $userAsli = User::find($dataPelatih->user_id);
                    if ($userAsli) {
                        $pelatihName = $userAsli->name; 
                    }
                } 
                else {
                    $userLangsung = User::find($idDiTabelJadwal);
                    if ($userLangsung) {
                        $pelatihName = $userLangsung->name;
                    }
                }
            }

            // LOGIKA "SATPAM ABSENSI" KHUSUS ATLET
            $statusAbsen = null;

            if ($atlet) {
                if ($item->status == 'Dibatalkan') {
                    $statusAbsen = 'Dibatalkan';
                } 
                elseif (Carbon::parse($item->tanggal)->isFuture()) {
                    $statusAbsen = 'Belum Dimulai';
                } 
                else {
                    $jadwalSudahDirekap = Absensi::where('jadwal_id', $item->id)->exists();

                    if (!$jadwalSudahDirekap) {
                        $statusAbsen = 'Belum Direkap';
                    } else {
                        $absenKu = Absensi::where('jadwal_id', $item->id)
                                          ->where('atlet_id', $atlet->id)
                                          ->first();
                        
                        if ($absenKu) {
                            $statusAbsen = $absenKu->status; 
                        } else {
                            $statusAbsen = 'A';
                        }
                    }
                }
            }

            return [
                'id'          => $item->id,
                'hari'        => $item->hari,
                'tanggal'     => $item->tanggal,
                'jam_mulai'   => $item->jam_mulai,
                'jam_selesai' => $item->jam_selesai,
                'kategori'    => $item->kategori,
                'lokasi'      => $item->lokasi,
                'pelatih'     => $pelatihName, 
                'materi'      => $item->materi ?? 'Latihan Fisik & Teknik', 
                'status_absen' => $statusAbsen, 
                // --- TAMBAHAN DATA UNTUK FLUTTER PELATIH ---
                'is_today'      => $item->is_today,
                'total_anak'    => $item->total_anak ?? 0,
                'total_hadir'   => $item->total_hadir ?? 0,
                'total_diabsen' => $item->total_diabsen ?? 0,
            ];
        };

        // [PERBAIKAN 3]: Terapkan Modifikasi dengan aman
        if ($isAll) {
            // Jika untuk Kalender (Tanpa Paginasi)
            $data = $jadwals->map($mapFunction);
        } else {
            // Jika untuk List Bawah (Pakai Paginasi)
            $jadwals->getCollection()->transform($mapFunction);
            $data = $jadwals;
        }

        return response()->json([
            'success' => true,
            'message' => 'List Data Jadwal Berhasil',
            'data'    => $data 
        ], 200);
    }
}