<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Silabus Materi') }}
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
                    <h3 class="text-white font-semibold text-sm tracking-wide">Form Edit Silabus</h3>
                </div>

                {{-- Form Body --}}
                <div class="p-6">
                    <form action="{{ route('master-materi.update', $materi->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-4">
                            {{-- Kategori --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Umur</label>
                                <select name="kategori" required
                                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 transition">
                                    <option value="KU-10" {{ $materi->kategori == 'KU-10' ? 'selected' : '' }}>KU-10 (SD)</option>
                                    <option value="KU-12" {{ $materi->kategori == 'KU-12' ? 'selected' : '' }}>KU-12 (SMP Awal)</option>
                                    <option value="KU-14" {{ $materi->kategori == 'KU-14' ? 'selected' : '' }}>KU-14 (SMP)</option>
                                    <option value="KU-16" {{ $materi->kategori == 'KU-16' ? 'selected' : '' }}>KU-16 (SMA)</option>
                                    <option value="KU-18" {{ $materi->kategori == 'KU-18' ? 'selected' : '' }}>KU-18 (SMA Akhir)</option>
                                    <option value="Semua Umur" {{ $materi->kategori == 'Semua Umur' ? 'selected' : '' }}>Semua Umur</option>
                                </select>
                            </div>

                            {{-- Pertemuan --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pertemuan Ke-</label>
                                <input type="number" name="pertemuan_ke" value="{{ $materi->pertemuan_ke }}" required min="1"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 transition">
                            </div>
                        </div>

                        {{-- Judul Materi --}}
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Materi Latihan</label>
                            <input type="text" name="judul_materi" value="{{ $materi->judul_materi }}" required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-green-500 focus:ring-green-500 transition">
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end gap-3 border-t border-gray-100 pt-4">
                            <a href="{{ route('master-materi.index') }}"
                                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-semibold text-sm transition">
                                Batal
                            </a>
                            <button type="submit"
                                style="background-color: #EAB308; color: white;"
                                class="px-6 py-2 rounded-lg font-semibold text-sm transition shadow-md hover:opacity-90">
                                Update Silabus
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>