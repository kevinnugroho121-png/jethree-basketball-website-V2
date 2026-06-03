<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Jadwal;
use App\Models\Absensi;
use App\Models\Atlet;
use App\Models\Pelatih;

class ApiPelatihController extends Controller
{
    // 1. PROFIL PELATIH (LENGKAP)
    public function profile()
    {
        $user = Auth::user();
        $pelatih = Pelatih::where('user_id', $user->id)->first();

        // Data default ambil dari User Login
        $data = [
            'nama_lengkap' => $user->name, 
            'email'        => $user->email, // PENTING: Untuk Tampilan Mobile
            'role'         => 'PELATIH',
            'no_hp'        => '-',
            'alamat'       => '-',
            'foto'         => null,
            'lisensi'      => '-',
            'tempat_lahir' => '-',
            'tanggal_lahir'=> '-',
            'status'       => 'Non-Aktif', // PENTING: Default status
        ];

        // Jika data di tabel pelatih ada, timpa data default
        if ($pelatih) {
            $data['nama_lengkap']  = $pelatih->nama_lengkap ?? $user->name;
            $data['no_hp']         = $pelatih->no_hp ?? '-';
            $data['alamat']        = $pelatih->alamat ?? '-';
            $data['foto']          = $pelatih->foto_profil ?? null; 
            $data['lisensi']       = $pelatih->lisensi ?? '-';
            $data['tempat_lahir']  = $pelatih->tempat_lahir ?? '-';
            $data['tanggal_lahir'] = $pelatih->tanggal_lahir ?? '-';
            $data['status']        = $pelatih->status ?? 'Non-Aktif'; // Ambil status real
        }

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    // 2. DASHBOARD PELATIH
    public function dashboard()
    {
        $user = Auth::user();
        $pelatih = Pelatih::where('user_id', $user->id)->first();

        $namaPelatih = $pelatih ? $pelatih->nama_lengkap : $user->name;

        if (!$pelatih) {
             return response()->json([
                'success' => true,
                'pelatih_nama' => $namaPelatih, 
                'jadwal_hari_ini' => null,
                'message' => 'Profil pelatih belum lengkap'
            ]);
        }

        // Ambil Jadwal Hari Ini
        Carbon::setLocale('id'); // Pastikan server support locale ID
        
        // Menggunakan format tanggal Y-m-d untuk akurasi lebih baik daripada nama hari
        $todayDate = Carbon::now()->format('Y-m-d');
        
        $jadwal_hari_ini = Jadwal::where('pelatih_id', $pelatih->id)
                                 ->where('tanggal', $todayDate) // Ubah 'hari' jadi 'tanggal' agar lebih presisi
                                 ->first();

        // Fallback: Jika pakai kolom 'hari', pastikan nama harinya sesuai (Senin, Selasa, dll)
        if (!$jadwal_hari_ini) {
             $namaHari = Carbon::now()->isoFormat('dddd');
             $jadwal_hari_ini = Jadwal::where('pelatih_id', $pelatih->id)
                                      ->where('hari', $namaHari)
                                      ->first();
        }

        return response()->json([
            'success' => true,
            'pelatih_nama' => $namaPelatih,
            'jadwal_hari_ini' => $jadwal_hari_ini ? [
                'id' => $jadwal_hari_ini->id,
                'kategori' => $jadwal_hari_ini->kategori,
                'lokasi' => $jadwal_hari_ini->lokasi,
                'jam' => substr($jadwal_hari_ini->jam_mulai, 0, 5) . ' - ' . substr($jadwal_hari_ini->jam_selesai, 0, 5),
            ] : null
        ]);
    }

    // 3. LIST SEMUA JADWAL (MENDUKUNG PAGINASI & KALENDER)
    public function listJadwal(Request $request)
    {
        $user = Auth::user();
        $pelatih = Pelatih::where('user_id', $user->id)->first();

        if (!$pelatih) {
             return response()->json(['success' => true, 'data' => []]);
        }

        $namaUntukJadwal = $pelatih->nama_lengkap;
        
     
     
        // [PERUBAHAN]: Cek permintaan kalender (?all=true)
        $isAll = $request->query('all') == 'true'; 
        
        $today = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();

        // --- SIHIR KUERI AUTO-SORT & HITUNG REKAP ---
        $query = Jadwal::where('pelatih_id', $pelatih->id)
            ->addSelect(['jadwals.*', 
                'total_anak' => \App\Models\Atlet::selectRaw('COUNT(*)')
                    ->whereColumn('atlets.kategori', 'jadwals.kategori')
                    ->where('atlets.status', 'Aktif'),
                
                'total_hadir' => \App\Models\Absensi::selectRaw('COUNT(*)')
                    ->whereColumn('absensis.jadwal_id', 'jadwals.id')
                    ->where('absensis.status', 'H'),
                    
                'total_diabsen' => \App\Models\Absensi::selectRaw('COUNT(*)')
                    ->whereColumn('absensis.jadwal_id', 'jadwals.id')
            ])
            ->selectRaw("IF(DATE(tanggal) = '$today', 1, 0) as is_today")
            ->orderByRaw("IF(DATE(tanggal) = '$today', 1, 0) DESC")
            ->orderByRaw("CASE WHEN DATE(tanggal) > '$today' THEN 1 ELSE 2 END")
            ->orderBy('tanggal', 'desc');
        
        // Jika minta semua (untuk kalender), pakai get(). Jika tidak, pakai paginate(10)
        $jadwals = $isAll ? $query->get() : $query->paginate(2);




        // Fungsi modifikasi data (sama seperti sebelumnya)
        $mapFunction = function($j) use ($namaUntukJadwal) {
            return [
                'id' => $j->id,
                'hari' => Carbon::parse($j->tanggal)->isoFormat('dddd'), 
                'tanggal' => $j->tanggal, 
                'tanggal_indo' => Carbon::parse($j->tanggal)->isoFormat('D MMMM Y'), 
                'kategori' => $j->kategori, 
                'jam' => substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5),
                'lokasi' => $j->lokasi,
                'materi' => $j->materi ?? '-',
                'pelatih_nama' => $namaUntukJadwal,
                'status_jadwal' => $j->status ?? 'Belum Selesai',
                // --- PAKET DATA KHUSUS MOBILE (DIBACA OLEH FLUTTER) ---
                'is_today'      => $j->is_today ?? 0,
                'total_anak'    => $j->total_anak ?? 0,
                'total_hadir'   => $j->total_hadir ?? 0,
                'total_diabsen' => $j->total_diabsen ?? 0,
            ];
        };

        // Terapkan logika ke data (Beda cara untuk paginasi vs non-paginasi)
        if ($isAll) {
            $data = $jadwals->map($mapFunction);
        } else {
            $jadwals->getCollection()->transform($mapFunction);
            $data = $jadwals;
        }

        return response()->json(['success' => true, 'data' => $data]);
    }

    // 4. AMBIL DAFTAR ATLET DI JADWAL TERTENTU
    public function getAtletByJadwal($jadwal_id)
    {
        $jadwal = Jadwal::find($jadwal_id);
        if (!$jadwal) return response()->json(['success' => false, 'message' => 'Jadwal tidak ditemukan'], 404);

        // Ambil tanggal jadwal tersebut, BUKAN tanggal hari ini
        // Agar pelatih bisa absen untuk jadwal kemarin/besok
        $tanggalJadwal = $jadwal->tanggal; 

        $atlets = Atlet::where('kategori', $jadwal->kategori) // Filter sesuai kategori jadwal (KU-10, dll)
                        ->where('status', 'Aktif') 
                        ->orderBy('nama_lengkap', 'asc')
                        ->get()
                        ->map(function($atlet) use ($jadwal_id, $tanggalJadwal) {
                            
                            // Cek Absensi berdasarkan ID JADWAL dan ID ATLET
                            $absen = Absensi::where('atlet_id', $atlet->id)
                                            ->where('jadwal_id', $jadwal_id)
                                            ->first();
                            
                            return [
                                'id' => $atlet->id,
                                'nama' => $atlet->nama_lengkap,
                                'posisi' => $atlet->posisi,
                                'foto' => $atlet->foto_url ?? null, // Handle jika null
                                'sudah_absen' => $absen ? true : false,
                                'nilai_sebelumnya' => $absen ? [
                                    'status' => $absen->status,
                                    'dribble' => $absen->nilai_dribble,
                                    'pass' => $absen->nilai_pass,
                                    'shoot' => $absen->nilai_shoot,
                                    'iq' => $absen->nilai_iq,
                                    'catatan' => $absen->catatan
                                ] : null
                            ];
                        });

        return response()->json(['success' => true, 'data' => $atlets]);
    }

    // 5. SIMPAN ABSENSI & NILAI
    public function storeAbsensi(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required',
            'atlet_id' => 'required',
            'status' => 'required|in:H,S,I,A', 
            'dribble' => 'nullable|integer',
            'pass' => 'nullable|integer',
            'shoot' => 'nullable|integer',
            'iq' => 'nullable|integer',
            'catatan' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $jadwal = Jadwal::find($request->jadwal_id);
            if (!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Jadwal tidak valid'], 404);
            }

            // Gunakan tanggal dari jadwal, bukan tanggal hari ini
            // Ini penting agar absen jadwal susulan tetap masuk ke tanggal yang benar
            $tanggalAbsen = $jadwal->tanggal;

            // Cek absen unik berdasarkan jadwal_id & atlet_id
            $absen = Absensi::where('jadwal_id', $request->jadwal_id)
                            ->where('atlet_id', $request->atlet_id)
                            ->first();

            if (!$absen) {
                $absen = new Absensi();
                $absen->jadwal_id = $request->jadwal_id;
                $absen->atlet_id = $request->atlet_id;
            }

            // Update data
            $absen->tanggal_latihan = $tanggalAbsen; // Pastikan tanggal sesuai jadwal
            $absen->status = $request->status;
            
            if ($request->status == 'H') {
                $absen->nilai_dribble = $request->dribble;
                $absen->nilai_pass = $request->pass;
                $absen->nilai_shoot = $request->shoot;
                $absen->nilai_iq = $request->iq;
            } else {
                $absen->nilai_dribble = null;
                $absen->nilai_pass = null;
                $absen->nilai_shoot = null;
                $absen->nilai_iq = null;
            }

            $absen->catatan = $request->catatan;
            $absen->save();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }
}