<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelatih;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; 
use Barryvdh\DomPDF\Facade\Pdf; // <--- WAJIB DITAMBAH

class PelatihController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelatih::query();

        if ($request->filled('search')) {
            $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        $pelatihs = $query->latest()->paginate(5);
        return view('admin.pelatih.index', compact('pelatihs'));
    }

    public function create()
    {
        return view('admin.pelatih.create');
    }

    // === STORE: BUAT AKUN + DATA PELATIH + VALIDASI UMUR + FOTO ===
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            // Akun Login
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|min:8',
            
            // Data Pelatih
            'nama_lengkap'    => 'required|string|max:255',
            'tanggal_lahir'   => 'required|date|before:-17 years', // Minimal 17/18 Tahun
            'no_hp'           => 'required|numeric',
            'status'          => 'required',
            'lisensi'         => 'nullable|string', // Tambahan: Lisensi
            'alamat'          => 'nullable|string', // Tambahan: Alamat
            'foto_profil'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori_fokus'  => 'required|string', // ⚡ BARU: Mengunci kategori fokus pelatih
            'gender_fokus'    => 'required|string', // ⚡ BARU: Mengunci gender fokus pelatih
        ], [
            'tanggal_lahir.before' => 'Maaf, Umur Coach minimal harus 17 tahun.',
            'foto_profil.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        // 2. Cek Ganda
        $cekGanda = Pelatih::where('nama_lengkap', $request->nama_lengkap)
            ->where('tanggal_lahir', $request->tanggal_lahir)
            ->exists();
        
        if($cekGanda) {
            return redirect()->back()->withInput()
                ->withErrors(['ganda' => 'GAGAL: Coach dengan Nama dan Tanggal Lahir tersebut sudah terdaftar.']);
        }

        // 3. PROSES SIMPAN (TRANSAKSI)
        DB::beginTransaction();
        try {
            // A. Upload Foto (Jika Ada)
            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')->store('foto-pelatih', 'public');
            }

            // B. Buat User Login
            $user = User::create([
                'name'     => $request->nama_lengkap,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'pelatih',
            ]);

            // C. Buat Data Pelatih (Link ke User)
            Pelatih::create([
                'user_id'       => $user->id,
                'nama_lengkap'  => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'tempat_lahir'  => $request->tempat_lahir ?? '-', // Default strip jika kosong
                'no_hp'         => $request->no_hp,
                'status'        => $request->status,
                'lisensi'       => $request->lisensi, // Simpan Lisensi
                'alamat'        => $request->alamat,   // Simpan Alamat
                'foto_profil'   => $fotoPath,
                'kategori_fokus'=> $request->kategori_fokus, // ⚡ BARU: Masuk ke database pelatihs
                'gender_fokus'  => $request->gender_fokus,   // ⚡ BARU: Masuk ke database pelatihs
                // SPESIALISASI SUDAH DIHAPUS
            ]);

            DB::commit();
            return redirect()->route('pelatih.index')->with('success', 'Berhasil! Akun dan Data Coach telah dibuat.');

        } catch (\Exception $e) {
            DB::rollback();
            if ($fotoPath && Storage::disk('public')->exists($fotoPath)) {
                Storage::disk('public')->delete($fotoPath);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // === SHOW: UNTUK MELIHAT DETAIL PELATIH ===
    public function show($id)
    {
        $pelatih = Pelatih::findOrFail($id);
        return view('admin.pelatih.show', compact('pelatih'));
    }

    public function edit(Pelatih $pelatih)
    {
        return view('admin.pelatih.edit', compact('pelatih'));
    }

    // === UPDATE: UPDATE DATA + FOTO + LOGIKA HAPUS FOTO ===
    public function update(Request $request, Pelatih $pelatih)
    {
        $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'tanggal_lahir' => 'required|date|before:-17 years',
            'no_hp'         => 'required|numeric',
            'status'        => 'required',
            'lisensi'       => 'nullable|string',
            'alamat'        => 'nullable|string',
            'foto_profil'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'kategori_fokus'=> 'required|string', // ⚡ BARU
            'gender_fokus'  => 'required|string',   // ⚡ BARU
        ]);

        // Simpan path foto lama dulu sebagai default
        $fotoPath = $pelatih->foto_profil; 

        // 1. CEK JIKA USER MINTA HAPUS FOTO (Checkbox dicentang)
        if ($request->has('hapus_foto') && $request->hapus_foto == '1') {
            // Hapus file fisik foto lama
            if ($pelatih->foto_profil && Storage::disk('public')->exists($pelatih->foto_profil)) {
                Storage::disk('public')->delete($pelatih->foto_profil);
            }
            $fotoPath = null; // Set null agar di database jadi kosong
        }

        // 2. CEK JIKA ADA UPLOAD FOTO BARU
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama (jika ada dan belum dihapus di langkah 1)
            if ($pelatih->foto_profil && Storage::disk('public')->exists($pelatih->foto_profil)) {
                Storage::disk('public')->delete($pelatih->foto_profil);
            }
            // Simpan foto baru
            $fotoPath = $request->file('foto_profil')->store('foto-pelatih', 'public');
        }

        // Update Database Pelatih
        $pelatih->update([
            'nama_lengkap'  => $request->nama_lengkap,
            'tanggal_lahir' => $request->tanggal_lahir,
            'tempat_lahir'  => $request->tempat_lahir ?? $pelatih->tempat_lahir,
            'no_hp'         => $request->no_hp,
            'status'        => $request->status,
            'lisensi'       => $request->lisensi,
            'alamat'        => $request->alamat,
            'foto_profil'   => $fotoPath,
            'kategori_fokus'=> $request->kategori_fokus, // ⚡ BARU
            'gender_fokus'  => $request->gender_fokus,   // ⚡ BARU
        ]);

        // Update Nama di User Login juga agar sinkron
        if($pelatih->user) {
            $pelatih->user->update(['name' => $request->nama_lengkap]);
        }

        return redirect()->route('pelatih.index')->with('success', 'Data coach berhasil diperbarui.');
    }

    // === DESTROY: HAPUS PELATIH + HAPUS USER + HAPUS FOTO ===
    public function destroy(Pelatih $pelatih)
    {
        // 1. Hapus Foto Profil dari Storage
        if ($pelatih->foto_profil && Storage::disk('public')->exists($pelatih->foto_profil)) {
            Storage::disk('public')->delete($pelatih->foto_profil);
        }

        $user = $pelatih->user;

        // Hapus Data Pelatih
        $pelatih->delete();

        // Hapus Akun Login
        if ($user) {
            $user->delete();
        }
        
        return redirect()->route('pelatih.index')->with('success', 'Data coach dan akun login berhasil dihapus.');
    }

    // === CETAK PDF (PREVIEW/STREAM) ===
    public function cetakPdf($id)
    {
        $pelatih = Pelatih::findOrFail($id);
        
        // Load view khusus PDF
        $pdf = Pdf::loadView('admin.pelatih.pdf', compact('pelatih'));
        
        // Setup ukuran kertas & orientasi
        $pdf->setPaper('a4', 'portrait');

        // Stream = Preview di browser (Bukan download langsung)
        return $pdf->stream('Biodata_Coach_' . $pelatih->nama_lengkap . '.pdf');
    }
}