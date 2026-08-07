<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Atlet;
use Illuminate\Support\Facades\Storage;

class TagihanController extends Controller
{
    // === 1. LIST TAGIHAN (Khusus User yg Login) ===
    public function index(Request $request)
    {
        $user = $request->user();

        // Cek apakah user ini adalah atlet?
        $atlet = Atlet::where('user_id', $user->id)->first();

        if (!$atlet) {
            return response()->json([
                'success' => false,
                'message' => 'Data Atlet tidak ditemukan untuk akun ini.',
                'data'    => []
            ], 404);
        }

        // Ambil tagihan milik atlet ini saja dengan pengurutan kronologis global yang cerdas
        $tagihans = Tagihan::where('atlet_id', $atlet->id)
                           // 1. Kelompokkan 'Belum Lunas' & 'Menunggu Verifikasi' di atas (0), paksa 'Lunas' tenggelam di bawah (1)
                           ->orderByRaw("CASE WHEN status = 'Lunas' THEN 1 ELSE 0 END ASC")
                           // 2. Urutkan dari tahun terlama/tunggakan (2026) ke tahun terjauh (2027)
                           ->orderBy('tahun', 'asc')
                           // 3. Urutkan dari bulan terkecil (Januari) ke bulan terbesar (Desember)
                           ->orderBy('bulan', 'asc')
                           ->paginate(4);

        return response()->json([
            'success' => true,
            'message' => 'List Tagihan Saya',
            // Paginasi otomatis menghasilkan struktur metadata
            'data'    => $tagihans
        ]);
    }

    // === 2. UPLOAD BUKTI BAYAR ===
    public function uploadBukti(Request $request, $id)
    {
        // Cari Tagihan berdasarkan ID
        $tagihan = Tagihan::find($id);

        if (!$tagihan) {
            return response()->json(['success' => false, 'message' => 'Tagihan tidak ditemukan'], 404);
        }


        // REVISI POIN 6: Ubah 'file' menjadi 'image' agar Laravel mengecek header asli gambar (Anti-Shell Bypass)
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        // Proses Upload
        if ($request->hasFile('bukti_pembayaran')) {
            
            // Hapus bukti lama jika ada (biar server ga penuh)
            if ($tagihan->bukti_pembayaran) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }

            // REVISI POIN 6: Ambil file dan paksa rename total format nama filenya
            $file = $request->file('bukti_pembayaran');
            $extension = $file->getClientOriginalExtension(); // Mengunci ekstensi asli aman (.jpg/.png)
            
            // Generate nama baru yang acak bersih, membuang nama asli bawaan user yang rawan double extension
            $safeName = 'bukti_' . time() . '_' . uniqid() . '.' . $extension;
            
            // Simpan menggunakan storeAs dengan nama yang sudah disterilkan
            $path = $file->storeAs('bukti-bayar', $safeName, 'public');


            // Update Database & Naikkan Sinyal Status Menunggu Verifikasi
            // PENTING: Jika status tidak diset ke 'Menunggu Verifikasi', auto-sorting pucuk pada Web Admin tidak akan bekerja!
            $tagihan->update([
                'bukti_pembayaran' => $path,
                'status'           => 'Menunggu Verifikasi', 
            ]);

            // 💡 TAMBAHAN BARU: Kirim Notifikasi Internal Aplikasi (In-App) ke Semua Akun Admin
            $admins = \App\Models\User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                \App\Models\Notifikasi::create([
                    'user_id'     => $admin->id,
                    'sender_id'   => $request->user()->id, // 💡 TAMBAHAN: Catat ID user atlet dari HP sebagai pengirim
                    'target_role' => 'admin',             // 💡 TAMBAHAN: Kunci target role khusus untuk admin
                    'judul'       => 'Konfirmasi Pembayaran Baru 💰',
                    'pesan'       => 'Atlet ' . ($tagihan->atlet->nama_lengkap ?? 'Siswa') . ' telah mengunggah bukti transfer untuk SPP Bulan ' . $tagihan->bulan . '/' . $tagihan->tahun . '. Mohon segera diperiksa.',
                    'kategori'    => 'pembayaran',
                    'is_read'     => false,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload dan notifikasi telah dikirim ke Admin!',
                'data'    => $tagihan
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal upload gambar'], 400);
    }
}