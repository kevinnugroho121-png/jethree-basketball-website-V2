<?php

namespace App\Http\Controllers\Atlet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Import Model
use App\Models\Tagihan;
use App\Models\Pembayaran; 

class PembayaranController extends Controller
{
    /**
     * 1. FORM BAYAR SATUAN (Dari tombol kecil 'Bayar' di tabel riwayat/list)
     */
    public function create($id_tagihan)
    {
        $tagihan = Tagihan::findOrFail($id_tagihan);
        
        // Kita bungkus jadi 'collection' agar formatnya sama dengan fitur bulk
        $selected_tagihan = collect([$tagihan]); 
        $total_bayar = $tagihan->nominal;

        return view('atlet.pembayaran.create', compact('selected_tagihan', 'total_bayar'));
    }

    /**
     * 2. FORM BAYAR MERAPEL (Dari tombol 'Bayar Sekarang' hasil checkbox)
     * [INI FITUR BARU YANG KITA BUAT TADI]
     */
    public function bulkCreate(Request $request)
    {
        // Validasi: Harus ada tagihan yang dipilih dari checkbox
        if (!$request->has('tagihan_ids')) {
            return redirect()->back()->with('error', 'Pilih minimal satu tagihan untuk dibayar.');
        }

        // Ambil semua data tagihan berdasarkan ID yang dicentang
        $selected_tagihan = Tagihan::whereIn('id', $request->tagihan_ids)->get();
        
        // Hitung total otomatis
        $total_bayar = $selected_tagihan->sum('nominal');

        // Kirim ke view yang sama
        return view('atlet.pembayaran.create', compact('selected_tagihan', 'total_bayar'));
    }

    /**
     * 3. PROSES SIMPAN (Menangani Single maupun Bulk)
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'tagihan_ids'      => 'required|array', // Wajib Array (kumpulan ID)
            'tagihan_ids.*'    => 'exists:tagihans,id',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
        ]);

        // 2. Upload Gambar (Cukup sekali upload untuk semua tagihan yang dipilih)
        $path = $request->file('bukti_pembayaran')->store('bukti_bayar', 'public');

        // 3. LOOPING: Simpan Pembayaran untuk SETIAP bulan yang dipilih
        foreach ($request->tagihan_ids as $id) {
            $tagihan = Tagihan::find($id);

            // Buat data pembayaran per bulan
            Pembayaran::create([
                'tagihan_id'       => $tagihan->id,
                'atlet_id'         => Auth::user()->atlet->id ?? $tagihan->atlet_id, // Safety check
                'metode'           => 'Manual Transfer',
                'bukti_pembayaran' => $path, // Bukti fotonya sama untuk semua
                'tanggal_pembayaran' => now(),
                'jumlah_dibayar'   => $tagihan->nominal, // Nominal sesuai tagihan bulan itu
                'status'           => 'Menunggu Verifikasi'
            ]);

            // Update status tagihan bulan itu jadi 'Menunggu Verifikasi'
            $tagihan->update([
                'status' => 'Menunggu Verifikasi',
                'bukti_pembayaran' => $path
            ]);
        }

        // ==========================================
        // [FITUR BARU] TEMBAK NOTIFIKASI KE ADMIN
        // ==========================================
        $admin = \App\Models\User::where('role', 'admin')->first(); // Cari superadmin
        
        if ($admin) {
            $namaAtlet = Auth::user()->name;
            $jumlahBulan = count($request->tagihan_ids);
            
            \App\Models\Notifikasi::create([
                'user_id'  => $admin->id,
                'judul'    => 'Ada Pembayaran Baru! 💰',
                'pesan'    => "Atlet {$namaAtlet} telah membayar {$jumlahBulan} bulan Tagihan/SPP. Segera cek menu Keuangan untuk Verifikasi Lunas.",
                'kategori' => 'pembayaran',
                'is_read'  => false,
            ]);
        }
        // ==========================================

        // 4. Kembali ke Halaman Tagihan
        return redirect()->route('atlet.tagihan.index')
                         ->with('success', 'Pembayaran berhasil dikirim! Admin akan memverifikasi data Anda.');
    }
}