<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterMateri;

class MasterMateriController extends Controller
{
    // 1. Tampilkan semua daftar materi
    public function index()
    {
        $materis = MasterMateri::orderBy('kategori', 'asc')->orderBy('pertemuan_ke', 'asc')->paginate(10);
        return view('admin.master_materi.index', compact('materis'));
    }

    // 2. Form tambah materi
    public function create()
    {
        return view('admin.master_materi.create');
    }

    // 3. Simpan materi ke database
    public function store(Request $request)
    {
        $request->validate([
            'kategori'     => 'required',
            'pertemuan_ke' => 'required|integer|min:1',
            'judul_materi' => 'required|string|max:255',
        ]);

        MasterMateri::create($request->all());

        return redirect()->route('master-materi.index')
            ->with('success', 'Silabus materi latihan berhasil ditambahkan!');
    }

    // 4. Form edit materi
    public function edit($id)
    {
        $materi = MasterMateri::findOrFail($id);
        return view('admin.master_materi.edit', compact('materi'));
    }

    // 5. Update materi
    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori'     => 'required',
            'pertemuan_ke' => 'required|integer|min:1',
            'judul_materi' => 'required|string|max:255',
        ]);

        $materi = MasterMateri::findOrFail($id);
        $materi->update($request->all());

        return redirect()->route('master-materi.index')
            ->with('success', 'Silabus materi latihan berhasil diperbarui!');
    }

    // 6. Hapus materi
    public function destroy($id)
    {
        $materi = MasterMateri::findOrFail($id);
        $materi->delete();

        return redirect()->route('master-materi.index')
            ->with('success', 'Data silabus berhasil dihapus.');
    }

    // --- FUNGSI KHUSUS UNTUK AJAX ---
    public function getByKategori(Request $request)
    {
        $kategori = $request->kategori;
        $materis = MasterMateri::where('kategori', $kategori)->orderBy('pertemuan_ke', 'asc')->get();
        
        return response()->json($materis);
    }

    // --- FUNGSI AJAX UNTUK CEK PERTEMUAN YANG SUDAH ADA ---
    public function getExistingPertemuan(Request $request)
    {
        $kategori = $request->kategori;
        $existingPertemuan = MasterMateri::where('kategori', $kategori)->pluck('pertemuan_ke')->toArray();
        
        return response()->json($existingPertemuan);
    }
}