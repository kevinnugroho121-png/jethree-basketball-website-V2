<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    // === FUNGSI LOGIN ===
    public function login(Request $request)
    {
        // [REVISI DOSEN] Validasi Username & Password Diperketat
        $request->validate([
            'email' => 'required|email', // Wajib format email (ada @)
            'password' => [
                'required',
                'min:8',              // Minimal 8 karakter
                'regex:/[a-zA-Z]/',   // Harus mengandung Huruf (a-z atau A-Z)
                'regex:/[0-9]/',      // Harus mengandung Angka
                'regex:/[@$!%*#?&]/', // Harus mengandung Simbol
            ],
        ], [
            // Pesan Error Bahasa Indonesia (Biar jelas di HP)
            'email.email' => 'Username harus format email yang valid (pakai @).',
            'password.min' => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus kombinasi Huruf, Angka, dan Simbol (@$!%*#?&).',
        ]);

        // Cek apakah email & password cocok di database
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Login Gagal! Email atau Password salah.',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Load relasi data agar lengkap (Owner diikutkan ke dalam rumpun pelatih)
        if ($user->role == 'atlet') {
            $user->load('atlet'); 
        } elseif ($user->role == 'pelatih' || $user->role == 'owner') {
            $user->load('pelatih');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Login Berhasil!',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user,
        ]);
    }

    // === FUNGSI LOGOUT ===
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    // === FUNGSI CEK PROFIL ===
    public function profile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        
        // Load data tambahan sesuai role masing-masing secara lengkap
        if ($user->role == 'atlet') {
            $user->load('atlet');
        } elseif ($user->role == 'pelatih' || $user->role == 'owner') {
            // 💡 REVISI: Diubah menjadi kategoris (pakai s) sesuai dengan nama fungsi di Model Pelatih
            $user->load(['pelatih.kategoris']); 
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Profil User Sukses Sinkron',
            'role'    => $user->role, // Memudahkan Flutter untuk cek role via if-else dashboard
            'data'    => $user
        ]);
    }

    // =========================================================================
    // [BARU] 4. UPDATE FOTO PROFIL MANDIRI VIA MOBILE (POIN 19)
    // =========================================================================
    public function updateFotoProfil(Request $request)
    {


        // REVISI POIN 19: Ubah 'file' menjadi 'image' agar Laravel mengecek header asli gambar (Anti-Shell Bypass)
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // REVISI POIN 19: Ambil file dan paksa rename total format nama filenya agar steril & seragam
        $file = $request->file('foto_profil');
        $extension = $file->getClientOriginalExtension();
        $safeName = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;

        // Simpan menggunakan storeAs dengan nama yang sudah disterilkan
        $path = $file->storeAs('foto-profil', $safeName, 'public');



        // Skenario A: Jika yang login adalah Atlet
        if ($user->role == 'atlet') {
            $atlet = \App\Models\Atlet::where('user_id', $user->id)->first();
            if ($atlet) {
                // Hapus file foto lama di storage jika sebelumnya sudah ada
                if ($atlet->foto_profil) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($atlet->foto_profil);
                }
                $atlet->update(['foto_profil' => $path]);
            }
        } 
        // Skenario B: Jika yang login adalah Pelatih atau Owner (Poin 69)
        elseif ($user->role == 'pelatih' || $user->role == 'owner') {
            $pelatih = \App\Models\Pelatih::where('user_id', $user->id)->first();
            if ($pelatih) {
                // Hapus file foto lama di storage jika sebelumnya sudah ada
                if ($pelatih->foto_profil) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($pelatih->foto_profil);
                }
                $pelatih->update(['foto_profil' => $path]);
            }
        }



        // 💡 SUNTIKAN BARU: Load ulang relasi data terbaru agar data yang dikirim ke mobile sudah terupdate
        if ($user->role == 'atlet') {
            $user->load('atlet');
        } elseif ($user->role == 'pelatih' || $user->role == 'owner') {
            $user->load('pelatih');
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Foto profil berhasil diperbarui, bro!',
            'foto_url'  => url('storage/' . $path), // 💡 PERUBAHAN: Mengembalikan URL penuh (http://...) agar Flutter bisa pakai Image.network()
            'user'      => $user                    // 💡 TAMBAHAN: Menyertakan data user terbaru agar dashboard mobile langsung sinkron otomatis
        ]);
    }
}