<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// 1. PANGGIL CONTROLLER BARU KITA
use App\Http\Controllers\Api\AuthController;       // Untuk Login
use App\Http\Controllers\Api\ApiPelatihController; // Logika Baru Pelatih (Dribble/Pass/Shoot)
use App\Http\Controllers\Api\ApiAtletController;   // Logika Baru Atlet (Grafik & Profil KU)

// 2. CONTROLLER LAMA (YANG SUDAH JALAN TETAP DIPAKAI)
use App\Http\Controllers\Api\TagihanController;    // Untuk Pembayaran (Katanya sudah jalan)
use App\Http\Controllers\Api\NotifikasiController; // Untuk Notifikasi
use App\Http\Controllers\Api\JadwalController;     // Jadwal Umum

// [PENTING] Pakai Controller Notifikasi yang BARU (ApiNotifikasiController)
use App\Http\Controllers\Api\ApiNotifikasiController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Api\ApiPaymentController;

// 1. Route untuk Mobile minta Token (Harus Login Dulu)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/payment/token', [ApiPaymentController::class, 'getSnapToken']);
    });

    // 2. Route untuk Midtrans lapor status (JANGAN pakai middleware auth, karena Midtrans itu orang asing)
    Route::post('/payment/callback', [ApiPaymentController::class, 'midtransCallback']);

    // === RUTE PUBLIK (Bisa Diakses Tanpa Token) ===
    Route::post('/login', [AuthController::class, 'login']);

    // === RUTE PRIVAT (Wajib Login / Punya Token) ===
    Route::middleware('auth:sanctum')->group(function () {
    
    
    
    // 1. AUTHENTICATION
    Route::post('/logout', [AuthController::class, 'logout']);

    // ===================================================
    // 2. KHUSUS PELATIH (LOGIKA BARU)
    // ===================================================
    // [BARU - WAJIB ADA] Profil Pelatih (Supaya Dashboard Pelatih muncul namanya)
    Route::get('/pelatih/profile', [ApiPelatihController::class, 'profile']);

    // Dashboard Pelatih (Cek Jadwal Hari Ini)
    Route::get('/pelatih/dashboard', [ApiPelatihController::class, 'dashboard']);
    
    // List Semua Jadwal (Untuk Menu Jadwal)
    Route::get('/pelatih/jadwal', [ApiPelatihController::class, 'listJadwal']);
    
    // Ambil daftar atlet BERDASARKAN Jadwal yang dipilih
    Route::get('/pelatih/jadwal/{id}/atlet', [ApiPelatihController::class, 'getAtletByJadwal']);
    
    // Simpan Absensi & Nilai
    Route::post('/pelatih/absensi/store', [ApiPelatihController::class, 'storeAbsensi']);

    // ===================================================
    // 3. KHUSUS ATLET / ORANG TUA (LOGIKA BARU)
    // ===================================================
    // Dashboard Atlet (Grafik & Profil Ringkas)
    Route::get('/atlet/dashboard', [ApiAtletController::class, 'dashboard']); 
    
    // Profil Lengkap (Fix KU--)
    Route::get('/atlet/profile', [ApiAtletController::class, 'profile']); 

    // Rapor Otomatis
    Route::get('/atlet/rapor', [ApiAtletController::class, 'rapor']);

    // [BARU] JADWAL LATIHAN KHUSUS ATLET
    // Agar menu 'Jadwal' dan 'Kalender' di HP bisa jalan
    Route::get('/jadwal', [App\Http\Controllers\Api\JadwalController::class, 'index']);


    // ===================================================
    // 4. FITUR LAIN (YANG KATANYA SUDAH JALAN)
    // ===================================================
    // Tagihan & Pembayaran
    Route::get('/tagihan', [TagihanController::class, 'index']); 
    Route::post('/tagihan/{id}/upload', [TagihanController::class, 'uploadBukti']); 

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);

    // Route untuk Notifikasi / Pengumuman
    Route::get('/notifikasi', [App\Http\Controllers\Api\ApiNotifikasiController::class, 'index']);
});