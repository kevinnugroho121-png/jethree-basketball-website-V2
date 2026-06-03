<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Atlet;
use App\Models\User;
use App\Models\Jadwal; // <--- TAMBAHKAN BARIS INI
use App\Models\Absensi;
use App\Models\Tagihan;
use App\Models\ProgresAtlet;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AtletController extends Controller
{
    // === 1. INDEX ===
    public function index(Request $request)
    {
        $query = Atlet::query();

        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }
        // [SESUAI DB LAMA] Gunakan 'kategori'
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $atlets = $query->latest()->paginate(10)->withQueryString();

        return view('admin.atlet.index', compact('atlets'));
    }

    public function create()
    {
        return view('admin.atlet.create');
    }

    // === 2. STORE (DIPERBAIKI SESUAI DB LAMA) ===
    public function store(Request $request)
    {
        // 1. Validasi (Sesuai nama input di Form View & Kolom Database Lama)
        $request->validate([
            // Akun
            'email'             => 'required|email|unique:users,email',
            'password'          => 'required|min:8',
            
            // Biodata
            'nama_lengkap'      => 'required|string|max:255',
            'tempat_lahir'      => 'required',
            'tanggal_lahir'     => 'required|date',
            'jenis_kelamin'     => 'required',
            
            // [PENTING] Ini nama input dari Form View Mas Kevin
            'jenjang_sekolah'   => 'required', 
            'nama_sekolah'      => 'required',
            'kategori'          => 'required', 
            
            // Orang Tua (Sesuai nama input form)
            'nama_orang_tua'    => 'required', 
            'no_hp_orang_tua'   => 'required', 
            
            'foto_profil'       => 'nullable|image|max:2048',
        ]);

        // 2. Logika Umur (PERTAHANKAN)
        $umur = Carbon::parse($request->tanggal_lahir)->age;
        $kategori = $request->kategori; // [SESUAIKAN INPUT]

        $rentangKU = [
            'KU-10' => ['min' => 5,  'max' => 10],
            'KU-12' => ['min' => 11, 'max' => 12],
            'KU-14' => ['min' => 13, 'max' => 14],
            'KU-16' => ['min' => 15, 'max' => 16],
            'KU-18' => ['min' => 17, 'max' => 18],
        ];

        if (isset($rentangKU[$kategori])) {
            if ($umur < $rentangKU[$kategori]['min'] || $umur > $rentangKU[$kategori]['max']) {
                return redirect()->back()->withInput()->withErrors([
                    'umur_salah' => "GAGAL: Umur $umur tahun tidak sesuai untuk $kategori."
                ]);
            }
        }

        // Cek Logika Sekolah
        if ($umur < 5) {
            return redirect()->back()->withInput()->withErrors(['sekolah_salah' => 'Atlet terlalu muda (Minimal 5 tahun).']);
        }

        // 3. Cek Ganda (PERTAHANKAN)
        $cekGanda = Atlet::where('nama_lengkap', $request->nama_lengkap)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->exists();

        if ($cekGanda) {
            return redirect()->back()->withInput()->withErrors(['ganda' => 'GAGAL: Atlet tersebut sudah terdaftar.']);
        }

        // 4. PROSES TRANSAKSI (User + Atlet)
        DB::beginTransaction();

        try {
            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')->store('foto-atlet', 'public');
            }

            // A. Buat User Login
            $user = User::create([
                'name'     => $request->nama_lengkap,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'atlet',
            ]);

            // B. Buat Data Atlet
            $atlet = Atlet::create([
                'user_id'           => $user->id,
                'nama_lengkap'      => $request->nama_lengkap,
                'nama_panggilan'    => $request->nama_panggilan,
                'tempat_lahir'      => $request->tempat_lahir,
                'tanggal_lahir'     => $request->tanggal_lahir,
                'jenis_kelamin'     => $request->jenis_kelamin,
                'alamat'            => $request->alamat,
                
                'jenjang_sekolah'   => $request->jenjang_sekolah,
                'nama_sekolah'      => $request->nama_sekolah,
                'kategori'          => $request->kategori,
                'posisi'            => $request->posisi,
                'no_hp_atlet'       => $request->no_hp_atlet, 
                
                'nama_orang_tua'    => $request->nama_orang_tua,
                'no_hp_orang_tua'   => $request->no_hp_orang_tua,
                
                // [FIX KUNCI 1] Ubah jadi Pending (Biar sama kayak Landing Page)
                'status'            => 'Pending',
                'tanggal_gabung'    => now(),
                'foto_profil'       => $fotoPath,
            ]);

            // [FIX KUNCI 2] AUTO-GENERATE TAGIHAN BULAN PERTAMA
            $bulanDaftar = now()->month;
            $tahunDaftar = now()->year;

            \App\Models\Tagihan::create([
                'atlet_id'        => $atlet->id,
                'jenis_tagihan'   => 'Pendaftaran & SPP', 
                'bulan'           => $bulanDaftar,
                'tahun'           => $tahunDaftar,
                'nominal'         => 100000,
                'tanggal_tagihan' => now(),
                'status'          => 'Belum Lunas',
            ]);

            // Notifikasi Atlet
            \App\Models\Notifikasi::create([
                'user_id'  => $user->id,
                'judul'    => 'Selamat Datang! 🎉',
                'pesan'    => 'Akun berhasil dibuat. Silakan selesaikan pembayaran Tagihan Pendaftaran agar fitur aplikasi terbuka.',
                'kategori' => 'tagihan',
                'is_read'  => false,
            ]);

            DB::commit();
            return redirect()->route('atlet.index')->with('success', 'Berhasil! Atlet dibuat & Tagihan perdana otomatis terbit.');

        } catch (\Exception $e) {
            // ... (Kodingan catch error tetap sama)
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // === 3. SHOW ===
    public function show(Atlet $atlet)
    {
        // Pasang "Ban Serep": Kalau tanggal_gabung null, pakai created_at
        $tanggalPatokan = $atlet->tanggal_gabung ?? $atlet->created_at;

        // LOGIKA MISI 2: Deteksi Materi Tertinggal
        $materiTertinggal = Jadwal::where('kategori', $atlet->kategori)
            ->whereDate('tanggal', '<', $tanggalPatokan)
            ->where('status', '!=', 'Dibatalkan') // Abaikan jadwal libur/batal
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('admin.atlet.show', compact('atlet', 'materiTertinggal'));
    }

    public function edit(Atlet $atlet)
    {
        return view('admin.atlet.edit', compact('atlet'));
    }

    // === 4. UPDATE (DIPERBAIKI SESUAI DB LAMA) ===
    public function update(Request $request, Atlet $atlet)
    {
        // Sesuaikan validasi update juga
        $request->validate([
            'nama_lengkap'    => 'required|string|max:255',
            'kategori'        => 'required|string', // BUKAN kategori_umur
            'tanggal_lahir'   => 'required|date',
            'foto_profil'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $umur = Carbon::parse($request->tanggal_lahir)->age;
        $kategori = $request->kategori;

        // Validasi Logic Umur (PERTAHANKAN)
        $rentangKU = [
            'KU-10' => ['min' => 5,  'max' => 10],
            'KU-12' => ['min' => 11, 'max' => 12],
            'KU-14' => ['min' => 13, 'max' => 14],
            'KU-16' => ['min' => 15, 'max' => 16],
            'KU-18' => ['min' => 17, 'max' => 18],
        ];

        if (isset($rentangKU[$kategori])) {
            if ($umur < $rentangKU[$kategori]['min'] || $umur > $rentangKU[$kategori]['max']) {
                return redirect()->back()->withInput()->withErrors([
                    'umur_salah' => "Update Gagal: Umur $umur tahun tidak sesuai untuk $kategori."
                ]);
            }
        }

        // Cek Ganda (Kecuali diri sendiri)
        $cekGanda = Atlet::where('nama_lengkap', $request->nama_lengkap)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->where('id', '!=', $atlet->id)
            ->exists();

        if ($cekGanda) {
            return redirect()->back()->withInput()->withErrors(['ganda' => 'Data bentrok dengan atlet lain.']);
        }
        
        // Ambil semua data input
        $data = $request->except(['foto_profil', 'email', 'password']);

        if ($request->hasFile('foto_profil')) {
            if ($atlet->foto_profil && Storage::disk('public')->exists($atlet->foto_profil)) {
                Storage::disk('public')->delete($atlet->foto_profil);
            }
            $path = $request->file('foto_profil')->store('foto-atlet', 'public');
            $data['foto_profil'] = $path;
        }

        // Update nama di tabel Users juga biar sinkron
        if($atlet->user) {
            $atlet->user->update(['name' => $request->nama_lengkap]);
        }

        $atlet->update($data);
        return redirect()->route('atlet.index')->with('success', 'Data atlet berhasil diperbarui.');
    }

    // === 5. DESTROY (HAPUS DATA + AKUN) ===
    public function destroy(Atlet $atlet)
    {
        // 1. Cari user yang nyantol
        $user = $atlet->user; 

        if ($atlet->foto_profil) {
             Storage::disk('public')->delete($atlet->foto_profil);
        }

        // 2. Hapus Data Atlet
        $atlet->delete();

        // 3. Hapus Akun Login (Jika ada)
        if ($user) {
            $user->delete();
        }

        return redirect()->route('atlet.index')->with('success', 'Data atlet dan akun login berhasil dihapus.');
    }

    // === 6. DOWNLOAD PDF ===
    public function downloadPDF($id)
    {
        $atlet = Atlet::findOrFail($id);
        $pdf = Pdf::loadView('admin.atlet.pdf_view', compact('atlet'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download('Biodata-' . $atlet->nama_lengkap . '.pdf');
    }

    // === 7. KIRIM RAPOR VIA WHATSAPP (SEMESTER BERJALAN + HYBRID NGROK) ===
    public function kirimRaporWA($id)
    {
        $atlet = Atlet::findOrFail($id);
        
        $targetHp = $atlet->no_hp_orang_tua ?: $atlet->no_hp_atlet;
        if (empty($targetHp)) {
            return redirect()->back()->with('error', 'Gagal: Nomor HP tidak ditemukan.');
        }

        $targetHp = preg_replace('/[^0-9]/', '', $targetHp);
        if (substr($targetHp, 0, 1) === '0') {
            $targetHp = '62' . substr($targetHp, 1);
        }

        // ==============================================================
        // 1. LOGIKA SEMESTER (GASAL / GENAP)
        // ==============================================================
        $now = Carbon::now();
        $year = $now->year;
        $month = $now->month;

        if ($month >= 7) {
            $namaSemester = 'Ganjil (Gasal)';
            $tahunAjaran = $year . '/' . ($year + 1);
            $startDate = Carbon::create($year, 7, 1)->startOfDay();
            $endDate = Carbon::create($year, 12, 31)->endOfDay();
        } else {
            $namaSemester = 'Genap';
            $tahunAjaran = ($year - 1) . '/' . $year;
            $startDate = Carbon::create($year, 1, 1)->startOfDay();
            $endDate = Carbon::create($year, 6, 30)->endOfDay();
        }

        // ==============================================================
        // 2. AMBIL DATA REAL SESUAI RENTANG SEMESTER
        // ==============================================================
        
        // A. Riwayat Latihan & Absensi
        $riwayat_latihan = Absensi::where('atlet_id', $atlet->id)
                            ->whereBetween('tanggal_latihan', [$startDate, $endDate])
                            ->orderBy('tanggal_latihan', 'asc')
                            ->get();

        $hadir = $riwayat_latihan->where('status', 'H')->count();
        $sakit = $riwayat_latihan->where('status', 'S')->count();
        $izin  = $riwayat_latihan->where('status', 'I')->count();
        $alpha = $riwayat_latihan->where('status', 'A')->count();

        // B. Keuangan & SPP (Tagihan di semester ini, atau yang belum lunas)
        $tagihans = Tagihan::where('atlet_id', $atlet->id)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->orderBy('created_at', 'desc')
                        ->get();
        // Hitung total tunggakan (sesuaikan dengan isi kolom status di databasemu)
        $total_tunggakan = $tagihans->where('status', 'Belum')->sum('nominal'); // Bisa 'Belum' atau 'Belum Lunas'

        // C. Evaluasi Akhir Pelatih (Ambil progres terbaru di semester ini)
        $last_progres = ProgresAtlet::where('atlet_id', $atlet->id)
                            ->whereBetween('tanggal', [$startDate, $endDate])
                            ->orderBy('tanggal', 'desc')
                            ->first();

        // ==============================================================
        // 3. GENERATE PDF RAPOR
        // ==============================================================
        $pdf = Pdf::loadView('admin.atlet.pdf_rapor_wa', compact(
            'atlet', 'namaSemester', 'tahunAjaran', 'riwayat_latihan', 
            'hadir', 'sakit', 'izin', 'alpha', 'tagihans', 'total_tunggakan', 'last_progres'
        )); 
        
        $namaFile = 'Rapor_Semester_' . str_replace(' ', '_', $atlet->nama_lengkap) . '_' . date('mY') . '.pdf';
        Storage::disk('public')->put('rapor/' . $namaFile, $pdf->output());
        
        // ==============================================================
        // 4. KIRIM WA (HYBRID NGROK)
        // ==============================================================
        $urlLokal = asset('storage/rapor/' . $namaFile); 
        $ngrokUrl = ''; // <-- ISI URL NGROK DI SINI JIKA MAU NGIRIM FILE ASLI
        
        $pesanWA = "📢 *INFO RAPOR EVALUASI LATIHAN* 🏀\n\n";
        $pesanWA .= "Halo Bapak/Ibu Wali dari *" . $atlet->nama_lengkap . "*,\n\n";
        $pesanWA .= "Berikut kami lampirkan *Rapor Evaluasi Latihan (Semester " . $namaSemester . ")* ananda.\n\n";
        $pesanWA .= "--------------------------------------\n";
        $pesanWA .= "*(Opsi Cadangan)* Jika dokumen file tidak muncul, silakan salin link berikut dan buka di browser:\n";
        $pesanWA .= "🔗 " . $urlLokal . "\n";
        $pesanWA .= "--------------------------------------\n\n";
        $pesanWA .= "Terima kasih atas dukungannya. Semangat terus! 🔥\n";
        $pesanWA .= "_Pesan otomatis Jethree Basketball_";

        $tokenFonnte = 'SqLCDpjTPVSgkJ2yJGKN'; 

        $curlData = array('target' => $targetHp, 'message' => $pesanWA);
        if ($ngrokUrl != '') {
            $curlData['file'] = $ngrokUrl . '/storage/rapor/' . $namaFile;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $curlData,
            CURLOPT_HTTPHEADER => array("Authorization: $tokenFonnte"),
        ));
        curl_exec($curl);
        curl_close($curl);

        return redirect()->back()->with('success', 'Rapor Semester berhasil dikirim ke WhatsApp!');
    }

    // Fungsi mencari nomor hp yang sudah digunakan orang tua
    public function checkHpOrtu(Request $request)
    {
        // Mengambil input nomor HP dari request AJAX
        $no_hp = $request->no_hp;

        // Mencari apakah ada atlet lain yang sudah menggunakan nomor HP tersebut
        $atlet_lama = \App\Models\Atlet::where('no_hp_orang_tua', $no_hp)->first();

        if ($atlet_lama) {
            // Jika ketemu, kirimkan nama orang tuanya kembali ke view
            return response()->json([
                'status' => 'ditemukan',
                'nama_orang_tua' => $atlet_lama->nama_orang_tua,
            ]);
        }

        // Jika tidak ketemu, kirim status kosong
        return response()->json([
            'status' => 'kosong'
        ]);
    }
}