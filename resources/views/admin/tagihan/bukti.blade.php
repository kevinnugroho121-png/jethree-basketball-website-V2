<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Bukti Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            <a href="{{ route('tagihan.index') }}" class="inline-flex items-center mb-4 px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
                &larr; Kembali
            </a>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    {{-- Informasi Singkat --}}
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-bold text-gray-800">Detail Pembayaran</h3>
                        <div class="grid grid-cols-2 gap-4 mt-2 text-sm">
                            <div>
                                <p class="text-gray-500">Nama Atlet:</p>
                                <p class="font-semibold">{{ $tagihan->atlet->nama_lengkap }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Tagihan:</p>
                                <p class="font-semibold">{{ $tagihan->judul_tagihan }} (Rp {{ number_format($tagihan->nominal, 0, ',', '.') }})</p>
                            </div>
                        </div>
                    </div>

                    {{-- AREA GAMBAR BUKTI --}}
                    <div class="flex justify-center bg-gray-100 p-4 rounded-lg border border-gray-300">
                        {{-- GAMBAR UTAMA --}}
                        <img src="{{ asset('storage/' . $tagihan->bukti_pembayaran) }}" 
                             alt="Bukti Transfer" 
                             class="max-h-[500px] shadow-lg rounded object-contain border border-gray-200">
                    </div>

                    {{-- TOMBOL AKSI VERIFIKASI --}}
                    <div class="mt-6 flex justify-end gap-3">
                        {{-- Tombol Hapus Bukti (Jika tidak valid) --}}
                        {{-- Opsi tambahan: Bisa ditambahkan fitur tolak bukti disini nanti --}}
                        
                        {{-- Tombol Validasi --}}
                        @if($tagihan->status != 'Lunas')
                            <form action="{{ route('tagihan.verifikasi_lunas', $tagihan->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none transition ease-in-out duration-150" onclick="return confirm('Apakah bukti ini valid dan lunas?')">
                                    Verifikasi Lunas
                                </button>
                            </form>
                        @else
                            <span class="px-4 py-2 bg-green-100 text-green-800 rounded font-bold border border-green-200">
                                Sudah Lunas
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>