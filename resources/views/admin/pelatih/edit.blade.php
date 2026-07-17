<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Data Coach') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                
                {{-- Info Akun --}}
                <div class="mb-6 bg-gray-50 p-4 rounded border border-gray-200 flex items-center gap-3">
                    <div class="bg-yellow-100 p-2 rounded-full">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">Akun Login</h4>
                        <p class="text-sm text-gray-600">Email: <strong>{{ $pelatih->user->email ?? 'Tidak terhubung akun' }}</strong></p>
                        <p class="text-xs text-gray-500 mt-1">*Jika Coach lupa password, minta mereka reset sendiri atau admin reset manual di menu User.</p>
                    </div>
                </div>

                <form action="{{ route('pelatih.update', $pelatih->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-4 border-b pb-2">✏️ Edit Biodata</h3>
                        
                        {{-- Nama Lengkap --}}
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $pelatih->nama_lengkap) }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                        </div>

                        {{-- Lisensi --}}
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Lisensi Melatih</label>
                            <input type="text" name="lisensi" value="{{ old('lisensi', $pelatih->lisensi) }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" placeholder="Contoh: Lisensi C AFC">
                        </div>

                        {{-- TTL & No HP (SUDAH DIPERBAIKI) --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            {{-- Tempat Lahir --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $pelatih->tempat_lahir) }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Tanggal Lahir <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $pelatih->tanggal_lahir) }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                            </div>

                            {{-- No HP --}}
                            <div>
                                <label class="block font-medium text-sm text-gray-700">No. WhatsApp <span class="text-red-500">*</span></label>
                                <input type="number" name="no_hp" value="{{ old('no_hp', $pelatih->no_hp) }}" class="form-input rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Alamat Lengkap</label>
                            <textarea name="alamat" rows="2" class="form-textarea rounded-md shadow-sm mt-1 block w-full border-gray-300">{{ old('alamat', $pelatih->alamat) }}</textarea>
                        </div>

                        {{-- ⚡ BARU: DROPDOWN EDIT KATEGORI & GENDER FOKUS PELATIH --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Kategori Umur Latihan <span class="text-red-500">*</span></label>
                                <select name="kategori_fokus" class="form-select rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                                    <option value="KU-10" {{ old('kategori_fokus', $pelatih->kategori_fokus) == 'KU-10' ? 'selected' : '' }}>KU-10</option>
                                    <option value="KU-12" {{ old('kategori_fokus', $pelatih->kategori_fokus) == 'KU-12' ? 'selected' : '' }}>KU-12</option>
                                    <option value="KU-14" {{ old('kategori_fokus', $pelatih->kategori_fokus) == 'KU-14' ? 'selected' : '' }}>KU-14</option>
                                    <option value="KU-16" {{ old('kategori_fokus', $pelatih->kategori_fokus) == 'KU-16' ? 'selected' : '' }}>KU-16</option>
                                    <option value="Umum" {{ old('kategori_fokus', $pelatih->kategori_fokus) == 'Umum' ? 'selected' : '' }}>Umum / Semuanya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Gender Fokus Latihan <span class="text-red-500">*</span></label>
                                <select name="gender_fokus" class="form-select rounded-md shadow-sm mt-1 block w-full border-gray-300" required>
                                    <option value="Putra" {{ old('gender_fokus', $pelatih->gender_fokus) == 'Putra' ? 'selected' : '' }}>Putra</option>
                                    <option value="Putri" {{ old('gender_fokus', $pelatih->gender_fokus) == 'Putri' ? 'selected' : '' }}>Putri</option>
                                    <option value="Campuran" {{ old('gender_fokus', $pelatih->gender_fokus) == 'Campuran' ? 'selected' : '' }}>Campuran (Putra & Putri)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Foto & Status (DENGAN PREVIEW & HAPUS) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Foto Profil</label>
                                
                                <div class="mt-2 flex items-start gap-4">
                                    {{-- PREVIEW FOTO --}}
                                    <div class="relative w-24 h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-300 flex items-center justify-center shrink-0">
                                        @if($pelatih->foto_profil)
                                            <img id="preview-img" src="{{ asset('storage/' . $pelatih->foto_profil) }}" class="w-full h-full object-cover">
                                            <span id="preview-text" class="text-xs text-gray-400 text-center px-1 hidden">No Foto</span>
                                        @else
                                            <img id="preview-img" src="#" class="w-full h-full object-cover hidden">
                                            <span id="preview-text" class="text-xs text-gray-400 text-center px-1">No Foto</span>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <input type="file" name="foto_profil" id="foto_input" 
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                                            onchange="previewImage(this)">
                                        
                                        <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG, Max 2MB.</p>

                                        {{-- CHECKBOX HAPUS FOTO --}}
                                        @if($pelatih->foto_profil)
                                            <div class="mt-3">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="hapus_foto" value="1" id="hapus_foto_checkbox" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500" onchange="toggleHapusFoto()">
                                                    <span class="ml-2 text-sm text-red-600 font-bold hover:text-red-800">🗑️ Hapus Foto Profil Ini</span>
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-gray-700">Status <span class="text-red-500">*</span></label>
                                <select name="status" class="form-select rounded-md shadow-sm mt-1 block w-full border-gray-300">
                                    <option value="Aktif" {{ $pelatih->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="Non-Aktif" {{ $pelatih->status == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6 gap-3">
                        <a href="{{ route('pelatih.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded font-bold hover:bg-green-700 shadow-lg">
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        function previewImage(input) {
            var preview = document.getElementById('preview-img');
            var previewText = document.getElementById('preview-text');
            var hapusCheckbox = document.getElementById('hapus_foto_checkbox');
            
            // Kalau user pilih foto baru, uncheck hapus foto otomatis
            if(hapusCheckbox) hapusCheckbox.checked = false;

            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    previewText.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleHapusFoto() {
            var checkbox = document.getElementById('hapus_foto_checkbox');
            var preview = document.getElementById('preview-img');
            var previewText = document.getElementById('preview-text');
            var input = document.getElementById('foto_input');

            if (checkbox.checked) {
                // Jika dicentang Hapus, sembunyikan foto, reset input file
                input.value = ""; 
                preview.classList.add('hidden');
                previewText.classList.remove('hidden');
                previewText.innerText = "Akan Dihapus";
                previewText.classList.add('text-red-500', 'font-bold');
            } else {
                // Jika batal hapus (uncheck), kembalikan (ini refresh sederhana)
                // Idealnya simpan src lama di variabel, tapi reload halaman juga cukup jika user bingung.
                previewText.innerText = "No Foto";
                previewText.classList.remove('text-red-500', 'font-bold');
                // Note: Gambar asli baru balik kalau halaman direfresh, tapi fungsi hapus sudah batal.
            }
        }
    </script>
</x-app-layout>