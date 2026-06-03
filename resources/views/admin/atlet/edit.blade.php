<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Atlet') }} : {{ $atlet->nama_lengkap }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Tampilkan Error Validasi --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong class="font-bold">Gagal Menyimpan!</strong>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORM EDIT (WAJIB ADA ENCTYPE UNTUK FOTO) --}}
                    <form action="{{ route('atlet.update', $atlet->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            
                            {{-- KOLOM KIRI: FOTO PROFIL (Lebar 1 Kolom) --}}
                            <div class="md:col-span-1 flex flex-col items-center">
                                <label class="block text-lg font-bold text-gray-700 mb-4 border-b pb-2 w-full text-center">Foto Profil</label>
                                
                                {{-- Frame Preview Foto --}}
                                <div class="w-48 h-64 bg-gray-100 border-2 border-dashed border-gray-400 rounded-lg flex items-center justify-center overflow-hidden relative mb-4 shadow-sm">
                                    
                                    {{-- Logika Tampilan Foto --}}
                                    <img id="preview-foto" 
                                         src="{{ $atlet->foto_profil ? asset('storage/' . $atlet->foto_profil) : '#' }}" 
                                         alt="Preview Foto" 
                                         class="absolute inset-0 w-full h-full object-cover {{ $atlet->foto_profil ? '' : 'hidden' }}">
                                    
                                    {{-- Placeholder Teks --}}
                                    <div id="placeholder-foto" class="text-center p-4 {{ $atlet->foto_profil ? 'hidden' : '' }}">
                                        <svg class="mx-auto h-10 w-10 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <p class="mt-1 text-xs text-gray-500">Belum ada foto</p>
                                    </div>
                                </div>

                                {{-- Input File --}}
                                <input type="file" name="foto_profil" accept="image/*" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                       onchange="previewImage(event)">
                                <p class="text-xs text-gray-500 mt-2 text-center italic">*Upload foto baru untuk mengganti.</p>
                            </div>

                            {{-- KOLOM KANAN: FORM BIODATA (Lebar 2 Kolom) --}}
                            <div class="md:col-span-2 space-y-6">

                                {{-- BAGIAN A: DATA DIRI --}}
                                <div>
                                    <h3 class="font-bold text-lg text-blue-600 mb-4 border-b pb-2">A. Data Diri Atlet (Mode Edit)</h3>
                                    
                                    {{-- === TAMBAHAN BARU: EMAIL LOGIN (READ ONLY) === --}}
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700">Email Login (Tidak bisa diubah)</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-200 text-gray-500 text-sm">
                                                📧
                                            </span>
                                            {{-- Ambil email dari relasi User --}}
                                            <input type="text" value="{{ $atlet->user->email ?? 'Akun belum terhubung' }}" disabled
                                                class="focus:ring-indigo-500 focus:border-indigo-500 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 bg-gray-100 text-gray-500 cursor-not-allowed">
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-1 italic">
                                            * Jika ingin mengganti email/password, silakan hapus data ini dan buat baru, atau minta Atlet ubah sendiri di menu profil mereka.
                                        </p>
                                    </div>
                                    {{-- ================================================= --}}

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $atlet->nama_lengkap) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="nama_panggilan" class="block text-sm font-medium text-gray-700">Nama Panggilan</label>
                                            <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan', $atlet->nama_panggilan) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="tempat_lahir" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $atlet->tempat_lahir) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                        
                                        {{-- PENGAMANAN 1: TANGGAL LAHIR TERKUNCI --}}
                                        <div class="mb-4">
                                            <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700">Tanggal Lahir 🔒</label>
                                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $atlet->tanggal_lahir) }}" 
                                                   class="mt-1 block w-full rounded-md border-gray-200 bg-gray-200 text-gray-500 cursor-not-allowed shadow-sm sm:text-sm" 
                                                   readonly title="Tanggal lahir tidak dapat diubah sembarangan">
                                            <p class="text-[11px] text-red-500 mt-1 italic">
                                                * Hubungi Admin Database jika ada kesalahan input tanggal lahir fatal.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="jenis_kelamin" class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                                            <select name="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="Laki-laki" {{ $atlet->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                <option value="Perempuan" {{ $atlet->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="no_hp_atlet" class="block text-sm font-medium text-gray-700">No. HP Atlet (WA)</label>
                                            <input type="text" name="no_hp_atlet" value="{{ old('no_hp_atlet', $atlet->no_hp_atlet) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                                        <textarea name="alamat" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('alamat', $atlet->alamat) }}</textarea>
                                    </div>
                                </div>

                                {{-- BAGIAN B: DATA SEKOLAH --}}
                                <div>
                                    <h3 class="font-bold text-lg text-blue-600 mb-4 border-b pb-2">B. Data Sekolah</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="jenjang_sekolah" class="block text-sm font-medium text-gray-700">Jenjang Sekolah</label>
                                            <select name="jenjang_sekolah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                                <option value="SD" {{ (old('jenjang_sekolah', $atlet->jenjang_sekolah) == 'SD') ? 'selected' : '' }}>SD / MI</option>
                                                <option value="SMP" {{ (old('jenjang_sekolah', $atlet->jenjang_sekolah) == 'SMP') ? 'selected' : '' }}>SMP / MTs</option>
                                                <option value="SMA" {{ (old('jenjang_sekolah', $atlet->jenjang_sekolah) == 'SMA') ? 'selected' : '' }}>SMA / SMK / MA</option>
                                                <option value="Kuliah" {{ (old('jenjang_sekolah', $atlet->jenjang_sekolah) == 'Kuliah') ? 'selected' : '' }}>Kuliah</option>
                                            </select>
                                        </div>
                                        <div class="mb-4">
                                            <label for="nama_sekolah" class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                                            <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $atlet->nama_sekolah) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- BAGIAN C: DATA AKADEMI --}}
                                <div>
                                    <h3 class="font-bold text-lg text-blue-600 mb-4 border-b pb-2">C. Data Akademi (Basket)</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        
                                        {{-- PENGAMANAN 2: KATEGORI TERKUNCI --}}
                                        <div class="mb-4">
                                            <label for="kategori_show" class="block text-sm font-medium text-gray-700">Kategori (Terkunci) 🔒</label>
                                            <input type="text" id="kategori_show" value="{{ $atlet->kategori }}" 
                                                   class="mt-1 block w-full rounded-md border-gray-200 bg-gray-200 text-gray-500 cursor-not-allowed shadow-sm sm:text-sm" readonly>
                                            {{-- Kirim nilai kategori asli (hidden) agar validasi controller tetap lulus --}}
                                            <input type="hidden" name="kategori" value="{{ $atlet->kategori }}">
                                        </div>

                                        <div class="mb-4">
                                            <label for="posisi" class="block text-sm font-medium text-gray-700">Posisi Bermain</label>
                                            <select name="posisi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                <option value="Belum Ditentukan">-- Pilih Posisi --</option>
                                                <option value="Point Guard" {{ $atlet->posisi == 'Point Guard' ? 'selected' : '' }}>Point Guard (PG)</option>
                                                <option value="Shooting Guard" {{ $atlet->posisi == 'Shooting Guard' ? 'selected' : '' }}>Shooting Guard (SG)</option>
                                                <option value="Small Forward" {{ $atlet->posisi == 'Small Forward' ? 'selected' : '' }}>Small Forward (SF)</option>
                                                <option value="Power Forward" {{ $atlet->posisi == 'Power Forward' ? 'selected' : '' }}>Power Forward (PF)</option>
                                                <option value="Center" {{ $atlet->posisi == 'Center' ? 'selected' : '' }}>Center (C)</option>
                                            </select>
                                        </div>

                                        <div class="mb-4">
                                            <label for="status" class="block text-sm font-medium text-gray-700">Status Keanggotaan</label>
                                            <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                                <option value="Aktif" {{ $atlet->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                                <option value="Non-Aktif" {{ $atlet->status == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                                <option value="Keluar" {{ $atlet->status == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- BAGIAN D: DATA ORANG TUA --}}
                                <div>
                                    <h3 class="font-bold text-lg text-blue-600 mb-4 border-b pb-2">D. Data Orang Tua / Wali</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="mb-4">
                                            <label for="nama_orang_tua" class="block text-sm font-medium text-gray-700">Nama Orang Tua</label>
                                            <input type="text" name="nama_orang_tua" value="{{ old('nama_orang_tua', $atlet->nama_orang_tua) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                        <div class="mb-4">
                                            <label for="no_hp_orang_tua" class="block text-sm font-medium text-gray-700">No. HP Orang Tua (WA)</label>
                                            <input type="text" name="no_hp_orang_tua" value="{{ old('no_hp_orang_tua', $atlet->no_hp_orang_tua) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        </div>
                                    </div>
                                </div>

                                {{-- TOMBOL AKSI --}}
                                <div class="flex justify-end mt-6">
                                    <a href="{{ route('atlet.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 mr-2">
                                        Batal
                                    </a>
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                        Update Data Atlet
                                    </button>
                                </div>

                            </div> {{-- End Kolom Kanan --}}
                        </div> {{-- End Grid --}}
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT: PREVIEW FOTO EDIT (VERSI AMAN) --}}
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e){
                    const output = document.getElementById('preview-foto');
                    const placeholder = document.getElementById('placeholder-foto');
                    
                    output.src = e.target.result;
                    output.classList.remove('hidden'); // Munculkan gambar baru
                    placeholder.classList.add('hidden'); // Sembunyikan placeholder
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout> 