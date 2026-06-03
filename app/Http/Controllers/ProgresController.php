<?php

namespace App\Http\Controllers; // <-- Pastikan ini (tanpa Pelatih)

use App\Http\Controllers\Controller; // <-- Tambahkan ini
use Illuminate\Http\Request;
use App\Models\Atlet; // <-- Import ini
use App\Models\ProgresAtlet;
use Illuminate\Support\Facades\Auth;

class ProgresController extends Controller // <-- Pastikan extends Controller
{
    /**
     * Fungsi untuk menampilkan daftar atlet
     */
    public function index()
    {
        // Ambil semua atlet yang statusnya 'Aktif'
        $atlets = Atlet::where('status', 'Aktif')->get();

        // Tampilkan view 'progres.index' dan kirim data $atlets
        return view('pelatih.progres.index', compact('atlets'));
    }

    /**
     * Fungsi untuk menampilkan form input progres
     */
    public function create($atlet_id)
    {
        // 1. Cari atlet yang diklik berdasarkan ID
        $atlet = Atlet::findOrFail($atlet_id);
        
        // 2. Tampilkan view 'progres.create' dan kirim data atlet itu
        return view('pelatih.progres.create', compact('atlet'));
    }

    /**
     * Fungsi untuk menyimpan progres
     */
    public function store(Request $request)
    {
        // 1. Validasi data dari form
        $request->validate([
            'atlet_id' => 'required|exists:atlets,id',
            'tanggal_progres' => 'required|date',
            'kriteria' => 'required|string',
            'nilai' => 'required|integer|min:1|max:10',
            'catatan' => 'nullable|string',
        ]);
        
        // 2. Ambil ID pelatih yang sedang login
        $pelatih_id = Auth::id();

        // 3. Simpan data baru ke database
        ProgresAtlet::create([
            'atlet_id' => $request->atlet_id,
            'pelatih_id' => $pelatih_id,
            'tanggal_progres' => $request->tanggal_progres,
            'kriteria' => $request->kriteria,
            'nilai' => $request->nilai,
            'catatan' => $request->catatan,
        ]);

        // 4. Kembali ke halaman daftar atlet (progres index) dengan pesan sukses
        return redirect()->route('pelatih.progres.index')
        ->with('success', 'Progres atlet berhasil disimpan.');
    }
}