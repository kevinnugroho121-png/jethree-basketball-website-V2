<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Bukti Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Tombol Kembali --}}
            {{-- 💡 Perbaikan: Diubah agar tujuannya mundur ke riwayat data tagihan atlet yang bersangkutan --}}
            <a href="{{ route('tagihan.show', $tagihan->atlet_id) }}" class="inline-flex items-center mb-4 px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
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

                    {{-- TOMBOL AKSI VERIFIKASI (SINKRON DENGAN PENOLAKAN MOBILE) --}}
                    <div class="mt-6">
                        @if($tagihan->status != 'Lunas')
                            {{-- Kotak Input Alasan Penolakan Berwarna Merah Khas Sistem J3 --}}
                            <div class="p-4 border border-red-200 rounded-lg bg-red-50 mb-4">
                                {{-- Form Penolakan terpisah, menembak route update bawaan --}}
                                <form action="{{ route('tagihan.update', $tagihan->id) }}" method="POST" id="form-tolak-pembayaran">
                                    @csrf
                                    @method('PUT')
                                    {{-- Mengirimkan instruksi balik status ke Belum Lunas --}}
                                    <input type="hidden" name="status" value="Belum Lunas">
                                    
                                    <label class="block text-sm font-bold text-red-700 mb-1">Alasan Penolakan / Catatan Nominal Kurang ❌</label>
                                    <textarea name="catatan_penolakan" rows="2" placeholder="Contoh: Nominal transfer kurang Rp 50.000 atau berkas struk buram/tidak valid." required class="w-full rounded-md border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm mb-3"></textarea>
                                </form>

                                <div class="flex justify-end gap-3">
                                    {{-- Tombol Tolak (Terhubung ke form-tolak-pembayaran menggunakan atribut form) --}}
                                    <button type="submit" form="form-tolak-pembayaran" onclick="return confirm('Yakin ingin menolak bukti transfer ini? Berkas struk lama otomatis dihapus agar atlet bisa upload struk ulang.')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition ease-in-out duration-150">
                                        Tolak Pembayaran
                                    </button>
                                    
                                    {{-- Tombol Verifikasi Lunas Bawaan Kamu Tetap Berdiri Gagah Disini --}}
                                    <form action="{{ route('tagihan.verifikasi_lunas', $tagihan->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none transition ease-in-out duration-150" onclick="return confirm('Apakah bukti ini valid dan lunas?')">
                                            Verifikasi Lunas
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            {{-- Tampilan jika data tagihan memang sudah diselesaikan/lunas --}}
                            <div class="flex justify-end">
                                <span class="px-4 py-2 bg-green-100 text-green-800 rounded font-bold border border-green-200">
                                    Sudah Lunas
                                </span>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>