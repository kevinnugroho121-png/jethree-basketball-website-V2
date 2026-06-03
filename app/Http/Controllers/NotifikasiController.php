<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan daftar notifikasi (History/Riwayat)
     * Ini menjawab request: "Disimpan di riwayat saja tanpa hilang"
     */
    public function index()
    {
        // Kita ambil SEMUA notifikasi (Baik yang sudah dibaca maupun belum)
        // Jadi history-nya tidak akan hilang.
        $notifikasis = Notifikasi::where('user_id', Auth::id())
                                ->orderBy('created_at', 'desc')
                                ->paginate(6);

        // Arahkan ke view pengumuman
        if (view()->exists('atlet.notifikasi.index')) {
            return view('atlet.notifikasi.index', compact('notifikasis'));
        } else {
            return redirect()->back();
        }
    }

    /**
     * Menandai notifikasi sudah dibaca (Agar Pop-up BERHENTI MUNCUL)
     */
    public function markAsRead($id)
    {
        $notif = Notifikasi::find($id);

        if ($notif) {
            // [PERBAIKAN UTAMA]
            // Kode ini SAYA AKTIFKAN. 
            // Ini mengubah status di database jadi "Sudah Dibaca" (1).
            // Akibatnya: Pop-up tidak akan muncul lagi untuk notifikasi ini.
            
            // Pastikan kolom di tabelmu namanya 'is_read'. 
            // Jika namanya 'read_at', ganti baris bawah jadi: $notif->update(['read_at' => now()]);
            $notif->is_read = 1; 
            $notif->save();
        }

        // Redirect back agar halaman refresh dan pop-up hilang
        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }
}