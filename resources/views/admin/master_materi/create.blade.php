<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Silabus Materi Baru') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="w-full px-4">

            {{-- Error Validasi --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded shadow-sm">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-md rounded-xl overflow-hidden">

                {{-- Header Card --}}
                <div class="bg-green-600 px-6 py-3">
                    <h3 class="text-white font-semibold text-sm tracking-wide">Form Tambah Silabus</h3>
                </div>

                {{-- Form Body --}}
                <div class="p-6">
                    <form action="{{ route('master-materi.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-1">
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Umur</label>
                                <select name="kategori" id="kategori_select" required
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 transition">
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="KU-10">KU-10 (SD)</option>
                                    <option value="KU-12">KU-12 (SMP Awal)</option>
                                    <option value="KU-14">KU-14 (SMP)</option>
                                    <option value="KU-16">KU-16 (SMA)</option>
                                    <option value="KU-18">KU-18 (SMA Akhir)</option>
                                    
                                </select>
                            </div>

                            {{-- Pertemuan --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pertemuan Ke-</label>
                                <select name="pertemuan_ke" id="pertemuan_select" required disabled
                                    class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2 text-sm shadow-sm cursor-not-allowed transition">
                                    <option value="">-- Pilih Kategori Dulu --</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Hanya pertemuan yang kosong yang bisa dipilih.</p>
                            </div>
                        </div>

                        {{-- Judul Materi --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Materi Latihan</label>
                            <input type="text" name="judul_materi" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 transition"
                                placeholder="Contoh: Ball Handling Dasar & Dribbling">
                            <p class="text-xs text-gray-400 mt-1">Materi ini akan muncul otomatis saat admin membuat jadwal.</p>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                            <a href="{{ route('master-materi.index') }}"
                                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-semibold text-sm transition">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-sm transition shadow-md">
                                Simpan Silabus
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- AJAX: Fungsi cek pertemuan yang sudah ada (TIDAK DIUBAH) --}}
    <script>
        document.getElementById('kategori_select').addEventListener('change', function() {
            let kategori = this.value;
            let pertemuanSelect = document.getElementById('pertemuan_select');

            if (kategori) {
                pertemuanSelect.innerHTML = '<option value="">⏳ Mengecek data...</option>';
                pertemuanSelect.disabled = true;

                fetch(`{{ url('admin/master-materi/get-existing-pertemuan') }}?kategori=${kategori}`)
                    .then(response => response.json())
                    .then(existing => {
                        pertemuanSelect.innerHTML = '<option value="">-- Pilih Urutan Pertemuan --</option>';
                        
                        for(let i = 1; i <= 24; i++) {
                            if(existing.includes(i)) {
                                pertemuanSelect.innerHTML += `<option value="${i}" disabled class="bg-gray-200 text-red-500 font-bold">❌ Pertemuan ${i} (Sudah Terisi)</option>`;
                            } else {
                                pertemuanSelect.innerHTML += `<option value="${i}" class="text-green-600 font-bold">✅ Pertemuan ${i}</option>`;
                            }
                        }
                        
                        pertemuanSelect.disabled = false;
                        pertemuanSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        pertemuanSelect.innerHTML = '<option value="">⚠️ Gagal memuat data</option>';
                    });
            } else {
                pertemuanSelect.innerHTML = '<option value="">-- Pilih Kategori Dulu --</option>';
                pertemuanSelect.disabled = true;
                pertemuanSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
            }
        });
    </script>
</x-app-layout>