<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiNotifikasiController extends Controller
{
    /**
     * MENAMPILKAN DAFTAR NOTIFIKASI DI MOBILE ATLET/COACH
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Mengambil notifikasi personal (looping) maupun broadcast global lama (NULL)
        $notif = DB::table('notifikasis')
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhereNull('user_id');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10); // Menampilkan 10 data per halaman agar load mobile lebih cepat

        return response()->json([
            'success' => true,
            'data'    => $notif,
        ]);
    }

    /**
     * 💡 PERBAIKAN TOTAL: Fungsi Broadcast dari HP Owner
     * Sistem otomatis melooping data per user agar status 'is_read' mandiri 
     * dan otomatis menembak WhatsApp Gateway Fonnte serentak.
     */
    public function broadcast(Request $request)
    {
        // 1. Validasi input dari Flutter (judul & pesan wajib diisi)
        $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        $senderId = $request->user()->id;

        // Ambil semua user tanpa terkecuali agar Superadmin juga kebagian di lonceng web
        $users = \App\Models\User::all();

        if ($users->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim broadcast, tidak ada pengguna target ditemukan.',
            ], 404);
        }

        // Pre-load data Atlet & Pelatih sekaligus sebelum looping (sekali tembak DB)
        $atletsByUserId   = \App\Models\Atlet::all()->keyBy('user_id');
        $pelatihsByUserId = \App\Models\Pelatih::all()->keyBy('user_id');

        $list_nomor_hp = [];
        $dataBatchNotif = [];
        $now = now()->toDateTimeString();

        foreach ($users as $user) {
            // Tampung data ke array (insert sekaligus di luar loop)
            $dataBatchNotif[] = [
                'user_id'     => $user->id,
                'sender_id'   => $senderId,
                'target_role' => 'semua', 
                'judul'       => $request->judul,
                'pesan'       => $request->pesan,
                'kategori'    => 'sistem',
                'is_read'     => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ];

            // 1. Ambil nomor HP Orang Tua / Atlet dari memori
            if ($user->role == 'atlet') {
                $atlet = $atletsByUserId->get($user->id);
                if ($atlet) {
                    $hpTarget = $atlet->no_hp_orang_tua ?: ($atlet->no_hp_atlet ?? '0');
                    if ($hpTarget && $hpTarget != '0') {
                        $hpClean = preg_replace('/[^0-9]/', '', $hpTarget);
                        $list_nomor_hp[] = preg_replace('/^0/', '62', $hpClean);
                    }
                }
            // 2. Ambil nomor HP Pelatih dari memori
            } elseif ($user->role == 'pelatih') {
                $pelatih = $pelatihsByUserId->get($user->id);
                if ($pelatih && $pelatih->no_hp) {
                    $hpClean = preg_replace('/[^0-9]/', '', $pelatih->no_hp);
                    $list_nomor_hp[] = preg_replace('/^0/', '62', $hpClean);
                }
            }
        }

        // Simpan semua notifikasi aplikasi secara bersamaan (Batch Insert)
        DB::table('notifikasis')->insert($dataBatchNotif);

        // =========================================================================
        // 4. JALUR TEMBAK WHATSAPP GATEWAY (FONNTE DARI MOBILE BROADCAST)
        // =========================================================================
        if (count($list_nomor_hp) > 0) {
            $stringTarget = implode(',', array_unique($list_nomor_hp));
            $tokenFonnte = env('FONNTE_TOKEN', 'SqLCDpjTPVSgkJ2yJGKN');

            $pesanWa = "📢 *PENGUMUMAN RESMI OWNER JETHREE BASKETBALL* 🏀\n\n" .
                       "*" . $request->judul . "*\n\n" .
                       $request->pesan . "\n\n" .
                       "Mohon diperhatikan dengan baik demi kenyamanan bersama. Terima kasih! 🙏";

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
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
                CURLOPT_SSL_VERIFYPEER => false
            ));
            
            curl_exec($curl);
            curl_close($curl);
        }

        // 5. Beri respon sukses balik ke aplikasi Flutter HP Owner
        return response()->json([
            'success' => true,
            'message' => 'Broadcast berhasil disebarkan ke ' . count($users) . ' pengguna via Aplikasi & WhatsApp!',
        ]);
    }
}