<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Kita pakai DB Facade biar aman cari tabel pelatihs
use Illuminate\Support\Facades\Auth; // Tambahan untuk deteksi user login
use Carbon\Carbon; // Tambahan untuk membandingkan tanggal jadwal

// IMPORT MODELS
use App\Models\Jadwal;
use App\Models\User; 
use App\Models\Atlet;
use App\Models\Absensi;

class JadwalController extends Controller
{
    public function index()
    {
        // 1. CARI SIAPA ATLET YANG SEDANG LOGIN SAAT INI
        $user = Auth::user();
        $atlet = null;
        
        // Kita cek, apakah yang login ini benar-benar atlet?
        if ($user && $user->role === 'atlet') {
            $atlet = Atlet::where('user_id', $user->id)->first();
        }

        // KITA PAKAI CARA MANUAL (LOOPING) AGAR SERVER TIDAK CRASH
        $jadwals = Jadwal::latest()->get()->map(function($item) use ($atlet) {
            
            // Default nama coach
            $pelatihName = 'Tim Pelatih';
            
            // Ambil ID dari kolom database (sesuai screenshot kamu: pelatih_id)
            $idDiTabelJadwal = $item->pelatih_id ?? $item->user_id;

            if ($idDiTabelJadwal) {
                // SKENARIO 1: Cek apakah ID ini ada di tabel 'pelatihs'?
                $dataPelatih = DB::table('pelatihs')->where('id', $idDiTabelJadwal)->first();

                if ($dataPelatih) {
                    $userAsli = User::find($dataPelatih->user_id);
                    if ($userAsli) {
                        $pelatihName = $userAsli->name; 
                    }
                } 
                else {
                    // SKENARIO 2: Jika tidak ada di tabel pelatihs, coba cari langsung di tabel users
                    $userLangsung = User::find($idDiTabelJadwal);
                    if ($userLangsung) {
                        $pelatihName = $userLangsung->name;
                    }
                }
            }

            // ========================================================
            // [TAMBAHAN BARU] LOGIKA "SATPAM ABSENSI" KHUSUS ATLET
            // ========================================================
            $statusAbsen = null;

            if ($atlet) {
                // 1. Cek apakah jadwal dibatalkan oleh admin
                if ($item->status == 'Dibatalkan') {
                    $statusAbsen = 'Dibatalkan';
                } 
                // 2. Cek apakah jadwal ini ada di masa depan
                elseif (Carbon::parse($item->tanggal)->isFuture()) {
                    $statusAbsen = 'Belum Dimulai';
                } 
                else {
                    // 3. Jadwal sudah lewat, mari kita periksa buku absensi!
                    
                    // Cek apakah PELATIH SUDAH MENGISI ABSEN untuk JADWAL INI?
                    // (Kita cek dari ada tidaknya SEMBARANG record absensi untuk jadwal id ini)
                    $jadwalSudahDirekap = Absensi::where('jadwal_id', $item->id)->exists();

                    if (!$jadwalSudahDirekap) {
                        // Pelatih belum ngisi absen sama sekali
                        $statusAbsen = 'Belum Direkap';
                    } else {
                        // Pelatih sudah ngisi absen. Cek status KHUSUS ANAK INI
                        $absenKu = Absensi::where('jadwal_id', $item->id)
                                          ->where('atlet_id', $atlet->id)
                                          ->first();
                        
                        if ($absenKu) {
                            $statusAbsen = $absenKu->status; // H, I, S, atau A
                        } else {
                            // Pelatih udah ngisi, tapi nama anak ini nggak ada di list (berarti Alpha)
                            $statusAbsen = 'A';
                        }
                    }
                }
            }
            // ========================================================

            return [
                'id'          => $item->id,
                'hari'        => $item->hari,
                'tanggal'     => $item->tanggal,
                'jam_mulai'   => $item->jam_mulai,
                'jam_selesai' => $item->jam_selesai,
                'kategori'    => $item->kategori,
                'lokasi'      => $item->lokasi,
                
                // HASIL PENCARIAN NAMA DI ATAS
                'pelatih'     => $pelatihName, 
                
                'materi'      => $item->materi ?? 'Latihan Fisik & Teknik', 
                
                // [TAMBAHAN BARU] KIRIM STATUS ABSEN KE FLUTTER
                'status_absen' => $statusAbsen, 
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'List Data Jadwal Berhasil',
            'data'    => $jadwals
        ], 200);
    }
}