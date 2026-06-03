<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\User; // <--- Perlu ini untuk mencari target broadcast
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * MENAMPILKAN DAFTAR NOTIFIKASI YANG SUDAH DIKIRIM
     */
    public function index()
    {
        // Menampilkan 20 notifikasi terakhir yang dikirim sistem
        $notifikasis = Notifikasi::with('user')->orderBy('created_at', 'desc')->paginate(5);
        return view('admin.notifikasi.index', compact('notifikasis'));
    }

    /**
     * FORM BUAT PENGUMUMAN BARU
     */
    public function create()
    {
        return view('admin.notifikasi.create');
    }

    /**
     * PROSES BROADCAST (KIRIM PESAN KE BANYAK ORANG)
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:255',
            'pesan'       => 'required|string', // Dulu 'isi', sekarang 'pesan'
            'target_role' => 'required|string', // Pilihan: 'semua', 'atlet', 'pelatih'
        ]);

        // 1. CARI SIAPA PENERIMANYA
        $users = [];
        
        if ($request->target_role == 'semua') {
            // Ambil semua user KECUALI admin sendiri
            $users = User::where('role', '!=', 'admin')->get();
        } else {
            // Ambil user sesuai role (atlet/pelatih)
            $users = User::where('role', $request->target_role)->get();
        }

        // 2. LOOPING: KIRIM PESAN SATU PER SATU (BLASTING)
        // Ini kuncinya agar setiap orang dapat notif & pop-up sendiri-sendiri
        foreach ($users as $user) {
            Notifikasi::create([
                'user_id' => $user->id,          // Penerima
                'judul'   => $request->judul,
                'pesan'   => $request->pesan,
                'kategori'    => 'info',             // Warna Biru (Info Umum)
                'is_read' => false,              // Agar muncul Pop-up
                'link'    => null,               // Pengumuman umum biasanya tidak ada link khusus
            ]);
        }

        return redirect()->route('notifikasi.index')
            ->with('success', 'Pengumuman berhasil disebarkan ke ' . count($users) . ' pengguna.');
    }

    /**
     * HAPUS NOTIFIKASI
     */
    public function destroy($id)
    {
        $notifikasi = Notifikasi::find($id);
        if($notifikasi) {
            $notifikasi->delete();
        }
        
        return redirect()->route('notifikasi.index')
            ->with('success', 'Notifikasi berhasil dihapus.');
    }
    
    // CATATAN: Fitur Edit/Update ditiadakan untuk pengumuman Broadcast 
    // karena pesannya sudah tersebar menjadi ratusan baris data (individual).
    // Kalau salah ketik, Admin disarankan hapus dan buat baru.
}