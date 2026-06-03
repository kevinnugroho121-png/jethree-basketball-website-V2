<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Tagihan SPP Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- MENAMPILKAN ERROR (Misal: Tagihan Duplikat) --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Gagal Menyimpan!</strong>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('tagihan.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- 1. PILIH ATLET --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Pilih Atlet <span class="text-red-500">*</span></label>
                                <select name="atlet_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    <option value="">-- Cari Nama Atlet --</option>
                                    @foreach($atlets as $atlet)
                                        <option value="{{ $atlet->id }}">
                                            {{ $atlet->nama_lengkap }} ({{ $atlet->kategori }})
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">*Hanya atlet status Aktif yang muncul.</p>
                            </div>

                            {{-- 2. PILIH BULAN (DROPDOWN OTOMATIS) --}}
                            {{-- Ini menggantikan input text manual agar data konsisten --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Untuk Bulan <span class="text-red-500">*</span></label>
                                <select name="bulan" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    {{-- Loop angka 1 sampai 12 untuk membuat pilihan bulan --}}
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }} 
                                            {{-- Output: Januari, Februari, dst --}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. PILIH TAHUN --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                                <select name="tahun" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    {{-- Pilihan Tahun Ini dan Tahun Depan --}}
                                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                                    <option value="{{ date('Y') + 1 }}">{{ date('Y') + 1 }}</option>
                                </select>
                            </div>

                            {{-- 4. NOMINAL (Rp) --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nominal SPP (Rp) <span class="text-red-500">*</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                                    </div>
                                    {{-- Default diisi 100000 agar admin tidak capek ngetik --}}
                                    <input type="number" name="nominal" value="100000" min="0"
                                        class="block w-full rounded-md border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 font-bold text-lg" required>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Default: Rp 100.000 (Dapat diubah sesuai kebutuhan).</p>
                            </div>

                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="flex justify-end mt-8 gap-3 border-t pt-4">
                            <a href="{{ route('tagihan.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-bold hover:bg-gray-300 transition">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md font-bold hover:bg-blue-700 shadow-lg transition transform hover:scale-105">
                                Simpan Tagihan
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>