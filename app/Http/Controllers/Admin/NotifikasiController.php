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

        // 1. CARI SIAPA PENERIMANYA (REVISI PENYARINGAN ROLE KETAT)
        $users = [];
        
        if ($request->target_role == 'semua') {
            // 💡 PERBAIKAN 1: Izinkan 'owner' masuk! Hanya kecualikan 'admin' saja agar Owner ikut menerima broadcast
            $users = User::whereNotIn('role', ['admin'])->get();
        } else {
            // Ambil user sesuai role spesifik pilihan (atlet / pelatih)
            $users = User::where('role', $request->target_role)->get();
        }

        // 2. LOOPING: KIRIM NOTIFIKASI INTERNAL + KOLEKSI NOMOR HP
        $list_nomor_hp = [];

        foreach ($users as $user) {
            // Tetap simpan notifikasi internal aplikasi (Jangan dihapus)
            Notifikasi::create([
                'user_id'     => $user->id,
                'sender_id'   => Auth::id(), 
                'target_role' => $request->target_role, // 💡 TAMBAHAN: Simpan target role asli (semua/atlet/pelatih)
                'judul'       => $request->judul,
                'pesan'       => $request->pesan,
                'kategori'    => 'info',
                'is_read'     => false,
                'link'        => null,
            ]);

            // Ambil nomor HP berdasarkan role untuk WhatsApp Broadcast (Poin 17)
            if ($user->role == 'atlet') {
                $atlet = \App\Models\Atlet::where('user_id', $user->id)->first();
                if ($atlet) {
                    // Prioritas nomor HP Orang Tua, kalau kosong pakai nomor Atlet
                    $hpTarget = $atlet->no_hp_orang_tua ?: ($atlet->no_hp_atlet ?? '0');
                    if ($hpTarget && $hpTarget != '0') {
                        // OPTIMASI Keamanan: Bersihkan spasi, strip, atau simbol + jika ada di database
                        $hpTarget = str_replace([' ', '-', '+'], '', $hpTarget);
                        $list_nomor_hp[] = preg_replace('/^0/', '62', $hpTarget);
                    }
                }
            } elseif ($user->role == 'pelatih') {
                $pelatih = \App\Models\Pelatih::where('user_id', $user->id)->first();
                if ($pelatih && $pelatih->no_hp) {
                    // OPTIMASI Keamanan: Bersihkan spasi, strip, atau simbol + jika ada di database
                    $hpTargetPelatih = str_replace([' ', '-', '+'], '', $pelatih->no_hp);
                    $list_nomor_hp[] = preg_replace('/^0/', '62', $hpTargetPelatih);
                }
            }
        }

        // =========================================================================
        // 3. JALUR TEMBAK WHATSAPP GATEWAY (FONNTE SERENTAK MULTI-TARGET)
        // =========================================================================
        if (count($list_nomor_hp) > 0) {
            // Hilangkan nomor ganda jika ada, lalu gabungkan dengan tanda koma sesuai dokumen Fonnte
            $stringTarget = implode(',', array_unique($list_nomor_hp));
            
            // DYNAMIC SWITCHER: Membaca token dari file .env. Jika di .env kosong, otomatis pakai token gratisan lokalmu saat ini!
            $tokenFonnte = env('FONNTE_TOKEN', 'SqLCDpjTPVSgkJ2yJGKN');

            $pesanWa = "📢 *PENGUMUMAN RESMI JETHREE BASKETBALL* 🏀\n\n" .
                       "*" . $request->judul . "*\n\n" .
                       $request->pesan . "\n\n" .
                       "Mohon diperhatikan dengan baik demi kenyamanan bersama. Terima kasih! 🙏";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10, // Batasi timeout agar eksekusi web admin tetap responsif
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $stringTarget,
                    'message' => $pesanWa,
                    'countryCode' => '62',
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $tokenFonnte
                ),
                CURLOPT_SSL_VERIFYPEER => false // Solusi anti-eror SSL saat kamu testing di localhost
            ));
            
            curl_exec($curl);
            curl_close($curl);
        }

        return redirect()->route('notifikasi.index')
            ->with('success', 'Pengumuman berhasil disebarkan ke ' . count($users) . ' pengguna via Aplikasi & WhatsApp Broadcast.');


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