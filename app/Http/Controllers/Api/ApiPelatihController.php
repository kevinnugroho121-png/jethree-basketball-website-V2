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

        // Data default ambil dari User Login (Disesuaikan Hak Istimewa Owner)
        $data = [
            'nama_lengkap' => $user->name, 
            'email'        => $user->email, 
            'role'         => $user->role === 'owner' ? 'PELATIH DAN OWNER' : 'PELATIH',
            'no_hp'        => '-',
            'alamat'       => '-',
            'foto'         => null,
            'lisensi'      => '-',
            'tempat_lahir' => '-',
            'tanggal_lahir'=> '-',
            'status'       => $user->role === 'owner' ? 'Aktif' : 'Non-Aktif', 
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
            
            // 💡 SUNTIKAN BARU: Masukkan data dari tabel pelatihs agar terkirim ke aplikasi mobile
            $data['kategori_fokus'] = $pelatih->kategori_fokus ?? '';
            $data['gender_fokus']   = $pelatih->gender_fokus ?? '';
            
            // Khusus owner, status akun wajib terkunci 'Aktif'
            $data['status']        = $user->role === 'owner' ? 'Aktif' : ($pelatih->status ?? 'Non-Aktif');
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

        // Jika data pelatih kosong dan BUKAN owner, baru tolak
        if (!$pelatih && $user->role !== 'owner') {
             return response()->json([
                'success' => true,
                'pelatih_nama' => $namaPelatih, 
                'jadwal_hari_ini' => null,
                'message' => 'Profil pelatih belum lengkap'
            ]);
        }

        // Ambil Jadwal Hari Ini
        Carbon::setLocale('id'); 
        $todayDate = Carbon::now()->format('Y-m-d');
        
        // --- STRATEGI AMBIL JADWAL HARI INI BERDASARKAN ROLE ---
        if ($user->role === 'owner') {
            // Owner melihat jadwal pertama apa saja yang ada di akademi hari ini secara global
            $jadwal_hari_ini = Jadwal::where('tanggal', $todayDate)->first();
            
            if (!$jadwal_hari_ini) {
                $namaHari = Carbon::now()->isoFormat('dddd');
                $jadwal_hari_ini = Jadwal::where('hari', $namaHari)->first();
            }
        } else {
            // Pelatih biasa dikunci berdasarkan kategori diampunya
            $kategoriDiampu = $pelatih->kategoris()->pluck('kategori')->toArray();
            
            $queryHariIni = Jadwal::where('pelatih_id', $pelatih->id)->where('tanggal', $todayDate);
            
            if (!empty($kategoriDiampu)) {
                $queryHariIni->whereIn('kategori', $kategoriDiampu);
            }
            
            $jadwal_hari_ini = $queryHariIni->first();

            if (!$jadwal_hari_ini) {
                 $namaHari = Carbon::now()->isoFormat('dddd');
                 $jadwal_hari_ini = Jadwal::where('pelatih_id', $pelatih->id)
                                           ->whereIn('kategori', $kategoriDiampu)
                                           ->where('hari', $namaHari)
                                           ->first();
            }
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




        // Jika data pelatih kosong dan BUKAN owner, baru kembalikan data kosong
        if (!$pelatih && $user->role !== 'owner') {
             return response()->json(['success' => true, 'data' => []]);
        }

        $namaUntukJadwal = $pelatih ? $pelatih->nama_lengkap : $user->name;
        $isAll = $request->query('all') == 'true'; 
        $today = \Carbon\Carbon::now('Asia/Jakarta')->toDateString();

        // --- STRATEGI KUERI BERDASARKAN ROLE (Poin 62) ---
        if ($user->role === 'owner') {
            // Khusus Owner: Buka gembok akses, tarik semua jadwal latihan tanpa filter kategori/id pelatih
            $query = Jadwal::query();

        } else {
            // Ambil daftar kategori dari tabel jembatan
            $kategoriDiampu = $pelatih->kategoris()->pluck('kategori')->toArray();
            
            // Query dasar: Ambil jadwal yang emang jatah ID pelatih ini
            $query = Jadwal::where('pelatih_id', $pelatih->id);
            
            // SMART FALLBACK: Jika admin sudah memetakan kategori, filter secara ketat. 
            // Jika belum (data lama/belum disetting), loloskan filter whereIn agar tidak blank.
            if (!empty($kategoriDiampu)) {
                $query->whereIn('kategori', $kategoriDiampu);
            }
        }

        // --- SIHIR KUERI AUTO-SORT & HITUNG REKAP BUNDLING ---
        $query->addSelect(['jadwals.*',
            
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
            'deskripsi_latihan' => 'required|string', // <--- TAMBAHKAN INI (Gembok penolak simpan jika kosong)
        ]);


        try {
            DB::beginTransaction();

            $jadwal = Jadwal::find($request->jadwal_id);
            if (!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Jadwal tidak valid'], 404);
            }

            // Otomatis update deskripsi latihan di tabel jadwal agar bisa diintip oleh atlet (Poin 13)
            $jadwal->update([
                'materi' => $request->deskripsi_latihan
            ]);

            // Gunakan tanggal dari jadwal, bukan tanggal hari ini
            // Ini penting agar absen jadwal susulan tetap masuk ke tanggal yang benar
            $tanggalAbsen = $jadwal->tanggal;

            // Cek absen unik berdasarkan jadwal_id & atlet_id
            $absen = Absensi::where('jadwal_id', $request->jadwal_id)
                            ->where('atlet_id', $request->atlet_id)
                            ->first();

            // ==================== KODE YANG BENAR (GANTI DENGAN INI) ====================
            if (!$absen) {
                $absen = new Absensi();
                $absen->jadwal_id = $request->jadwal_id;
                $absen->atlet_id = $request->atlet_id;
            }

            // --- DETEKSI DINAMIS SIAPA YANG MELATIH DI LAPANGAN ---
            $userLog = $request->user(); 
            $pelatihHadirId = null;

            // 1. Jika Coach Irul memilih dari dropdown Flutter
            if ($request->has('pelatih_id') && $request->pelatih_id != null) {
                $pelatihHadirId = $request->pelatih_id;
            } 
            // 2. Jika pelatih biasa (dropdown sembunyi), otomatis ambil dari session loginnya
            else {
                $pelatihAsli = Pelatih::where('user_id', $userLog->id)->first();
                $pelatihHadirId = $pelatihAsli ? $pelatihAsli->id : null;
            }

            // Update data
            $absen->tanggal_latihan = $tanggalAbsen; 
            $absen->status = $request->status;
            
            // SUNTIKAN FIELD BARU: Catat pelatih yang bereksekusi di lapangan hari ini
            $absen->pelatih_hadir_id = $pelatihHadirId; 
// ============================================================================
            
            // Cek apakah sekarang sudah memasuki akhir bulan (Mulai tanggal 25 s/d akhir bulan)
            $hariIni = \Carbon\Carbon::now('Asia/Jakarta')->day;
            $isAkhirBulan = ($hariIni >= 25);

            if ($request->status == 'H') {
                // Nilai angka hanya disimpan jika diinput di tanggal 25 ke atas (Poin 14)
                $absen->nilai_dribble = $isAkhirBulan ? $request->dribble : null;
                $absen->nilai_pass    = $isAkhirBulan ? $request->pass : null;
                $absen->nilai_shoot   = $isAkhirBulan ? $request->shoot : null;
                $absen->nilai_iq      = $isAkhirBulan ? $request->iq : null;
            } else {
                $absen->nilai_dribble = null;
                $absen->nilai_pass    = null;
                $absen->nilai_shoot   = null;
                $absen->nilai_iq      = null;
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