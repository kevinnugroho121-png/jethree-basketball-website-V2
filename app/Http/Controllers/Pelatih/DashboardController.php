<?php

namespace App\Http\Controllers\Pelatih;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Jadwal;
use App\Models\Pelatih;
use App\Models\Notifikasi; // [BARU] Jangan lupa import ini
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. CARI DATA PELATIH (Berdasarkan User Login)
        $pelatih = Pelatih::where('user_id', $user->id)->first();

        // Siapkan variabel default jika data pelatih belum di-link
        if (!$pelatih) {
            return view('pelatih.dashboard', [
                'error' => 'Data profil Anda belum terhubung. Hubungi Admin.',
                'jadwal_hari_ini' => collect(),
                'jadwal_mendatang' => collect(),
                'notifikasis' => collect(), // Kirim koleksi kosong biar gak error
                'total_sesi' => 0,
                'sesi_bulan_ini' => 0
            ]);
        }

        // 2. AMBIL JADWAL (Hanya milik pelatih ini)
        // a. Jadwal Hari Ini
        $jadwal_hari_ini = Jadwal::where('pelatih_id', $pelatih->id)
                            ->whereDate('tanggal', Carbon::today())
                            ->orderBy('jam_mulai', 'asc')
                            ->get();

        // b. Jadwal Mendatang
        $jadwal_mendatang = Jadwal::where('pelatih_id', $pelatih->id)
                            ->whereDate('tanggal', '>=', Carbon::today())
                            ->orderBy('tanggal', 'asc')
                            ->get();

        // 3. HITUNG STATISTIK
        $total_sesi = $jadwal_mendatang->count();
        $sesi_bulan_ini = Jadwal::where('pelatih_id', $pelatih->id)
                            ->whereMonth('tanggal', Carbon::now()->month)
                            ->count();

        // 4. AMBIL NOTIFIKASI (Revisi Filter Kategori Jadwal)
        // Cek dulu apakah tabel/model Notifikasi ada untuk mencegah error
        $notifikasis = collect(); 
        if(class_exists(Notifikasi::class)) {
            $notifikasis = Notifikasi::where(function($query) {
                                // Grupkan role agar filter tidak bocor
                                $query->where('target_role', 'all')
                                      ->orWhere('target_role', 'pelatih');
                            })
                            ->where('kategori', 'jadwal') // [KUNCI REVISI] Hanya tarik kategori jadwal
                            ->where('created_at', '>=', now()->subHours(24)) // Maksimal 24 Jam terakhir
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
        }

        // 5. KIRIM SEMUA KE VIEW
        return view('pelatih.dashboard', compact(
            'pelatih', 
            'jadwal_hari_ini', 
            'jadwal_mendatang', 
            'total_sesi', 
            'sesi_bulan_ini',
            'notifikasis' // [BARU] Data notifikasi dikirim
        ));
    }
}