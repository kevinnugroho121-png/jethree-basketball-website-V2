<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Jadwal Latihan') }}
        </h2>
        {{-- CSS Flatpickr --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Menampilkan Error Validasi --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong class="font-bold">Gagal Menyimpan:</strong>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('jadwal.update', $jadwal->id) }}" method="POST">
                        @csrf
                        @method('PUT') 

                        {{-- 1. INPUT TANGGAL --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Latihan</label>
                            <input type="text" name="tanggal" value="{{ old('tanggal', $jadwal->tanggal) }}" 
                                class="datepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                        </div>

                        {{-- 2. KATEGORI & PELATIH --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Umur</label>
                                <select name="kategori" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach(['Semua Umur', 'KU-10', 'KU-12', 'KU-14', 'KU-16', 'KU-18'] as $kat)
                                        <option value="{{ $kat }}" {{ (old('kategori', $jadwal->kategori) == $kat) ? 'selected' : '' }}>
                                            {{ $kat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Pelatih --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pelatih Bertugas</label>
                                <select name="pelatih_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="">-- Pilih Coach --</option>
                                    @foreach($pelatihs as $pelatih)
                                        <option value="{{ $pelatih->id }}" {{ (old('pelatih_id', $jadwal->pelatih_id) == $pelatih->id) ? 'selected' : '' }}>
                                            Coach {{ $pelatih->nama_lengkap }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- 3. INPUT JAM --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">⏰ Jam Mulai</label>
                                <input type="text" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai) }}" 
                                    class="timepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">🏁 Jam Selesai</label>
                                <input type="text" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai) }}" 
                                    class="timepicker mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                        </div>

                        {{-- [BARU] 4. MATERI LATIHAN (Textarea) --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">📝 Materi Latihan</label>
                            <textarea name="materi" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                placeholder="Contoh: Fokus Defense, Latihan Dribble, Scrimmage 5v5..." required>{{ old('materi', $jadwal->materi) }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Ubah materi latihan jika diperlukan.</p>
                        </div>

                        {{-- 5. LOKASI & STATUS --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">📍 Lokasi Latihan</label>
                                <input type="text" name="lokasi" 
                                    value="{{ old('lokasi', $jadwal->lokasi) }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status Jadwal</label>
                                <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="Aktif" {{ (old('status', $jadwal->status) == 'Aktif') ? 'selected' : '' }}>Aktif</option>
                                    <option value="Dibatalkan" {{ (old('status', $jadwal->status) == 'Dibatalkan') ? 'selected' : '' }}>Dibatalkan</option>
                                </select>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end gap-3 mt-8 border-t pt-4">
                            <a href="{{ route('jadwal.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-gray-300 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-semibold text-xs uppercase tracking-widest hover:bg-blue-500 transition shadow-lg">
                                Perbarui Jadwal
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT FLATPICKR --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d", 
            altInput: true,      
            altFormat: "l, d F Y",
            allowInput: true
        });

        flatpickr(".timepicker", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i", 
            time_24hr: true,
        });
    </script>
</x-app-layout>