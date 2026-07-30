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

        // 💡 FIX KALENDER: Jika aplikasi Flutter mengirim parameter tanggal hasil klik kalender, saring di sini
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->query('tanggal'));
        } elseif ($request->filled('date')) {
            $query->whereDate('tanggal', $request->query('date'));
        }

        // --- SIHIR KUERI AUTO-SORT & HITUNG REKAP BUNDLING ---
        $query->addSelect(['jadwals.*',
            
                // 🟢 REVISI: Menggabungkan nama kategori jadwal dengan gender fokus pelatih secara dinamis
                'total_anak' => \App\Models\Atlet::selectRaw('COUNT(*)')
                    ->where('atlets.status', 'Aktif')
                    ->whereRaw("atlets.kategori LIKE CASE 
                        WHEN (SELECT p.gender_fokus FROM pelatihs p WHERE p.id = jadwals.pelatih_id) IS NOT NULL 
                        THEN CONCAT(jadwals.kategori, ' ', (SELECT p.gender_fokus FROM pelatihs p WHERE p.id = jadwals.pelatih_id))
                        ELSE CONCAT('%', jadwals.kategori, '%')
                    END"),
                
                'total_hadir' => \App\Models\Absensi::selectRaw('COUNT(*)')
                    ->whereColumn('absensis.jadwal_id', 'jadwals.id')
                    ->where('absensis.status', 'H'),
                    
                'total_diabsen' => \App\Models\Absensi::selectRaw('COUNT(*)')
                    ->whereColumn('absensis.jadwal_id', 'jadwals.id')
            ])
            ->selectRaw("IF(DATE(tanggal) = '$today', 1, 0) as is_today")
            ->orderByRaw("IF(DATE(tanggal) = '$today', 1, 0) DESC")
            ->orderByRaw("CASE WHEN DATE(tanggal) > '$today' THEN 1 WHEN DATE(tanggal) < '$today' THEN 2 ELSE 3 END ASC")
            ->orderByRaw("CASE 
                WHEN DATE(tanggal) > '$today' THEN DATEDIFF(tanggal, '$today') 
                ELSE DATEDIFF('$today', tanggal) 
              END ASC");
        
        // Jika minta semua (untuk kalender), pakai get(). Jika tidak, pakai paginate(10)
        $jadwals = $isAll ? $query->get() : $query->paginate(2);




        // Fungsi modifikasi data (DIPERBAIKI AGAR MENDUKUNG GENDER & NAMA COACH DINAMIS)
        $mapFunction = function($j) use ($namaUntukJadwal) {
            // 💡 FIX GENDER: Ambil data gender_fokus dari database secara dinamis
            $genderSuffix = '';
            $displayCoachName = $namaUntukJadwal;

            if ($j->pelatih_id) {
                $pInfo = DB::table('pelatihs')->where('id', $j->pelatih_id)->first();
                if ($pInfo) {
                    // Cek jika field gender_fokus di database terisi (Putra/Putri)
                    if (!empty($pInfo->gender_fokus)) {
                        $genderSuffix = ' ' . $pInfo->gender_fokus;
                    }
                    // Jika Owner atau Admin yang login, sesuaikan nama coach asli pemilik jadwal
                    if (Auth::user()->role === 'owner' || Auth::user()->role === 'admin') {
                        $displayCoachName = $pInfo->nama_lengkap ?? $namaUntukJadwal;
                    }
                }
            }

            // 🟢 JALUR AMAN: Hitung nama pelatih pengganti ke variabel terpisah sebelum masuk ke return array
            $finalPelatihNama = $displayCoachName;
            if ($j->is_takeover) {
                $sampelAbsen = DB::table('absensis')->where('jadwal_id', $j->id)->first();
                if ($sampelAbsen && $sampelAbsen->pelatih_hadir_id) {
                    $pHadir = DB::table('pelatihs')->where('id', $sampelAbsen->pelatih_hadir_id)->first();
                    $finalPelatihNama = $pHadir ? ($pHadir->nama_lengkap ?? 'Owner Jethree') : 'Owner Jethree';
                } else {
                    $finalPelatihNama = 'Owner Jethree';
                }
            }

            return [
                'id' => $j->id,
                'hari' => Carbon::parse($j->tanggal)->isoFormat('dddd'), 
                // 💡 FIX TIMEZONE: Paksa Carbon mengembalikan teks tanggal bersih (YYYY-MM-DD) agar tidak diubah ke UTC oleh Laravel
                'tanggal' => Carbon::parse($j->tanggal)->toDateString(), 
                'tanggal_indo' => Carbon::parse($j->tanggal)->isoFormat('D MMMM Y'),
                'kategori' => $j->kategori . $genderSuffix, // 💡 FIX: Otomatis menghasilkan "KU-10 Putra" / "KU-12 Putri"
                'jam' => substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5),
                'jam_mulai' => $j->jam_mulai ? substr($j->jam_mulai, 0, 5) : null,
                'jam_selesai' => $j->jam_selesai ? substr($j->jam_selesai, 0, 5) : null,
                'lokasi' => $j->lokasi,
                'materi' => $j->materi ?? '-',
                'pelatih_nama' => $finalPelatihNama, // 🟢 SEKARANG AMAN BERSIH TANPA EROR TANDA KURUNG
                'status_jadwal' => $j->status ?? 'Belum Selesai',
                
                // ⚡ SINKRONISASI WORKFLOW V2: Teruskan field baru untuk kebutuhan logika UI Flutter
                'status_rilis'   => $j->status_rilis ?? 'Draft',
                'link_youtube'   => $j->link_youtube,
                'review_latihan' => $j->review_latihan,
                'is_takeover'    => (bool) ($j->is_takeover ?? false),

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

        // 💡 FIX KATEGORI: Menggunakan LIKE agar kategori "KU-12" di jadwal bisa mencocokkan data "KU-12 Putra" atau "KU-12 Putri" di tabel atlet
        $atlets = Atlet::where('kategori', 'LIKE', '%' . $jadwal->kategori . '%') 
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

        // 🟢 REVISI: Ambil sampel salah satu absensi untuk tahu siapa pelatih yang hadir mengajar di lapangan
        $sampelAbsen = \App\Models\Absensi::where('jadwal_id', $jadwal_id)->first();
        $pelatihHadirId = $sampelAbsen ? $sampelAbsen->pelatih_hadir_id : null;

        // 💡 SINKRONISASI WORKFLOW V3: Kirim rekap data lengkap beserta status jadwal utuh ke Flutter
        return response()->json([
            'success' => true, 
            'data' => $atlets,
            'materi' => $jadwal->materi,
            'link_youtube' => $jadwal->link_youtube,
            'review_latihan' => $jadwal->review_latihan, // 🟢 Mengembalikan teks review agar menetap di HP
            'is_takeover' => (bool) ($jadwal->is_takeover ?? false),
            'status_jadwal' => $jadwal->status ?? 'Belum Selesai', // 🟢 Penentu tombol Flutter berubah jadi abu-abu
            'pelatih_hadir_id' => $pelatihHadirId ? (string)$pelatihHadirId : null // 🟢 Pengunci dropdown pelatih
        ]);
    }

    // 5. SIMPAN ABSENSI & NILAI (VERSI HYBRID: DUKUNG ECERAN & MASSAL KELAS)
    public function storeAbsensi(Request $request)
    {
        // Atur validasi adaptif sesuai tipe pengiriman data dari Flutter
        $rules = [
            'jadwal_id' => 'required',
        ];

        if ($request->has('atlet')) {
            $rules['review_latihan'] = 'required|string';
            $rules['atlet'] = 'required|array';
            $rules['atlet.*.atlet_id'] = 'required';
            $rules['atlet.*.status'] = 'required|in:H,A';
        } else {
            $rules['review_latihan'] = 'required|string';
            $rules['atlet_id'] = 'required';
            $rules['status'] = 'required|in:H,A';
            $rules['dribble'] = 'nullable|integer';
            $rules['pass'] = 'nullable|integer';
            $rules['shoot'] = 'nullable|integer';
            $rules['iq'] = 'nullable|integer';
            $rules['catatan'] = 'nullable|string';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $jadwal = Jadwal::find($request->jadwal_id);
            if (!$jadwal) {
                return response()->json(['success' => false, 'message' => 'Jadwal tidak valid'], 404);
            }

            // ⚡ WORKFLOW HARI-H: Simpan catatan evaluasi ke review_latihan & ubah status jadwal menjadi 'Selesai'
            $updateJadwalData = [
                'review_latihan' => $request->review_latihan,
                'status'         => 'Selesai'
            ];

            // ⚡ LOGIKA ANTI-CURANG TAKEOVER OWNER: Jika Owner yang melakukan absen di lapangan, kunci statusnya
            $userLog = $request->user();
            if ($userLog->role === 'owner' && !$jadwal->is_takeover) {
                $updateJadwalData['is_takeover'] = true;
                $updateJadwalData['pelatih_asli_id'] = $jadwal->pelatih_id; 

                DB::table('histori_takeovers')->insert([
                    'jadwal_id'        => $jadwal->id,
                    'owner_id'         => $userLog->id,
                    'pelatih_id'       => $jadwal->pelatih_id,
                    'tanggal_takeover' => \Carbon\Carbon::now('Asia/Jakarta')->toDateString(),
                    'created_at'       => \Carbon\Carbon::now('Asia/Jakarta'),
                    'updated_at'       => \Carbon\Carbon::now('Asia/Jakarta'),
                ]);
            }

            $jadwal->update($updateJadwalData);
            $tanggalAbsen = $jadwal->tanggal;

            // --- DETEKSI DINAMIS SIAPA YANG MELATIH DI LAPANGAN ---
            $pelatihHadirId = null;
            if ($request->has('pelatih_id') && $request->pelatih_id != null) {
                $pelatihHadirId = $request->pelatih_id;
            } else {
                $pelatihAsli = Pelatih::where('user_id', $userLog->id)->first();
                $pelatihHadirId = $pelatihAsli ? $pelatihAsli->id : null;
            }

            // Normalisasi data agar format kiriman eceran maupun massal dapat dibaca satu pintu loop
            $atletsToProcess = [];
            if ($request->has('atlet')) {
                $atletsToProcess = $request->atlet;
            } else {
                $atletsToProcess[] = [
                    'atlet_id' => $request->atlet_id,
                    'status'   => $request->status,
                    'dribble'  => $request->dribble,
                    'pass'     => $request->pass,
                    'shoot'    => $request->shoot,
                    'iq'       => $request->iq,
                    'catatan'  => $request->catatan,
                ];
            }

            // Perulangan eksekusi penyimpanan ke tabel absensis
            foreach ($atletsToProcess as $atletData) {
                $absen = Absensi::where('jadwal_id', $request->jadwal_id)
                                ->where('atlet_id', $atletData['atlet_id'])
                                ->first();

                if (!$absen) {
                    $absen = new Absensi();
                    $absen->jadwal_id = $request->jadwal_id;
                    $absen->atlet_id = $atletData['atlet_id'];
                }

                $absen->tanggal_latihan = $tanggalAbsen; 
                $absen->status = $atletData['status'];
                $absen->pelatih_hadir_id = $pelatihHadirId; 
                $absen->catatan = $atletData['catatan'] ?? null;

                // Gerbang filter tanggal 25 untuk pengisian nilai kompetensi bulanan
                $hariIni = \Carbon\Carbon::now('Asia/Jakarta')->day;
                $isAkhirBulan = ($hariIni >= 25);

                if ($atletData['status'] == 'H') {
                    $absen->nilai_dribble = $isAkhirBulan ? ($atletData['dribble'] ?? 0) : null;
                    $absen->nilai_pass    = $isAkhirBulan ? ($atletData['pass'] ?? 0) : null;
                    $absen->nilai_shoot   = $isAkhirBulan ? ($atletData['shoot'] ?? 0) : null;
                    $absen->nilai_iq      = $isAkhirBulan ? ($atletData['iq'] ?? 0) : null;
                } else {
                    $absen->nilai_dribble = null;
                    $absen->nilai_pass    = null;
                    $absen->nilai_shoot   = null;
                    $absen->nilai_iq      = null;
                }

                $absen->save();
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data berhasil disimpan!']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()], 500);
        }
    }

    // 6. ISI MATERI & RILIS JADWAL (Poin 2 & Poin 42)
    public function releaseJadwal(Request $request, $id)
    {
        $request->validate([
            'materi'       => 'required|string',
            'link_youtube' => 'required|string', // Wajib diisi jauh-jauh hari sebelum Hari-H
        ]);

        $user = Auth::user();
        $pelatih = Pelatih::where('user_id', $user->id)->first();
        $jadwal = Jadwal::find($id);

        if (!$jadwal) {
            return response()->json(['success' => false, 'message' => 'Jadwal latihan tidak ditemukan'], 404);
        }

        // Kunci Keamanan: Pastikan pelatih biasa tidak bisa merilis jadwal milik pelatih lain
        if ($user->role !== 'owner' && $user->role !== 'admin' && $jadwal->pelatih_id !== $pelatih->id) {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak! Ini bukan jadwal kelas Anda.'], 403);
        }

        // Eksekusi Rilis ke tabel jadwals
        $jadwal->update([
            'materi'       => $request->materi,
            'link_youtube' => $request->link_youtube,
            'status_rilis' => 'Rilis' // Otomatis berubah agar bisa diintip oleh Atlet
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dilengkapi dan resmi dirilis ke Atlet!'
        ], 200);
    }

    // 💡 FITUR HISTORI TAKEOVER (BARU): Menarik riwayat ambil alih untuk dikonsumsi HP Owner
    public function historiTakeover()
    {
        try {
            $histori = DB::table('histori_takeovers')
                ->join('jadwals', 'histori_takeovers.jadwal_id', '=', 'jadwals.id')
                ->join('pelatihs', 'histori_takeovers.pelatih_id', '=', 'pelatihs.id')
                ->join('users', 'histori_takeovers.owner_id', '=', 'users.id')
                ->select([
                    'histori_takeovers.id',
                    'histori_takeovers.tanggal_takeover',
                    'jadwals.kategori as kategori_kelas',
                    'pelatihs.nama_lengkap as coach_asli',
                    'users.name as coach_baru'
                ])
                ->orderBy('histori_takeovers.id', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $histori
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil histori: ' . $e->getMessage()
            ], 500);
        }
    }

    // 🟢 SUNTIKAN BARU: Mengambil daftar pelatih aktif untuk dropdown Take Over Owner di Flutter
    public function listPelatih()
    {
        try {
            $pelatihs = Pelatih::where('status', 'Aktif')->get(['id', 'nama_lengkap']);
            
            return response()->json([
                'success' => true,
                'data' => $pelatihs
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat list pelatih: ' . $e->getMessage()
            ], 500);
        }
    }

    // 🔔 NOTIFICATION CENTER DINAMIS (COACH & OWNER ALERTS)
    public function notifications()
    {
        $user = Auth::user();
        $notifications = [];
        $now = \Carbon\Carbon::now();

        // 👑 PERBAIKAN LOGIKA: Jika yang login adalah Owner / Admin Utama Akademi
        if ($user->role === 'owner' || $user->role === 'admin') {
            
            // 💡 NOTIFIKASI OWNER 1: Hitung tagihan SPP masuk yang berstatus 'Pending' (Menunggu Verifikasi)
            $sppPendingCount = \App\Models\Tagihan::where('status', 'Pending')->count();
            
            if ($sppPendingCount > 0) {
                $notifications[] = [
                    'id' => 'owner_spp_pending',
                    'title' => 'Konfirmasi Pembayaran SPP 💰',
                    'message' => 'Ada ' . $sppPendingCount . ' transaksi SPP atlet baru yang berstatus Pending. Yuk cek berkas dan verifikasi pembayarannya!',
                    'type' => 'finance',
                    'created_at' => $now->toIso8601String(),
                    'is_unread' => true
                ];
            }

            // 💡 NOTIFIKASI OWNER 2: Info Rekap Histori Lapangan (System Monitoring)
            $notifications[] = [
                'id' => 'owner_system_monitoring',
                'title' => 'Pusat Kendali Akademi 👑',
                'message' => 'Manajemen latihan aman terkendali. Seluruh riwayat ambil alih kelas (Takeover) pelatih otomatis terekam di menu Histori Lapangan.',
                'type' => 'system',
                'created_at' => $now->subHours(4)->toIso8601String(),
                'is_unread' => false
            ];

        } else {
            // 🏀 JALUR ASLI COACH: Tetap utuh tanpa dikurangi sedikit pun
            $notifications[] = [
                'id' => 'remind_absen_' . $now->format('d_m_Y'),
                'title' => 'Pengingat Absen Latihan 📋',
                'message' => 'Coach, mohon pastikan untuk selalu mengisi kehadiran dan menginput nilai performa atlet setelah sesi latihan selesai hari ini ya!',
                'type' => 'attendance',
                'created_at' => $now->toIso8601String(),
                'is_unread' => true
            ];

            $notifications[] = [
                'id' => 'info_takeover_' . $now->format('m_Y'),
                'title' => 'Pembaruan Tugas Lapangan ⚡',
                'message' => 'Pantau terus beranda aplikasi Anda. Jika ada instruksi "Takeover Jadwal" dari Owner, detail tugas akan otomatis tertera di menu latihan.',
                'type' => 'system',
                'created_at' => $now->subHours(2)->toIso8601String(),
                'is_unread' => false
            ];
        }

        return response()->json([
            'success' => true,
            'total_unread' => collect($notifications)->where('is_unread', true)->count(),
            'data' => $notifications
        ]);
    }
}