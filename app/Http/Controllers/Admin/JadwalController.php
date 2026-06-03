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
}