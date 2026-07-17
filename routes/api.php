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


    // 2b. UPLOAD FOTO PROFIL MANDIRI (Poin 19 - Bisa Diakses Semua Role)
    Route::post('/user/update-foto', [AuthController::class, 'updateFotoProfil']);



    // ===================================================
    // 3. KHUSUS PELATIH & OWNER (Akses Dibuka untuk Pelatih, Owner, dan Admin)
    // ===================================================
    Route::group(['middleware' => [function ($request, $next) {
        // Kita izinkan role pelatih, owner, dan admin untuk masuk ke rute ini
        if ($request->user() && !in_array($request->user()->role, ['pelatih', 'owner', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak! Anda tidak memiliki otoritas.'], 403);
        }
        return $next($request);
    }]], function () {


        Route::get('/pelatih/profile', [ApiPelatihController::class, 'profile']);
        Route::get('/pelatih/dashboard', [ApiPelatihController::class, 'dashboard']);
        Route::get('/pelatih/jadwal', [ApiPelatihController::class, 'listJadwal']);
        Route::get('/pelatih/jadwal/{id}/atlet', [ApiPelatihController::class, 'getAtletByJadwal']);
        Route::get('/pelatih/kalender-full', [ApiPelatihController::class, 'kalenderJadwal']);
        Route::post('/pelatih/absensi/store', [ApiPelatihController::class, 'storeAbsensi']);

        // 💡 SINKRONISASI MOBILE: Daftarkan rute penampung kiriman broadcast dari HP Owner & Admin
        Route::post('/owner/broadcast', [ApiNotifikasiController::class, 'broadcast']);
    });

    // ===================================================
    // 4. KHUSUS ATLET / ORANG TUA (Hanya User Ber-Role "atlet" yang Bisa Masuk)
    // ===================================================
    Route::group(['middleware' => [function ($request, $next) {
        if ($request->user() && $request->user()->role !== 'atlet') {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak! Anda bukan Atlet.'], 403);
        }
        return $next($request);
    }]], function () {

        Route::get('/atlet/dashboard', [ApiAtletController::class, 'dashboard']); 
        Route::get('/atlet/profile', [ApiAtletController::class, 'profile']); 
        Route::get('/atlet/rapor', [ApiAtletController::class, 'rapor']);
        

        // Jadwal Latihan (Agar menu kalender di HP Orang Tua jalan)
        Route::get('/jadwal', [JadwalController::class, 'index']);

        // REVISI POIN 4: Amankan rute payment & tagihan ke dalam grup ini agar token pelatih otomatis ditolak server
        Route::post('/payment/token', [ApiPaymentController::class, 'getSnapToken']);
        Route::get('/tagihan', [TagihanController::class, 'index']); 
        Route::post('/tagihan/{id}/upload', [TagihanController::class, 'uploadBukti']); 

        // PERBAIKAN SINKRONISASI: Tambahkan rute fallback notifikasi khusus atlet agar sinkron dengan Flutter
        Route::get('/atlet/notifikasi', [ApiNotifikasiController::class, 'index']);
    });





    // ===================================================
    // 6. NOTIFIKASI
    // ===================================================
    // Menggunakan Controller Notifikasi yang BARU sesuai permintaan
    Route::get('/notifikasi', [ApiNotifikasiController::class, 'index']);

});