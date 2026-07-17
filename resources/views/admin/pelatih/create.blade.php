<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Coach Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                
                {{-- Tampilkan Error Validasi --}}
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Terjadi Kesalahan!</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pelatih.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    {{-- BAGIAN 1: AKUN LOGIN --}}
                    <div class="mb-6 bg-yellow-50 p-4 rounded border border-yellow-200">
                        <h3 class="font-bold text-lg text-yellow-800 mb-2">🔐 Buat Akun Login</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Email Login <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" placeholder="contoh: coach.budi@gmail.com" required>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Password <span class="text-red-500">*</span></label>
                                <input type="password" name="password" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" placeholder="Minimal 8 karakter" required>
                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN 2: BIODATA --}}
                    <div class="mb-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-2">👤 Biodata Lengkap</h3>
                        
                        {{-- Nama Lengkap --}}
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" placeholder="Nama lengkap beserta gelar (jika ada)" required>
                        </div>

                        {{-- Lisensi (YANG HILANG TADI) --}}
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Lisensi Melatih <span class="text-gray-400 text-xs">(Opsional)</span></label>
                            <input type="text" name="lisensi" value="{{ old('lisensi') }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" placeholder="Contoh: Lisensi C AFC, Lisensi B Nasional">
                        </div>

                        {{-- TTL & No HP (SUDAH DIPERBAIKI) --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            {{-- Tempat Lahir --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" placeholder="Contoh: Kediri" required>
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                                <p class="text-[10px] text-green-600 mt-1">*Min. 17 tahun.</p>
                            </div>

                            {{-- No HP --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">No. WhatsApp <span class="text-red-500">*</span></label>
                                <input type="number" name="no_hp" value="{{ old('no_hp') }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>

                        {{-- Alamat (YANG HILANG TADI) --}}
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Alamat Lengkap <span class="text-gray-400 text-xs">(Opsional)</span></label>
                            <textarea name="alamat" rows="2" class="form-textarea rounded-md shadow-sm mt-1 block w-full border-gray-300" placeholder="Masukkan alamat domisili saat ini...">{{ old('alamat') }}</textarea>
                        </div>

                        {{-- ⚡ BARU: DROPDOWN KATEGORI & GENDER FOKUS PELATIH --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Kategori Umur Latihan <span class="text-red-500">*</span></label>
                                <select name="kategori_fokus" class="form-select rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                                    <option value="" disabled selected>-- Pilih Kategori Umur --</option>
                                    <option value="KU-10" {{ old('kategori_fokus') == 'KU-10' ? 'selected' : '' }}>KU-10</option>
                                    <option value="KU-12" {{ old('kategori_fokus') == 'KU-12' ? 'selected' : '' }}>KU-12</option>
                                    <option value="KU-14" {{ old('kategori_fokus') == 'KU-14' ? 'selected' : '' }}>KU-14</option>
                                    <option value="KU-16" {{ old('kategori_fokus') == 'KU-16' ? 'selected' : '' }}>KU-16</option>
                                    <option value="Umum" {{ old('kategori_fokus') == 'Umum' ? 'selected' : '' }}>Umum / Semuanya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Gender Fokus Latihan <span class="text-red-500">*</span></label>
                                <select name="gender_fokus" class="form-select rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                                    <option value="" disabled selected>-- Pilih Fokus Gender --</option>
                                    <option value="Putra" {{ old('gender_fokus') == 'Putra' ? 'selected' : '' }}>Putra</option>
                                    <option value="Putri" {{ old('gender_fokus') == 'Putri' ? 'selected' : '' }}>Putri</option>
                                    <option value="Campuran" {{ old('gender_fokus') == 'Campuran' ? 'selected' : '' }}>Campuran (Putra & Putri)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Foto & Status (DENGAN PREVIEW) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Foto Profil <span class="text-gray-400 text-xs">(Opsional, Max 2MB)</span></label>
                                
                                <div class="mt-2 flex items-center gap-4">
                                    {{-- Area Preview --}}
                                    <div class="relative w-20 h-20 bg-gray-100 rounded-full overflow-hidden border border-gray-300 flex items-center justify-center shrink-0">
                                        <img id="preview-img" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                                        <span id="preview-text" class="text-xs text-gray-400 text-center px-1">Belum ada foto</span>
                                    </div>
                                    
                                    <div class="flex-1">
                                        <input type="file" name="foto_profil" id="foto_input" 
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                            onchange="previewImage(this)">
                                        
                                        <button type="button" id="btn-batal-foto" onclick="resetFoto()" class="mt-2 text-xs text-red-600 hover:text-red-800 font-medium hidden">
                                            x Batalkan Foto
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Status <span class="text-red-500">*</span></label>
                                <select name="status" class="form-select rounded-md shadow-sm mt-1 block w-full border-gray-300">
                                    <option value="Aktif" selected>Aktif</option>
                                    <option value="Non-Aktif">Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3">
                        <a href="{{ route('pelatih.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700 shadow-lg">
                            Simpan Data
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT PREVIEW FOTO --}}
    <script>
        function previewImage(input) {
            var preview = document.getElementById('preview-img');
            var previewText = document.getElementById('preview-text');
            var btnBatal = document.getElementById('btn-batal-foto');

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    previewText.classList.add('hidden');
                    btnBatal.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function resetFoto() {
            document.getElementById('foto_input').value = ""; 
            document.getElementById('preview-img').classList.add('hidden'); 
            document.getElementById('preview-text').classList.remove('hidden'); 
            document.getElementById('btn-batal-foto').classList.add('hidden'); 
        }
    </script>
</x-app-layout>