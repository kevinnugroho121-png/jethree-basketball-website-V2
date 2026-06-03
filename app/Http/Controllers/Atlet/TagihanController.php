<?php

namespace App\Http\Controllers\Atlet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Atlet;
use App\Models\Tagihan;

class TagihanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Cari data atlet
        $atlet = Atlet::where('user_id', $user->id)->first();

        // Cek jika data atlet belum ada
        if (!$atlet) {
            return redirect()->route('atlet.dashboard')->with('error', 'Profil atlet belum ditemukan.');
        }

        // 1. AMBIL SEMUA DATA (Untuk Tampilan Kalender Kiri)
        $semua_tagihan = Tagihan::where('atlet_id', $atlet->id)
                            ->orderBy('tahun', 'desc') // Tahun terbaru di atas
                            ->orderBy('bulan', 'asc')  // Bulan urut Jan-Des
                            ->get();

        // 2. FILTER TAGIHAN BELUM LUNAS (Khusus untuk Badge Merah 'X Invoice')
        // Kita ambil dari data di atas, tidak perlu query database lagi
        $tagihan_belum = $semua_tagihan->where('status', 'Belum Lunas');

        // 3. FILTER RIWAYAT PEMBAYARAN (Khusus untuk Tabel Kanan)
        $history_bayar = $semua_tagihan->where('status', 'Lunas')->sortByDesc('updated_at');

        // Kirim KETIGANYA ke View agar tidak ada error "Undefined Variable"
        return view('atlet.tagihan.index', compact('semua_tagihan', 'tagihan_belum', 'history_bayar'));
    }
}