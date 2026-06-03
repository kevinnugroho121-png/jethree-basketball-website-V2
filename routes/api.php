<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==============================================================================
// 1. IMPORT CONTROLLER (SEMUA CONTROLLER DIKUMPULKAN DI SINI BIAR RAPI)
// ==============================================================================
use App\Http\Controllers\Api\AuthController;          // Login & Logout
use App\Http\Controllers\Api\ApiPelatihController;    // Fitur Pelatih (Baru)
use App\Http\Controllers\Api\ApiAtletController;      // Fitur Atlet (Baru)
use App\Http\Controllers\Api\TagihanController;       // Tagihan Lama (Sudah Jalan)
use App\Http\Controllers\Api\JadwalController;        // Jadwal Umum
use App\Http\Controllers\Api\ApiNotifikasiController; // Notifikasi (Baru)
use App\Http\Controllers\Api\ApiPaymentController;    // Midtrans (Paling Baru)


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ===================================================
// A. RUTE PUBLIK (BISA DIAKSES TANPA LOGIN)
// ===================================================

// 1. Login
Route::post('/login', [AuthController::class, 'login']);

// 2. Midtrans Callback (WAJIB di luar middleware karena diakses server Midtrans)
Route::post('/payment/callback', [ApiPaymentController::class, 'midtransCallback']);


// ===================================================
// B. RUTE PRIVAT (WAJIB LOGIN / PUNYA TOKEN)
// ===================================================
Route::middleware('auth:sanctum')->group(function () {

    // 1. LOGOUT
    Route::post('/logout', [AuthController::class, 'logout']);

    // 2. MIDTRANS (Minta Token Bayar)
    Route::post('/payment/token', [ApiPaymentController::class, 'getSnapToken']);

    // ===================================================
    // 3. KHUSUS PELATIH
    // ===================================================
    Route::get('/pelatih/profile', [ApiPelatihController::class, 'profile']);
    Route::get('/pelatih/dashboard', [ApiPelatihController::class, 'dashboard']);
    Route::get('/pelatih/jadwal', [ApiPelatihController::class, 'listJadwal']);
    Route::get('/pelatih/jadwal/{id}/atlet', [ApiPelatihController::class, 'getAtletByJadwal']);
    Route::get('/pelatih/kalender-full', [ApiPelatihController::class, 'kalenderJadwal']);
    Route::post('/pelatih/absensi/store', [ApiPelatihController::class, 'storeAbsensi']);
    

    // ===================================================
    // 4. KHUSUS ATLET / ORANG TUA
    // ===================================================
    Route::get('/atlet/dashboard', [ApiAtletController::class, 'dashboard']); 
    Route::get('/atlet/profile', [ApiAtletController::class, 'profile']); 
    Route::get('/atlet/rapor', [ApiAtletController::class, 'rapor']);
    
    // Jadwal Latihan (Agar menu kalender di HP Orang Tua jalan)
    Route::get('/jadwal', [JadwalController::class, 'index']);

    // ===================================================
    // 5. FITUR TAGIHAN & PEMBAYARAN (YANG SUDAH JALAN)
    // ===================================================
    Route::get('/tagihan', [TagihanController::class, 'index']); 
    Route::post('/tagihan/{id}/upload', [TagihanController::class, 'uploadBukti']); 

    // ===================================================
    // 6. NOTIFIKASI
    // ===================================================
    // Menggunakan Controller Notifikasi yang BARU sesuai permintaan
    Route::get('/notifikasi', [ApiNotifikasiController::class, 'index']);

});