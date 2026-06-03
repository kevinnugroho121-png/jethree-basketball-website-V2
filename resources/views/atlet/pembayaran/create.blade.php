<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Konfirmasi Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">
                    
                    {{-- 1. HEADER --}}
                    <div class="text-center mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                            💸
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Rincian Pembayaran</h3>
                        <p class="text-sm text-gray-500">Silakan cek kembali tagihan yang akan dibayar.</p>
                    </div>

                    {{-- 2. LIST TAGIHAN YANG AKAN DIBAYAR --}}
                    <div class="bg-gray-50 rounded-xl p-5 mb-6 border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Item Tagihan</h4>
                        <ul class="space-y-3">
                            @foreach($selected_tagihan as $item)
                                <li class="flex justify-between items-center text-sm border-b border-gray-200 last:border-0 pb-2 last:pb-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                        <span class="text-gray-700 font-medium">
                                            SPP Bulan {{ \Carbon\Carbon::create()->month($item->bulan)->translatedFormat('F') }} {{ $item->tahun }}
                                        </span>
                                    </div>
                                    <span class="font-bold text-gray-800">Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                                </li>
                            @endforeach
                        </ul>
                        
                        {{-- TOTAL BAYAR --}}
                        <div class="border-t-2 border-dashed border-gray-200 mt-4 pt-4 flex justify-between items-center">
                            <span class="font-bold text-gray-600">TOTAL TRANSFER</span>
                            <span class="font-extrabold text-2xl text-green-600">Rp {{ number_format($total_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- 3. FORM UPLOAD --}}
                    <form action="{{ route('atlet.pembayaran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- PENTING: Hidden Input untuk kirim SEMUA ID tagihan (Array) --}}
                        @foreach($selected_tagihan as $item)
                            <input type="hidden" name="tagihan_ids[]" value="{{ $item->id }}">
                        @endforeach

                        {{-- Input Gambar --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Upload Bukti Transfer</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:bg-gray-50 transition relative">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                            <span>Upload file gambar</span>
                                            <input id="file-upload" name="bukti_pembayaran" type="file" class="sr-only" required onchange="previewImage(event)">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 2MB</p>
                                    <p id="file-name" class="text-sm text-gray-800 font-bold mt-2 hidden"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex gap-3">
                            <a href="{{ route('atlet.tagihan.index') }}" class="flex-1 py-3 text-center border border-gray-300 rounded-lg text-gray-600 font-bold hover:bg-gray-100 transition">
                                Batal
                            </a>
                            <button type="submit" class="flex-1 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Kirim Bukti Bayar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk menampilkan nama file yang dipilih --}}
    <script>
        function previewImage(event) {
            const input = event.target;
            const fileName = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                fileName.textContent = 'File terpilih: ' + input.files[0].name;
                fileName.classList.remove('hidden');
            }
        }
    </script>
</x-app-layout>