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
                // 💡 FIX: Ambil kata pertama saja (Misal "KU-12 Putra" diambil "KU-12") agar cocok dengan nama kategori di tabel jadwals
                $baseKategori = explode(' ', $kategoriAtlet)[0];
                $query->where('kategori', 'LIKE', '%' . $baseKategori . '%');
            }

            // ⚡ WORKFLOW BARU: Atlet HANYA boleh melihat jadwal yang sudah di-RILIS pelatih
            $query->where('status_rilis', 'Rilis');
        }

        // --- ATURAN BARU: FILTER ROLE PELATIH VS ROLE BYPASS (OWNER/ADMIN) ---
        if ($user && $user->role === 'pelatih') {
            // Jika pelatih biasa, hanya bisa melihat jadwal miliknya sendiri
            $query->where('pelatih_id', $user->id);
        }
        // Jika $user->role adalah 'owner' atau 'admin' (Coach Irul), 
        // baris di atas akan dilewati (Bypass), sehingga semua jadwal otomatis lolos tampil semua.

        // 💡 FIX KALENDER: Jika Flutter mengirimkan parameter tanggal hasil klik kalender, filter datanya di sini
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->query('tanggal'));
        } elseif ($request->filled('date')) {
            $query->whereDate('tanggal', $request->query('date'));
        }

        // 💡 FIX PAGINASI: Hari ini teratas, lalu tanggal masa depan TERDEKAT (asc), lalu riwayat masa lalu terdekat (asc)
        $query->selectRaw("jadwals.*, IF(DATE(tanggal) = '$today', 1, 0) as is_today")
            ->orderByRaw("IF(DATE(tanggal) = '$today', 1, 0) DESC")
            ->orderByRaw("CASE WHEN DATE(tanggal) > '$today' THEN 1 WHEN DATE(tanggal) < '$today' THEN 2 ELSE 3 END ASC")
            ->orderByRaw("CASE 
                WHEN DATE(tanggal) > '$today' THEN DATEDIFF(tanggal, '$today') 
                ELSE DATEDIFF('$today', tanggal) 
              END ASC");

        // Jika minta semua, pakai get(). Jika tidak, pakai paginate(2)
        $jadwals = $isAll ? $query->get() : $query->paginate(2);

        

        // Kita simpan logika pencarian pelatih & satpam absen ke dalam Variabel Fungsi
        $mapFunction = function($item) use ($atlet) {
            
            $pelatihName = 'Tim Pelatih';

            if ($item->is_takeover) {
                // 🟢 PERBAIKAN DINAMIS: Cari tahu pelatih pengganti yang hadir mengajar hari itu di lapangan
                $sampelAbsen = DB::table('absensis')->where('jadwal_id', $item->id)->first();
                if ($sampelAbsen && $sampelAbsen->pelatih_hadir_id) {
                    $pInfo = DB::table('pelatihs')->where('id', $sampelAbsen->pelatih_hadir_id)->first();
                    $pelatihName = $pInfo ? ($pInfo->nama_lengkap ?? 'Owner Jethree') : 'Owner Jethree';
                } else {
                    $pelatihName = 'Owner Jethree'; // Fallback aman jika Owner mengajar sendiri
                }
            } else {
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

            // 🟢 LOGIKA PENANGANAN GENDER: Gunakan kategori lengkap dari profil atlet ("KU-12 Putra")
            $kategoriDisplay = ($atlet && !empty($atlet->kategori)) 
                ? $atlet->kategori 
                : trim(($item->kategori ?? '') . ' ' . ($item->gender ?? $item->jenis_kelamin ?? ''));

            return [
                'id'          => $item->id,
                'hari'        => $item->hari,
                // 💡 FIX TIMEZONE: Paksa Carbon mengembalikan teks tanggal bersih (YYYY-MM-DD) agar tidak diubah ke UTC oleh Laravel
                'tanggal'     => Carbon::parse($item->tanggal)->toDateString(),
                'jam_mulai'   => $item->jam_mulai,
                'jam_selesai' => $item->jam_selesai,
                'kategori'    => $kategoriDisplay,
                'lokasi'      => $item->lokasi,
                'pelatih'     => $pelatihName, 
                'materi'      => $item->materi ?? 'Latihan Fisik & Teknik', 
                'status_absen' => $statusAbsen, 
                
                // ⚡ REVISI POIN 3, 4 & 7: Sertakan data ringkasan latihan & video untuk atlet
                'link_youtube'   => $item->link_youtube,
                'review_latihan' => $item->review_latihan,
                'is_takeover'    => (bool) $item->is_takeover,

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