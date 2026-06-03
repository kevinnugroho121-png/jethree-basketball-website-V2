<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Atlet;
use App\Models\Notifikasi; // [PENTING] Model Notifikasi Wajib Ada
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf; 

class TagihanController extends Controller
{
    // === 1. TAMPILKAN DAFTAR ATLET & REKAP TAGIHAN (REVISI 4, 6 & 2) ===
    public function index(Request $request)
    {
        // 1. Panggil Atlet beserta tagihannya
        $query = Atlet::with('tagihans')
                      ->withMax('tagihans', 'tanggal_lunas');
        
        // 2. [LOGIKA BARU] Buat kolom sementara (subquery) untuk mengecek 
        // apakah atlet ini punya tagihan 'Menunggu Verifikasi'.
        // Jika ada (1), Jika tidak ada (0). Ini digunakan untuk Sorting Auto-Naik ke Atas.
        $query->addSelect([
            'has_pending_verification' => \App\Models\Tagihan::selectRaw('MAX(IF(status = "Menunggu Verifikasi", 1, 0))')
                ->whereColumn('atlet_id', 'atlets.id')
        ]);

        // 3. Filter Pencarian Nama
        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // 4. [BARU] Filter Kategori (Misal: KU-12, KU-15, dll)
        if ($request->filled('kategori') && $request->kategori != 'Semua') {
            $query->where('kategori', $request->kategori); // Ubah ke 'kategori' atau 'kategori_umur'
        }

        // 5. [BARU] Filter Status Pembayaran
        if ($request->filled('status_bayar') && $request->status_bayar != 'Semua') {
            if ($request->status_bayar == 'Menunggu Verifikasi') {
                // Hanya tampilkan atlet yang punya minimal 1 tagihan Menunggu Verifikasi
                $query->whereHas('tagihans', function($q) {
                    $q->where('status', 'Menunggu Verifikasi');
                });
            } elseif ($request->status_bayar == 'Nunggak') {
                // Hanya tampilkan atlet yang punya minimal 1 tagihan Belum Lunas
                $query->whereHas('tagihans', function($q) {
                    $q->where('status', 'Belum Lunas');
                });
            } elseif ($request->status_bayar == 'Lunas') {
                // Tampilkan atlet yang tagihannya Lunas SEMUA (Tidak punya yang Nunggak/Pending)
                $query->whereDoesntHave('tagihans', function($q) {
                    $q->whereIn('status', ['Belum Lunas', 'Menunggu Verifikasi']);
                })->whereHas('tagihans'); // Pastikan dia memang sudah pernah ditagih
            }
        }

        // 6. [SORTING SUPER] 
        // - Prioritas 1: Yang Menunggu Verifikasi wajib naik ke pucuk (DESC)
        // - Prioritas 2: Yang baru saja dilunaskan (DESC)
        // - Prioritas 3: Abjad Nama (ASC)
        $atlets = $query->orderBy('has_pending_verification', 'desc')
                        ->orderBy('tagihans_max_tanggal_lunas', 'desc')
                        ->orderBy('nama_lengkap', 'asc')
                        ->paginate(10)
                        ->withQueryString();

        return view('admin.tagihan.index', compact('atlets'));
    }

    // === 2. FORM BUAT TAGIHAN BARU ===
    public function create()
    {
        $atlets = Atlet::where('status', 'Aktif')->orderBy('nama_lengkap', 'asc')->get();
        return view('admin.tagihan.create', compact('atlets'));
    }

    // === 3. SIMPAN TAGIHAN + KIRIM NOTIFIKASI ===
    public function store(Request $request)
    {
        $request->validate([
            'atlet_id' => 'required|exists:atlets,id',
            'bulan'    => 'required|integer|min:1|max:12',
            'tahun'    => 'required|integer|min:2024|max:2030',
            'nominal'  => 'required|numeric|min:0',
        ]);

        // CEK DUPLIKAT (Biar gak dobel tagihan di bulan sama)
        $cek = Tagihan::where('atlet_id', $request->atlet_id)
                      ->where('bulan', $request->bulan)
                      ->where('tahun', $request->tahun)
                      ->exists();

        if ($cek) {
            return back()->withErrors(['duplikat' => 'GAGAL: Tagihan SPP untuk Bulan & Tahun tersebut sudah dibuat sebelumnya!']);
        }

        // Simpan Data Tagihan
        $tagihan = Tagihan::create([
            'atlet_id'        => $request->atlet_id,
            'jenis_tagihan'   => 'SPP',
            'bulan'           => $request->bulan,
            'tahun'           => $request->tahun,
            'nominal'         => $request->nominal,
            'tanggal_tagihan' => now(),
            'status'          => 'Belum Lunas',
        ]);

        // [BARU] NOTIFIKASI KE DATABASE (Agar muncul di HP Atlet)
        $atlet = Atlet::find($request->atlet_id);
        if ($atlet && $atlet->user_id) {
            Notifikasi::create([
                'user_id'  => $atlet->user_id,
                'judul'    => 'Tagihan SPP Baru 💸',
                'pesan'    => 'Admin telah menerbitkan Tagihan SPP Bulan ' . $request->bulan . '/' . $request->tahun . '. Mohon segera dicek.',
                'kategori' => 'tagihan', // Sesuaikan dengan kolom migrasi tadi
                'is_read'  => false,
            ]);
        }

        return redirect()->route('tagihan.index')->with('success', 'Tagihan SPP berhasil dibuat & Notifikasi dikirim ke Atlet.');
    }

    // === 4. FORM EDIT / BAYAR (KASIR) ===
    public function edit(Tagihan $tagihan)
    {
        return view('admin.tagihan.edit', compact('tagihan'));
    }

    // === 5. UPDATE (PROSES PEMBAYARAN + NOTIFIKASI LUNAS) ===
    public function update(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'status'            => 'required|in:Lunas,Belum Lunas',
            'metode_pembayaran' => 'nullable|string',
            'bukti_pembayaran'  => 'nullable|image|max:2048', // Max 2MB
            'keterangan'        => 'nullable|string',
        ]);

        $data = [
            'status'     => $request->status,
            'keterangan' => $request->keterangan,
        ];

        // SKENARIO LUNAS
        if ($request->status == 'Lunas') {
            
            // Validasi Metode
            if (!$request->metode_pembayaran && !$tagihan->metode_pembayaran) {
                return back()->withErrors(['metode' => 'Metode pembayaran wajib dipilih jika status Lunas.']);
            }
            
            $data['metode_pembayaran'] = $request->metode_pembayaran;
            $data['tanggal_lunas'] = $tagihan->tanggal_lunas ?? now(); // Kalau sudah pernah lunas, tanggal jangan berubah

            // UPLOAD BUKTI (Jika Ada File Baru)
            if ($request->hasFile('bukti_pembayaran')) {
                // Hapus file lama biar gak menuhi storage
                if ($tagihan->bukti_pembayaran) {
                    Storage::disk('public')->delete($tagihan->bukti_pembayaran);
                }
                $path = $request->file('bukti_pembayaran')->store('bukti-bayar', 'public');
                $data['bukti_pembayaran'] = $path;
            }
            
            // [LOGIKA BARU] NOTIFIKASI PEMBAYARAN LUNAS
            // Kirim notif HANYA JIKA status sebelumnya 'Belum Lunas'
            if ($tagihan->status == 'Belum Lunas') {
                $atlet = $tagihan->atlet;
                if ($atlet && $atlet->user_id) {
                    Notifikasi::create([
                        'user_id'  => $atlet->user_id,
                        'judul'    => 'Pembayaran Lunas ✅',
                        'pesan'    => 'Terima kasih! Pembayaran ' . $tagihan->jenis_tagihan . ' ' . $tagihan->bulan . '/' . $tagihan->tahun . ' telah kami terima dan diverifikasi.',
                        'kategori' => 'pembayaran',
                        'is_read'  => false,
                    ]);
                }

                // BUKA GEMBOK ATLET JIKA STATUS MASIH PENDING
                if ($atlet && $atlet->status == 'Pending') {
                    $atlet->update(['status' => 'Aktif']);
                }
            }

        } 
        // SKENARIO BATAL LUNAS (Reset)
        elseif ($request->status == 'Belum Lunas') {
            $data['tanggal_lunas'] = null;
            $data['metode_pembayaran'] = null;
            // Bukti pembayaran tidak kita hapus otomatis, buat jaga-jaga.
        }

        $tagihan->update($data);

        return redirect()->route('tagihan.index')->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    // === 6. HAPUS TAGIHAN ===
    public function destroy(Tagihan $tagihan)
    {
        if ($tagihan->bukti_pembayaran) {
            Storage::disk('public')->delete($tagihan->bukti_pembayaran);
        }
        $tagihan->delete();
        return redirect()->route('tagihan.index')->with('success', 'Tagihan berhasil dihapus.');
    }

    // === 7. LIHAT BUKTI BAYAR (PAGE KHUSUS) ===
    public function lihatBukti($id) 
    {
        $tagihan = Tagihan::with('atlet')->findOrFail($id);
        
        // Return ke view yang sudah kamu buat
        return view('admin.tagihan.bukti', compact('tagihan'));
    }

    // === 8. PREVIEW LAPORAN (VIEW) ===
    public function preview(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', date('Y'));

        // Query Laporan: Hanya ambil yang LUNAS
        $query = Tagihan::with('atlet.user')->where('status', 'Lunas');

        if ($bulan) $query->where('bulan', $bulan);
        if ($tahun) $query->where('tahun', $tahun);

        $laporan = $query->orderBy('tanggal_lunas', 'asc')->get();
        $totalPemasukan = $laporan->sum('nominal');

        return view('admin.tagihan.preview', [
            'laporan' => $laporan, 
            'bulan' => $bulan, 
            'tahun' => $tahun, 
            'total' => $totalPemasukan
        ]);
    }

    // === 9. CETAK PDF (DOWNLOAD) ===
    public function cetakPdf(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun', date('Y'));

        $query = Tagihan::with('atlet.user')->where('status', 'Lunas');

        if ($bulan) $query->where('bulan', $bulan);
        if ($tahun) $query->where('tahun', $tahun);

        $laporan = $query->orderBy('tanggal_lunas', 'asc')->get();
        $totalPemasukan = $laporan->sum('nominal');

        $pdf = Pdf::loadView('admin.tagihan.cetak_pdf', [
            'laporan' => $laporan, 'bulan' => $bulan, 'tahun' => $tahun, 'total' => $totalPemasukan
        ]);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Laporan-Keuangan.pdf');
    }

    // === 10. [BARU] FITUR VERIFIKASI CEPAT (TOMBOL OK HIJAU DI INDEX) ===
    // Fungsi ini dipanggil saat tombol OK ditekan di halaman index
    public function verifikasiLunas($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        // Update status jadi Lunas
        $tagihan->update([
            'status' => 'Lunas',
            'tanggal_lunas' => now(),
            // Jika metode pembayaran kosong (misal dari mobile belum diset), kita set default
            'metode_pembayaran' => $tagihan->metode_pembayaran ?? 'Transfer Bank (Via Mobile)', 
        ]);

        // Kirim Notifikasi ke Atlet bahwa pembayaran diterima
        if ($tagihan->atlet && $tagihan->atlet->user_id) {
            Notifikasi::create([
                'user_id'  => $tagihan->atlet->user_id,
                'judul'    => 'Pembayaran Lunas ✅',
                'pesan'    => 'Terima kasih! Pembayaran ' . $tagihan->jenis_tagihan . ' telah diverifikasi Admin.',
                'kategori' => 'pembayaran',
                'is_read'  => false,
            ]);

            // BUKA GEMBOK ATLET JIKA STATUS MASIH PENDING
            if ($tagihan->atlet->status == 'Pending') {
                $tagihan->atlet->update(['status' => 'Aktif']);
            }
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi Lunas!');
    }


    // === [BARU] HALAMAN DETAIL SPP KHUSUS 1 ATLET ===
    public function show($id)
    {
        // Cari data atlet berdasarkan ID
        $atlet = Atlet::findOrFail($id);
        
        // Ambil semua riwayat tagihan milik atlet ini, urutkan dari yang terbaru
        $tagihans = Tagihan::where('atlet_id', $id)
                           ->orderBy('tahun', 'desc')
                           ->orderBy('bulan', 'desc')
                           ->get();
                           
        // Kirim datanya ke halaman view baru (show.blade.php)
        return view('admin.tagihan.show', compact('atlet', 'tagihans'));
    }


    // === [BARU] SIMPAN TAGIHAN MASAL (CHECKBOX) + VALIDASI UMUR DAFTAR + NOTIF WA (REVISI 2, 5a, 5b) ===
    public function storeBulk(Request $request)
    {
        $request->validate([
            'atlet_id' => 'required|exists:atlets,id',
            'tahun'    => 'required|integer|min:2024|max:2030',
            'nominal'  => 'required|numeric|min:0',
            'bulan'    => 'required|array|min:1', // Harus milih minimal 1 bulan
        ]);

        $atlet = Atlet::findOrFail($request->atlet_id);
        
        // REVISI 2: Ambil bulan dan tahun saat atlet didaftarkan di sistem
        $tanggalDaftar = \Carbon\Carbon::parse($atlet->created_at);
        $bulanDaftar = $tanggalDaftar->month;
        $tahunDaftar = $tanggalDaftar->year;

        $jumlahSukses = 0;
        $listBulanSukses = [];

        // Looping setiap bulan yang dicentang oleh admin
        foreach ($request->bulan as $bln) {
            
            // REVISI 2: Cek apakah admin mencoba membuat tagihan di bulan sebelum atlet terdaftar
            if ($request->tahun < $tahunDaftar || ($request->tahun == $tahunDaftar && $bln < $bulanDaftar)) {
                // Jika melanggar, bulan ini dilewati (skip), tidak di-input ke database
                continue;
            }

            // CEK DUPLIKAT: Biar gak dobel tagihan di bulan dan tahun yang sama
            $cekDuplikat = Tagihan::where('atlet_id', $request->atlet_id)
                                  ->where('bulan', $bln)
                                  ->where('tahun', $request->tahun)
                                  ->exists();

            if (!$cekDuplikat) {
                // Simpan Tagihan ke Database
                Tagihan::create([
                    'atlet_id'        => $request->atlet_id,
                    'jenis_tagihan'   => 'SPP',
                    'bulan'           => $bln,
                    'tahun'           => $request->tahun,
                    'nominal'         => $request->nominal,
                    'tanggal_tagihan' => now(),
                    'status'          => 'Belum Lunas',
                ]);

                // Simpan data notifikasi internal database (untuk HP Atlet)
                if ($atlet->user_id) {
                    Notifikasi::create([
                        'user_id'  => $atlet->user_id,
                        'judul'    => 'Tagihan SPP Baru 💸',
                        'pesan'    => 'Admin telah menerbitkan Tagihan SPP Bulan ' . $bln . '/' . $request->tahun . '. Mohon segera dicek.',
                        'kategori' => 'tagihan',
                        'is_read'  => false,
                    ]);
                }

                $jumlahSukses++;
                $listBulanSukses[] = $bln;
            }
        }

        // =========================================================================
        // REVISI 5b: Jika ada tagihan yang berhasil dibuat, tembak Notif WA Fonnte
        // =========================================================================
        if ($jumlahSukses > 0) {
            $namaBulan = [
                1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni',
                7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
            ];
            
            $stringBulan = implode(', ', array_map(fn($b) => $namaBulan[$b], $listBulanSukses));
            
            // 1. PERBAIKAN LOGIKA NOMOR HP (Prioritas HP Ortu, kalau kosong pakai HP Atlet)
            $targetHp = $atlet->no_hp_orang_tua ? $atlet->no_hp_orang_tua : ($atlet->no_hp_atlet ?? '0');
            
            // Format nomor HP ke standar internasional (ubah awalan 0 jadi 62)
            $no_hp = preg_replace('/^0/', '62', $targetHp);
            
            $pesanWa = "📢 *INFO TAGIHAN SPP JETHREE* 🏀\n\n" .
                       "Halo Orang Tua dari *" . $atlet->nama_lengkap . "*,\n\n" .
                       "Admin telah menerbitkan *Tagihan SPP* untuk Bulan: *" . $stringBulan . " " . $request->tahun . "*.\n" .
                       "Total Tagihan Baru: *" . $jumlahSukses . " Bulan*\n" .
                       "Nominal per bulan: *Rp " . number_format($request->nominal, 0, ',', '.') . "*.\n\n" .
                       "Mohon untuk melakukan pengecekan dan pembayaran melalui aplikasi Android Jethree Anda. Terima kasih! 🙏";

            // 2. PERBAIKAN TOKEN FONNTE (Langsung ditanam agar pasti terbaca)
            $tokenFonnte = 'SqLCDpjTPVSgkJ2yJGKN'; 

            // 3. Kodingan cURL Fonnte
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 5, // Dibatasi 5 detik agar tidak memblokir aplikasi jika server Fonnte lambat
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $no_hp,
                    'message' => $pesanWa,
                    'countryCode' => '62',
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $tokenFonnte
                ),
                CURLOPT_SSL_VERIFYPEER => false // SANGAT PENTING: Mengatasi error SSL di server Localhost
            ));
            
            curl_exec($curl);
            curl_close($curl);
        }

        if ($jumlahSukses == 0) {
            return redirect()->back()->with('error', 'Gagal membuat tagihan. Bulan yang dipilih mungkin sudah ada sebelumnya atau melanggar batas bulan join atlet.');
        }

        return redirect()->back()->with('success', $jumlahSukses . ' Tagihan SPP baru berhasil dibuat masal & Notifikasi WhatsApp otomatis telah dikirim ke orang tua.');
    }
}