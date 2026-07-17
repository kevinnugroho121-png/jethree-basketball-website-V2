<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Atlet;
use App\Models\Tagihan;
use App\Models\Notifikasi;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // ==========================================
        // 1. VALIDASI DATA (SUDAH DISESUAIKAN DENGAN NAME HTML)
        // ==========================================
        $request->validate([
            // Validasi Akun
            'name'             => ['required', 'string', 'max:255'], 
            'email'            => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'         => ['required', 'confirmed', Rules\Password::defaults()],

            // Validasi Data Atlet
            'nama_panggilan'   => ['nullable', 'string', 'max:50'],
            'tempat_lahir'     => ['required', 'string', 'max:255'],
            'tgl_lahir'        => ['required', 'date', 'before:-5 years'], // Di HTML namanya tgl_lahir
            'jenis_kelamin'    => ['required', 'in:Laki-laki,Perempuan'],
            'no_hp'            => ['required', 'numeric'],                 // Di HTML namanya no_hp
            'alamat'           => ['nullable', 'string'],                  // Di HTML namanya alamat
            
            // Sekolah & Ortu
            'jenjang_sekolah'  => ['required', 'string'],
            'nama_sekolah'     => ['required', 'string', 'max:255'],
            'posisi'           => ['nullable', 'string'],
            'nama_ortu'        => ['required', 'string', 'max:255'],       // Di HTML namanya nama_ortu
            'no_hp_ortu'       => ['required', 'numeric'],                 // Di HTML namanya no_hp_ortu
            
            // Foto
            'foto_profil'      => ['nullable', 'image', 'max:2048'],
        ], [
            'tgl_lahir.before'   => 'Maaf, usia minimal untuk mendaftar adalah 5 tahun.',
            'email.unique'       => 'Email ini sudah terdaftar, silakan gunakan email lain.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);




        // ... (Kodingan validasi $request->validate yang di atas tetap sama) ...

        // ==========================================
        // [FITUR BARU] CEK DATA GANDA (SAMA PERSIS DENGAN ADMIN)
        // ==========================================
        $cekGanda = Atlet::where('nama_lengkap', $request->name)
            ->where('tanggal_lahir', $request->tgl_lahir)
            ->exists();

        if ($cekGanda) {
            return redirect()->back()->withInput()->withErrors([
                'ganda' => 'GAGAL: Atlet dengan Nama dan Tanggal Lahir tersebut sudah terdaftar di sistem kami.'
            ]);
        }

        
        // ==========================================
        // 2. PROSES TRANSAKSI KE DATABASE
        // ==========================================
        DB::beginTransaction();

        try {
            // A. Proses Upload Foto Profil
            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')->store('foto-atlet', 'public');
            }

            // B. Buat Akun User (Login)
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'atlet',
            ]);

            // C. Hitung Umur & Tentukan Kategori (KU) berdasarkan tgl_lahir
            $usia = \Carbon\Carbon::parse($request->tgl_lahir)->age;
            
            $kategoriBase = 'KU-18'; // Default dasar
            if($usia <= 10) $kategoriBase = 'KU-10';
            elseif($usia <= 12) $kategoriBase = 'KU-12';
            elseif($usia <= 14) $kategoriBase = 'KU-14';
            elseif($usia <= 16) $kategoriBase = 'KU-16';

            // 💡 SUNTIKAN BARU: Gabungkan Kategori Angka dengan Gender agar sinkron dengan HTML
            $genderSuffix = ($request->jenis_kelamin === 'Laki-laki') ? ' Putra' : ' Putri';
            $kategori = $kategoriBase . $genderSuffix;
            
            // D. Buat Data Atlet (Menerjemahkan dari HTML ke Database)
            $atlet = Atlet::create([
                'user_id'         => $user->id,
                'nama_lengkap'    => $request->name,
                'nama_panggilan'  => $request->nama_panggilan,
                'tempat_lahir'    => $request->tempat_lahir,
                'tanggal_lahir'   => $request->tgl_lahir,         // HTML -> DB
                'jenis_kelamin'   => $request->jenis_kelamin,
                'alamat'          => $request->alamat,            // HTML -> DB
                
                'jenjang_sekolah' => $request->jenjang_sekolah,
                'nama_sekolah'    => $request->nama_sekolah,
                'kategori'        => $kategori,
                'posisi'          => $request->posisi ?? 'Belum Ditentukan',
                
                'no_hp_atlet'     => $request->no_hp,             // HTML -> DB
                'nama_orang_tua'  => $request->nama_ortu,         // HTML -> DB
                'no_hp_orang_tua' => $request->no_hp_ortu,        // HTML -> DB
                
                'status'          => 'Pending', // Mengunci fitur web sebelum lunas pendaftaran
                'tanggal_gabung'  => now(),
                'foto_profil'     => $fotoPath,
            ]);

            // E. AUTO-GENERATE TAGIHAN BULAN PERTAMA
            $bulanDaftar = now()->month;
            $tahunDaftar = now()->year;

            Tagihan::create([
                'atlet_id'        => $atlet->id,
                'jenis_tagihan'   => 'Pendaftaran & SPP', 
                'bulan'           => $bulanDaftar,
                'tahun'           => $tahunDaftar,
                'nominal'         => 100000,
                'tanggal_tagihan' => now(),
                'status'          => 'Belum Lunas',
            ]);

            // F. Buat Notifikasi Awal (Untuk Atlet)
            Notifikasi::create([
                'user_id'     => $user->id,
                'target_role' => 'atlet', // 💡 TAMBAHAN: Agar jenis target tercatat rapi
                'judul'       => 'Selamat Datang! 🎉',
                'pesan'       => 'Pendaftaran berhasil. Silakan selesaikan pembayaran Tagihan Pendaftaran Anda agar fitur aplikasi terbuka.',
                'kategori'    => 'tagihan',
                'is_read'     => false,
            ]);

            // 💡 TAMBAHAN BARU: Kirim Notifikasi Lonceng ke Semua Akun Admin
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notifikasi::create([
                    'user_id'     => $admin->id,
                    'sender_id'   => $user->id,       // 💡 TAMBAHAN: Catat ID user atlet baru sebagai pemicu/pengirim
                    'target_role' => 'admin',       // 💡 TAMBAHAN: Sesuai dengan tujuan role
                    'judul'       => 'Pendaftaran Atlet Baru 🏀',
                    'pesan'       => 'Seorang atlet baru bernama ' . $atlet->nama_lengkap . ' telah mendaftar ke sistem. Mohon periksa berkas dan aktifkan statusnya.',
                    'kategori'    => 'sistem',
                    'is_read'     => false,
                ]);
            }

            DB::commit();

            // Trigger Event Registrasi Bawaan Laravel
            event(new Registered($user));

            // [HAPUS AUTO LOGIN]
            return redirect()->route('login')->with('success', 'Pendaftaran Berhasil! Silakan login menggunakan email & password Anda.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}