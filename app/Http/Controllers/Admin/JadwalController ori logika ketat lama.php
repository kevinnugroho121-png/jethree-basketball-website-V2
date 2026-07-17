<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Pelatih;
use App\Models\Atlet;      // [PENTING] Tambah Model Atlet
use App\Models\Notifikasi; // [PENTING] Tambah Model Notifikasi
use App\Models\Tagihan;    // <--- TAMBAHKAN BARIS INI (MISI 3)

class JadwalController extends Controller
{
    /**
     * Menampilkan daftar jadwal dengan Filter Lengkap.
     */
    public function index(Request $request)
    {
        // 1. Siapkan query
        // 'withTrashed' agar nama pelatih yang sudah dihapus tetap muncul di histori jadwal
        $query = Jadwal::with(['pelatih' => function ($q) {
            $q->withTrashed(); 
        }]);

        // 2. Filter Kategori (Jika ada)
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // 3. Filter Rentang Tanggal
        if ($request->filled('mulai_tanggal') && $request->filled('sampai_tanggal')) {
            $query->whereBetween('tanggal', [$request->mulai_tanggal, $request->sampai_tanggal]);
        } elseif ($request->filled('mulai_tanggal')) {
            $query->where('tanggal', '>=', $request->mulai_tanggal);
        } elseif ($request->filled('sampai_tanggal')) {
            $query->where('tanggal', '<=', $request->sampai_tanggal);
        }

        // 4. Ambil data
        $jadwals = $query->orderBy('tanggal', 'desc')
                         ->orderBy('jam_mulai', 'asc')
                         ->paginate(5)
                         ->withQueryString(); 

        return view('admin.jadwal.index', compact('jadwals'));
    }

    /**
     * Form tambah jadwal.
     */
    public function create()
    {
        $pelatihs = Pelatih::where('status', 'Aktif')->get();
        return view('admin.jadwal.create', compact('pelatihs'));
    }

    /**
     * Simpan jadwal baru.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'tanggal'     => 'required|date|after_or_equal:today', 
            'kategori'    => 'required',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai', 
            'lokasi'      => 'required',
            'status'      => 'required',
            'pelatih_id'  => 'required|exists:pelatihs,id',
            'materi'      => 'required|string', // Kolom materi wajib diisi
        ]);

        // 2. Cek Bentrok (Satpam Waktu)
        $bentrok = Jadwal::where('pelatih_id', $request->pelatih_id)
            ->where('tanggal', $request->tanggal)
            ->where(function ($query) use ($request) {
                // Rumus Irisan Waktu: (Start1 < End2) AND (End1 > Start2)
                $query->where('jam_mulai', '<', $request->jam_selesai)
                      ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->first();

        if ($bentrok) {
            $namaPelatih = Pelatih::find($request->pelatih_id)->nama_lengkap;
            return redirect()->back()
                ->withInput()
                ->withErrors(['pelatih_id' => "❌ GAGAL: Coach $namaPelatih sudah ada jadwal lain di jam tersebut ($bentrok->jam_mulai - $bentrok->jam_selesai)."]);
        }
        
        // 3. Simpan Jadwal ke Database
        $jadwal = Jadwal::create($request->all());

        // ========================================================
        // [TAMBAHAN] KIRIM NOTIFIKASI KE LONCENG PELATIH
        // ========================================================
        $pelatihDitugaskan = Pelatih::find($request->pelatih_id);
        if ($pelatihDitugaskan && $pelatihDitugaskan->user_id) {
            Notifikasi::create([
                'user_id'     => $pelatihDitugaskan->user_id,
                'target_role' => 'pelatih', // [KUNCI REVISI] Agar terbaca di dashboard pelatih
                'judul'       => 'Jadwal Melatih Baru 🏀',
                'pesan'       => "Coach, Anda ditugaskan melatih kategori " . $request->kategori . " pada " . date('d M Y', strtotime($request->tanggal)) . " jam " . substr($request->jam_mulai, 0, 5) . " di " . $request->lokasi . ".",
                'kategori'    => 'jadwal', 
                'is_read'     => false,
            ]);
        }

        // ========================================================
        // 4. AMBIL DATA ATLET & SIAPKAN FORMAT BROADCAST DINAMIS
        // ========================================================
        $atlets = Atlet::where('kategori', $request->kategori)->get();
        
        $targetHps = []; // Penampung nomor WA
        $pesanWAs = [];  // Penampung Pesan spesifik per anak

        foreach ($atlets as $atlet) {
            // A. Simpan Notifikasi In-App (di lonceng web/mobile)
            if ($atlet->user_id) { 
                Notifikasi::create([
                    'user_id'     => $atlet->user_id,
                    'target_role' => 'atlet', // [KUNCI REVISI] Mencegah error jika kolom wajib diisi
                    'judul'       => 'Jadwal Latihan Baru 📅',
                    'pesan'       => "Latihan " . $request->materi . " pada " . date('d M Y', strtotime($request->tanggal)) . " (" . $request->jam_mulai . ") di " . $request->lokasi,
                    'kategori'    => 'jadwal', 
                    'is_read'     => false,
                ]);
            }

            // B. Tentukan Nomor HP (Prioritas HP Atlet, kalau kosong pakai HP Ortu)
            $targetHp = $atlet->no_hp_atlet ? $atlet->no_hp_atlet : $atlet->no_hp_orang_tua;

            if ($targetHp) {
                // Bersihkan karakter spasi atau tanda strip dari nomor HP jika ada
                $targetHp = preg_replace('/[^0-9]/', '', $targetHp);
                
                // Pastikan format kode negara benar (ganti 0 dengan 62)
                if (substr($targetHp, 0, 1) === '0') {
                    $targetHp = '62' . substr($targetHp, 1);
                }

                // C. Cek Status Tagihan SPP si Anak
                $tagihanTertunggak = Tagihan::where('atlet_id', $atlet->id)
                                        ->where('status', 'Belum Lunas')
                                        ->first();

                $pesanSPP = "";
                if ($tagihanTertunggak) {
                    $pesanSPP = "⚠️ *INFO KEUANGAN:* Mohon maaf, ada tagihan SPP yang belum dibayarkan sebesar *Rp " . number_format($tagihanTertunggak->jumlah_tagihan, 0, ',', '.') . "*. Silakan cek aplikasi untuk pembayaran.";
                } else {
                    $pesanSPP = "✅ *INFO KEUANGAN:* Jika ada SPP Ananda yang masih belum lunas, segera dilunasi ya";
                }

                // D. Susun Pesan WA Spesifik
                $pesanWA = "📢 *INFO JADWAL LATIHAN BARU* 🏀\n\n";
                $pesanWA .= "Halo *" . $atlet->nama_lengkap . "* (Tim " . $request->kategori . "),\n";
                $pesanWA .= "Ada jadwal latihan baru untukmu:\n\n";
                $pesanWA .= "🗓 Tanggal: *" . date('d M Y', strtotime($request->tanggal)) . "*\n";
                $pesanWA .= "⏰ Jam: *" . $request->jam_mulai . " - " . $request->jam_selesai . " WIB*\n";
                $pesanWA .= "📍 Lokasi: *" . $request->lokasi . "*\n";
                $pesanWA .= "📝 Materi: *" . $request->materi . "*\n\n";
                $pesanWA .= "-----------------------------------\n";
                $pesanWA .= $pesanSPP . "\n";
                $pesanWA .= "-----------------------------------\n\n";
                $pesanWA .= "Jangan sampai telat ya! Semangat latihannya! 🔥\n";
                $pesanWA .= "_Pesan otomatis Jethree Basketball_";

                // Tambahkan nomor & pesannya ke array
                $targetHps[] = $targetHp;
                $pesanWAs[] = $pesanWA;
            }
        }
        
        // ========================================================
        // 5. KIRIM BROADCAST WA VIA FONNTE (Loop Kilat)
        // ========================================================
        $tokenFonnte = 'SqLCDpjTPVSgkJ2yJGKN';

        // Kita loop array yang sudah dikumpulkan tadi
        for ($i = 0; $i < count($targetHps); $i++) {
            $target = $targetHps[$i];
            $message = $pesanWAs[$i];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 2, // Timeout dipercepat agar tidak nunggu lama kalau gagal 1 nomor
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $target,     // Kirim 1 nomor
                    'message' => $message,   // Kirim 1 pesan spesifik
                    'delay' => '1'           // Delay aman biar nggak dikira spam
                ),
                CURLOPT_HTTPHEADER => array(
                    "Authorization: $tokenFonnte"
                ),
            ));

            curl_exec($curl);
            curl_close($curl);
        }

        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal latihan berhasil ditambahkan & Notifikasi WA terkirim ke seluruh atlet!');
    }

    /**
     * Form edit jadwal.
     */
    public function edit($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $pelatihs = Pelatih::where('status', 'Aktif')->get();
        return view('admin.jadwal.edit', compact('jadwal', 'pelatihs'));
    }

    /**
     * Update jadwal.
     */
    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        // 1. Validasi
        $request->validate([
            'tanggal'     => 'required|date|after_or_equal:today',
            'kategori'    => 'required',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'lokasi'      => 'required',
            'status'      => 'required',
            'pelatih_id'  => 'required|exists:pelatihs,id',
            'materi'      => 'required|string',
        ]);

        // 2. Cek Bentrok (Kecuali jadwal ini sendiri)
        $bentrok = Jadwal::where('pelatih_id', $request->pelatih_id)
            ->where('tanggal', $request->tanggal)
            ->where('id', '!=', $id) 
            ->where(function ($query) use ($request) {
                $query->where('jam_mulai', '<', $request->jam_selesai)
                      ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->first();

        if ($bentrok) {
            $namaPelatih = Pelatih::find($request->pelatih_id)->nama_lengkap;
            return redirect()->back()
                ->withInput()
                ->withErrors(['pelatih_id' => "❌ GAGAL: Coach $namaPelatih bentrok dengan jadwal lain ($bentrok->jam_mulai - $bentrok->jam_selesai)."]);
        }
        
        $jadwal->update($request->all());
        
        return redirect()->route('jadwal.index')
            ->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Hapus jadwal.
     */
    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        
        return redirect()->route('jadwal.index')
            ->with('success', 'Data jadwal berhasil dihapus.');
    }

    /**
     * ⚡ BARU: AUTO GENERATE JADWAL BULANAN MASSAL + MATCHING PELATIH
     */
    public function generate(Request $request)
    {
        // 1. Validasi Input Modal Form Massal
        $request->validate([
            'bulan_tahun' => 'required|string',
            'hari'        => 'required|array',
            'kategori'    => 'required|string',
            'gender'      => 'required|string',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'lokasi'      => 'required|string',
        ]);

        // 2. Cari Pelatih Otomatis Berdasarkan Kategori & Gender Fokus Kelas yang Aktif
        $pelatih = Pelatih::where('status', 'Aktif')
            ->where('kategori_fokus', $request->kategori)
            ->where('gender_fokus', $request->gender)
            ->first();

        // Proteksi: Jika tidak ada coach yang memegang spesialisasi kelas ini
        if (!$pelatih) {
            return redirect()->back()->withInput()->withErrors([
                'kategori' => "❌ GAGAL GENERATE: Tidak ditemukan Coach Aktif yang memegang fokus kelas {$request->kategori} {$request->gender}."
            ]);
        }

        // 3. Pecah Bulan & Tahun untuk Tentukan Jumlah Hari
        list($tahun, $bulan) = explode('-', $request->bulan_tahun);
        $jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        
        $jadwalDibuat = 0;
        $jadwalBentrok = 0;

        // Gunakan DB Transaction agar eksekusi data aman
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            // Loop tanggal 1 sampai akhir bulan
            for ($d = 1; $d <= $jumlahHari; $d++) {
                $tanggalStr = sprintf('%04d-%02d-%02d', $tahun, $bulan, $d);
                $hariInggris = date('l', strtotime($tanggalStr));

                // Jika hari tanggal tersebut tercentang di form pilihan admin
                if (in_array($hariInggris, $request->hari)) {
                    
                    // Cek Apakah Coach Terkait Mengalami Bentrok Jam di Tanggal Ini
                    $bentrok = Jadwal::where('pelatih_id', $pelatih->id)
                        ->where('tanggal', $tanggalStr)
                        ->where(function ($query) use ($request) {
                            $query->where('jam_mulai', '<', $request->jam_selesai)
                                  ->where('jam_selesai', '>', $request->jam_mulai);
                        })
                        ->exists();

                    if ($bentrok) {
                        $jadwalBentrok++;
                        continue; // Lewati tanggal ini jika bentrok, lanjut ke tanggal berikutnya
                    }

                    // Eksekusi Pembuatan Baris Jadwal Otomatis
                    Jadwal::create([
                        'tanggal'     => $tanggalStr,
                        'kategori'    => $request->kategori,
                        'jam_mulai'   => $request->jam_mulai,
                        'jam_selesai' => $request->jam_selesai,
                        'lokasi'      => $request->lokasi,
                        'status'      => 'Aktif',
                        'pelatih_id'  => $pelatih->id,
                        'materi'      => "Latihan Rutin Tim " . $request->kategori . " (" . $request->gender . ")",
                    ]);

                    // Kirim Notifikasi Lonceng Web (In-App) ke Coach
                    if ($pelatih->user_id) {
                        \App\Models\Notifikasi::create([
                            'user_id'     => $pelatih->user_id,
                            'target_role' => 'pelatih',
                            'judul'       => 'Penugasan Jadwal Rutin 🏀',
                            'pesan'       => "Anda otomatis ditugaskan melatih kelas rutin " . $request->kategori . " pada tanggal " . date('d-m-Y', strtotime($tanggalStr)) . " jam " . substr($request->jam_mulai, 0, 5) . " WIB.",
                            'kategori'    => 'jadwal',
                            'is_read'     => false,
                        ]);
                    }

                    $jadwalDibuat++;
                }
            }

            // Jika dari seluruh loop tidak ada satu pun hari yang berhasil dibuat
            if ($jadwalDibuat === 0) {
                \Illuminate\Support\Facades\DB::rollBack();
                return redirect()->route('jadwal.index')->with('error', 'Gagal: Tidak ada jadwal rutin yang berhasil dibuat pada bulan tersebut. Cek kesesuaian hari pilihan Anda.');
            }

            \Illuminate\Support\Facades\DB::commit();
            
            // Susun notifikasi informasi performa sistem ke Admin
            $notifSukses = "Berhasil! Sistem otomatis membuat {$jadwalDibuat} jadwal latihan rutin sebulan penuh untuk Coach {$pelatih->nama_lengkap}.";
            if ($jadwalBentrok > 0) {
                $notifSukses .= " ({$jadwalBentrok} tanggal dilewati karena jam melatih coach bentrok).";
            }

            return redirect()->back()->with('success', $notifSukses); // ⚡ SINKRON: Kembali ke halaman asal (Kalender/Jadwal)

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi gangguan sistem: ' . $e->getMessage());
        }
    }
}