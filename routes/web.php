<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;

// --- IMPORT MODEL ---
use App\Models\Pelatih;
use App\Models\Jadwal;

// --- IMPORT CONTROLLERS (ADMIN) ---
use App\Http\Controllers\Admin\AtletController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\KalenderController; 
use App\Http\Controllers\Admin\PelatihController;
use App\Http\Controllers\Admin\TagihanController;
use App\Http\Controllers\Admin\NotifikasiController as AdminNotifikasiController;
use App\Http\Controllers\Admin\DashboardController; 
use App\Http\Controllers\Admin\AbsensiController as AdminAbsensiController; 
use App\Http\Controllers\Admin\MasterMateriController;
use App\Http\Controllers\Admin\RekapPelatihController;

// --- IMPORT CONTROLLERS (UMUM/PELATIH) ---
use App\Http\Controllers\AbsensiController; 
use App\Http\Controllers\NotifikasiController as UserNotifikasiController;
use App\Http\Controllers\ProgresAtletController;

// --- IMPORT CONTROLLERS (DASHBOARD SPESIFIK) ---
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Atlet\DashboardController as AtletDashboardController;
use App\Http\Controllers\Pelatih\DashboardController as PelatihDashboardController;

// --- IMPORT CONTROLLER ATLET (TAGIHAN & PEMBAYARAN) ---
use App\Http\Controllers\Atlet\TagihanController as AtletTagihanController;
use App\Http\Controllers\Atlet\PembayaranController as AtletPembayaranController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// --- TAMBAHKAN RUTE PUBLIK INI UNTUK AJAX PENDAFTARAN ---
Route::post('/cek-wa-publik', [App\Http\Controllers\Admin\AtletController::class, 'checkHpOrtu'])->name('public.checkWa');

// --- LOGIKA UTAMA (PENGARAH / SWITCH DASHBOARD) ---
Route::get('/dashboard', function () {
    $role = Auth::user()->role; 

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'owner') {
        return redirect()->route('owner.dashboard');
    } elseif ($role === 'pelatih') {
        return redirect()->route('pelatih.dashboard');
    } elseif ($role === 'atlet') {
        return redirect()->route('atlet.dashboard');
    } else {
        return view('dashboard'); 
    }
})->middleware(['auth', 'verified'])->name('dashboard');


// --- GROUP ROUTE PROFILE ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ====================================================
// 1. GRUP ADMIN
// ====================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    
    // Dashboard Admin
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Manajemen Data (CRUD)
    // --- TAMBAHKAN 1 BARIS INI UNTUK AJAX WA ORANG TUA ---
    Route::post('/atlet/check-wa', [AtletController::class, 'checkHpOrtu'])->name('atlet.checkWa'); // AJAX WA orang tua
    Route::resource('atlet', AtletController::class);
    Route::get('atlet/{id}/pdf', [AtletController::class, 'downloadPDF'])->name('atlet.pdf');
    Route::post('/atlet/{id}/kirim-rapor', [\App\Http\Controllers\Admin\AtletController::class, 'kirimRaporWA'])->name('atlet.kirim-rapor');

    // --- MANAJEMEN JADWAL ---
    Route::resource('jadwal', JadwalController::class);
    
    // --- ABSENSI (Admin side) ---
    Route::get('/absensi', [AdminAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/jadwal/{id}/absensi', [AdminAbsensiController::class, 'create'])->name('absensi.create');
    Route::post('/jadwal/{id}/absensi', [AdminAbsensiController::class, 'store'])->name('absensi.store');

    // --- MANAJEMEN PELATIH (PERBAIKAN DISINI) ---
    // 1. Route PDF (Wajib ditaruh SEBELUM resource agar tidak tertimpa)
    Route::get('/pelatih/{id}/cetak-pdf', [PelatihController::class, 'cetakPdf'])->name('pelatih.cetakPdf');
    
    // 2. Route Resource (CRUD)
    Route::resource('pelatih', PelatihController::class);

    // --- KEUANGAN & TAGIHAN ---
    Route::post('tagihan/store-bulk', [TagihanController::class, 'storeBulk'])->name('tagihan.storeBulk');
    Route::get('tagihan/preview', [TagihanController::class, 'preview'])->name('tagihan.preview');
    Route::get('tagihan/cetak-pdf', [TagihanController::class, 'cetakPdf'])->name('tagihan.cetak_pdf');
    Route::get('tagihan/{id}/bukti', [TagihanController::class, 'lihatBukti'])->name('tagihan.bukti');
    Route::put('tagihan/{id}/verifikasi-lunas', [TagihanController::class, 'verifikasiLunas'])->name('tagihan.verifikasi_lunas');

    Route::resource('tagihan', TagihanController::class);
    
    Route::resource('notifikasi', AdminNotifikasiController::class);

    // Route untuk AJAX tarik data materi otomatis
    Route::get('/master-materi/get-by-kategori', [MasterMateriController::class, 'getByKategori'])->name('master-materi.getByKategori');

    // Route untuk AJAX cek pertemuan yang sudah ada
    Route::get('/master-materi/get-existing-pertemuan', [MasterMateriController::class, 'getExistingPertemuan'])->name('master-materi.getExistingPertemuan');

    // Route untuk Master Materi Latihan
    Route::resource('master-materi', MasterMateriController::class);

    // Route Rekap Pelatih
    Route::get('/rekap-pelatih', [RekapPelatihController::class, 'index'])->name('admin.rekap-pelatih');

    // Route Halaman Preview di dalam Admin
    Route::get('/rekap-pelatih/preview-semua', [RekapPelatihController::class, 'previewSemua'])->name('admin.rekap-pelatih.preview-semua');
    Route::get('/rekap-pelatih/preview/{id}', [RekapPelatihController::class, 'previewPelatih'])->name('admin.rekap-pelatih.preview-pelatih');
    
    // 2 Route Baru untuk PDF:
    Route::get('/rekap-pelatih/cetak-semua', [RekapPelatihController::class, 'cetakPdfSemua'])->name('admin.rekap-pelatih.cetak-semua');
    Route::get('/rekap-pelatih/cetak/{id}', [RekapPelatihController::class, 'cetakPdfPelatih'])->name('admin.rekap-pelatih.cetak-pelatih');
});


// ====================================================
// 2. GRUP OWNER
// ====================================================
Route::middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])
        ->name('owner.dashboard');
});


// ====================================================
// 3. GRUP PELATIH
// ====================================================
Route::middleware(['auth', 'role:pelatih'])->group(function () {
    
    // A. Dashboard Pelatih
    Route::get('/pelatih/dashboard', [PelatihDashboardController::class, 'index'])
        ->name('pelatih.dashboard');

    // B. Route Absensi Pelatih 
    Route::get('/pelatih/absensi', [AbsensiController::class, 'index'])->name('pelatih.absensi.index');
    
    // Gunakan prefix /pelatih/ agar tidak bentrok dengan route admin
    Route::get('/pelatih/jadwal/{id}/absensi', [AbsensiController::class, 'create'])->name('pelatih.absensi.create');
    Route::post('/pelatih/jadwal/{id}/absensi', [AbsensiController::class, 'store'])->name('pelatih.absensi.store');

    // C. Progres Atlet
    Route::get('/pelatih/progres', [ProgresAtletController::class, 'index'])->name('pelatih.progres.index');
    Route::get('/pelatih/progres/{id}/create', [ProgresAtletController::class, 'create'])->name('pelatih.progres.create');
    Route::post('/pelatih/progres', [ProgresAtletController::class, 'store'])->name('pelatih.progres.store');
});


// ====================================================
// 4. GRUP ATLET/ORANG TUA
// ====================================================
Route::middleware(['auth', 'role:atlet'])->group(function () {
    
    // A. Dashboard Atlet
    Route::get('/atlet/dashboard', [AtletDashboardController::class, 'index'])
        ->name('atlet.dashboard');

    // B. Halaman Tagihan & SPP
    Route::get('/atlet/tagihan', [AtletTagihanController::class, 'index'])
        ->name('atlet.tagihan.index');

    // C. Fitur Pembayaran
    Route::get('/atlet/bayar/{id_tagihan}', [AtletPembayaranController::class, 'create'])
        ->name('atlet.pembayaran.create');
        
    Route::post('/atlet/bayar/bulk', [AtletPembayaranController::class, 'bulkCreate'])
        ->name('atlet.pembayaran.bulk');

    Route::post('/atlet/bayar/store', [AtletPembayaranController::class, 'store'])
        ->name('atlet.pembayaran.store');

    // Di dalam group role:atlet
    Route::get('/atlet/cetak-rapor', [App\Http\Controllers\Atlet\DashboardController::class, 'cetakRapor'])->name('atlet.cetak_rapor');

    // Route BARU untuk Halaman Preview (Pembungkus)
    Route::get('/atlet/preview-rapor', [App\Http\Controllers\Atlet\DashboardController::class, 'previewRapor'])->name('atlet.preview_rapor');
});


// ====================================================
// 5. GRUP UMUM (SEMUA USER LOGIN BISA AKSES)
// ====================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/pengumuman', [UserNotifikasiController::class, 'index'])
        ->name('notifikasi.index.user'); 
        
    Route::get('/notifikasi/baca/{id}', [UserNotifikasiController::class, 'markAsRead'])
        ->name('notifikasi.markRead');

    Route::get('/kalender-latihan', [KalenderController::class, 'index'])->name('kalender.index');
    Route::get('/kalender/events', [KalenderController::class, 'getEvents'])->name('kalender.events');
});

require __DIR__.'/auth.php';