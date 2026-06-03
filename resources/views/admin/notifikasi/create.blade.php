<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengumuman Baru (Broadcast)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- MENAMPILKAN ERROR (JIKA ADA) --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Gagal Mengirim!</strong>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('notifikasi.store') }}" method="POST">
                        @csrf

                        {{-- 1. TARGET PENERIMA --}}
                        <div class="mb-6">
                            <label for="target_role" class="block text-sm font-bold text-gray-700 mb-2">Kirim Ke Siapa? <span class="text-red-500">*</span></label>
                            <select name="target_role" id="target_role" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">-- Pilih Penerima --</option>
                                {{-- Value "semua" sesuai dengan Controller baru --}}
                                <option value="semua">📢 Semua Pengguna (Atlet & Pelatih)</option>
                                <option value="atlet">🏃 Khusus Atlet</option>
                                <option value="pelatih">👟 Khusus Pelatih</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Pesan akan dikirim secara otomatis ke dashboard masing-masing pengguna.</p>
                        </div>

                        {{-- 2. JUDUL PENGUMUMAN --}}
                        <div class="mb-6">
                            <label for="judul" class="block text-sm font-bold text-gray-700 mb-2">Judul Pengumuman <span class="text-red-500">*</span></label>
                            <input type="text" name="judul" id="judul" placeholder="Contoh: Perubahan Jadwal Latihan"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        {{-- 3. ISI PESAN (GANTI 'isi' JADI 'pesan') --}}
                        <div class="mb-6">
                            <label for="pesan" class="block text-sm font-bold text-gray-700 mb-2">Isi Pesan <span class="text-red-500">*</span></label>
                            {{-- Perhatikan name="pesan" --}}
                            <textarea name="pesan" id="pesan" rows="6" placeholder="Tuliskan detail pengumuman di sini..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="flex justify-end gap-3 border-t pt-4">
                            <a href="{{ route('notifikasi.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-bold hover:bg-gray-300 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold hover:bg-blue-700 shadow-lg transition transform hover:scale-105">
                                🚀 Publikasikan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>