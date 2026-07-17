<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tagihan; // Pastikan Model Tagihan sudah ada di App/Models/Tagihan.php
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class ApiPaymentController extends Controller
{
    // --- KONFIGURASI AWAL MIDTRANS ---
    public function __construct()
    {
        // Set konfigurasi Midtrans dari file .env
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set ke Development/Sandbox Environment (false = sandbox, true = production)
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        // Set sanitasi agar data aman
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        // Set 3DS untuk keamanan kartu kredit (wajib true)
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);
    }

    // --- 1. FUNGSI UNTUK MINTA TOKEN (Dipanggil dari HP) ---
    public function getSnapToken(Request $request)
    {
        // Validasi: Pastikan ID Tagihan dikirim
        $request->validate([
            'tagihan_id' => 'required|exists:tagihans,id',
        ]);

        // Ambil data tagihan beserta data atletnya
        $tagihan = Tagihan::with('atlet')->find($request->tagihan_id);

        // Cek: Jangan sampai bayar tagihan yang sudah lunas
        if ($tagihan->status == 'Lunas') {
            return response()->json([
                'success' => false, 
                'message' => 'Tagihan ini sudah lunas!'
            ], 400);
        }

        // Buat Order ID Unik (Format: TGH-IDTAGIHAN-TIMESTAMP)
        // Timestamp (waktu) ditambahkan agar ID selalu unik meski user membatalkan dan mengulang bayar
        $orderIdCustom = 'TGH-' . $tagihan->id . '-' . time();

        // Siapkan keranjang belanja untuk Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $orderIdCustom,
                'gross_amount' => (int) $tagihan->nominal, // Midtrans minta harga dalam Integer (tanpa koma)
            ],
            'customer_details' => [
                'first_name' => $tagihan->atlet->nama_lengkap ?? 'Atlet',
                'email' => $tagihan->atlet->user->email ?? 'noreply@jethree.com', // Email untuk kirim bukti bayar otomatis
            ],
            'item_details' => [
                [
                    'id' => $tagihan->id,
                    'price' => (int) $tagihan->nominal,
                    'quantity' => 1,
                    'name' => 'SPP ' . $tagihan->bulan . ' ' . $tagihan->tahun,
                ]
            ]
        ];

        try {


            // REVISI POIN 2: Gunakan createTransaction agar Token & Redirect URL di-generate otomatis secara dinamis oleh SDK Midtrans
            $transaction = Snap::createTransaction($params);

            // Kirim token dan redirect_url bawaan SDK asli balik ke Aplikasi HP
            return response()->json([
                'success' => true,
                'snap_token' => $transaction->token,
                'redirect_url' => $transaction->redirect_url, // Otomatis mendeteksi Sandbox/Production secara internal tanpa hardcode URL string
                'order_id_midtrans' => $orderIdCustom 
            ]);



        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // --- 2. FUNGSI CALLBACK (Diketuk oleh Midtrans secara otomatis) ---
    public function midtransCallback(Request $request)
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        
        // Buat signature key lokal untuk dicocokkan dengan data Midtrans (Keamanan)
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        // Jika kunci cocok, berarti data valid dari Midtrans
        if ($hashed == $request->signature_key) {
            
            // Ambil ID Tagihan Asli dari Order ID (pecah string 'TGH-123-9999')
            $orderIdParts = explode('-', $request->order_id);
            $tagihanId = $orderIdParts[1]; // Ambil angka '123'

            $tagihan = Tagihan::find($tagihanId);
            if (!$tagihan) return response()->json(['message' => 'Tagihan not found'], 404);

            // Cek Status Transaksi
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                // LUNAS: Update database beserta tanggal lunasnya secara permanen
                $tagihan->update([
                    'status' => 'Lunas',
                    'tanggal_lunas' => now()
                ]);

                // Otomatis membuat riwayat di tabel pembayarans agar masuk database SQLyog
                \App\Models\Pembayaran::create([
                    'tagihan_id'         => $tagihan->id,
                    'atlet_id'           => $tagihan->atlet_id,
                    'metode'             => 'Midtrans (' . ($request->payment_type ?? 'Otomatis') . ')',
                    'bukti_pembayaran'   => 'MIDTRANS-' . $request->transaction_id, 
                    'tanggal_pembayaran' => now(), 
                    'jumlah_dibayar'     => $tagihan->nominal, // 💡 SUNTIKAN KODE: Isi nominal agar database tidak crash!
                ]);

                // =========================================================================
                // 💡 SUNTIKAN BARU: Otomatis Mengisi Tabel Notifikasis untuk Lonceng
                // =========================================================================
                // Panggil relasi atlet secara dinamis untuk mengambil data nama dan user_id
                $tagihan->load('atlet');
                $atlet = $tagihan->atlet;

                if ($atlet) {
                    // 1. Kirim ke Lonceng Semua Akun Admin (Info Uang Masuk)
                    $admins = \App\Models\User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        \App\Models\Notifikasi::create([
                            'user_id'     => $admin->id,
                            'sender_id'   => $atlet->user_id, // Atlet sebagai pemicu notif
                            'target_role' => 'admin',
                            'judul'       => '💰 Pembayaran Midtrans Sukses!',
                            'pesan'       => 'Atlet ' . $atlet->nama_lengkap . ' telah berhasil membayar ' . $tagihan->jenis_tagihan . ' dengan Midtrans LUNAS.',
                            'kategori'    => 'tagihan',
                            'is_read'     => false,
                        ]);
                    }

                    // 2. Kirim ke HP Atlet Sendiri (Info Konfirmasi Sukses di Aplikasi Mobile)
                    \App\Models\Notifikasi::create([
                        'user_id'     => $atlet->user_id,
                        'sender_id'   => null, // Otomatis dari sistem
                        'target_role' => 'atlet',
                        'judul'       => '✅ Pembayaran Berhasil!',
                        'pesan'       => 'Terima kasih, pembayaran ' . $tagihan->jenis_tagihan . ' Anda sebesar Rp ' . number_format($tagihan->nominal) . ' telah lunas diverifikasi.',
                        'kategori'    => 'tagihan',
                        'is_read'     => false,
                    ]);
                }
                
            } elseif ($request->transaction_status == 'expire' || $request->transaction_status == 'cancel' || $request->transaction_status == 'deny') {


                // GAGAL: Update database
                $tagihan->update(['status' => 'Gagal']); // Atau kembalikan ke 'Belum Lunas'
            }

            return response()->json(['message' => 'Callback received successfully']);
        }

        return response()->json(['message' => 'Invalid Signature'], 403);
    }
}