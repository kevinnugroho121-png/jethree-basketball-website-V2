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

        // Ambil tagihan milik atlet ini saja
        // PERUBAHAN: Ganti ->get() menjadi ->paginate(10)
        $tagihans = Tagihan::where('atlet_id', $atlet->id)
                           ->orderBy('status', 'asc') // 'Belum Lunas' (B) < 'Lunas' (L)
                           ->orderBy('id', 'desc')
                           ->paginate(4); // Menampilkan maksimal 10 per halaman

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

        // Validasi Input Gambar
        $request->validate([
            'bukti_pembayaran' => 'required|image|max:5120', // Max 5MB
        ]);

        // Proses Upload
        if ($request->hasFile('bukti_pembayaran')) {
            
            // Hapus bukti lama jika ada (biar server ga penuh)
            if ($tagihan->bukti_pembayaran) {
                Storage::disk('public')->delete($tagihan->bukti_pembayaran);
            }

            // Simpan gambar baru
            $path = $request->file('bukti_pembayaran')->store('bukti-bayar', 'public');

            // Update Database
            $tagihan->update([
                'bukti_pembayaran' => $path,
                // Jangan langsung 'Lunas', tapi 'Menunggu Verifikasi' (opsional)
                // Atau biarkan 'Belum Lunas' sampai Admin yang klik Lunas
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diupload!',
                'data'    => $tagihan
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal upload gambar'], 400);
    }
}