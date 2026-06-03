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

        // Load relasi data agar lengkap (Logic ini SUDAH BENAR, jangan diubah)
        if ($user->role == 'atlet') {
            $user->load('atlet'); 
        } elseif ($user->role == 'pelatih') {
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
        $user = $request->user();
        
        // Load data tambahan biar HP bisa tampilkan nama lengkap atlet/pelatih
        if ($user->role == 'atlet') {
            $user->load('atlet');
        } elseif ($user->role == 'pelatih') {
            $user->load('pelatih');
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Profil User',
            'data'    => $user
        ]);
    }
}