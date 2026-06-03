<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Pembayaran (Kasir)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- ALERT STATUS --}}
                    @if($tagihan->status == 'Lunas')
                        <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                            <p class="font-bold">✅ TAGIHAN LUNAS</p>
                            <p class="text-sm">Pembayaran telah diverifikasi. Bukti pembayaran tersimpan aman.</p>
                        </div>
                    @endif

                    {{-- ERROR HANDLING --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong class="font-bold">Gagal Menyimpan!</strong>
                            <ul class="mt-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- FORM UTAMA --}}
                    {{-- PENTING: enctype="multipart/form-data" WAJIB ADA untuk upload foto --}}
                    <form action="{{ route('tagihan.update', $tagihan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- BAGIAN 1: INFO TAGIHAN (READONLY / TIDAK BISA DIEDIT) --}}
                            {{-- Admin kasir tidak boleh merubah nominal/bulan sembarangan --}}
                            <div class="col-span-2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-bold text-gray-800 mb-2">Rincian Tagihan</h3>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Nama Atlet</p>
                                        <p class="font-bold text-gray-800">{{ $tagihan->atlet->nama_lengkap }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Kategori</p>
                                        <p class="font-bold text-gray-800">{{ $tagihan->atlet->kategori }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Jenis Tagihan</p>
                                        <p class="font-bold text-blue-700">{{ $tagihan->judul_tagihan }}</p> {{-- Otomatis dari Model --}}
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Total Tagihan</p>
                                        <p class="font-bold text-xl text-gray-900">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>

                            <hr class="col-span-2 my-2">

                            {{-- BAGIAN 2: PROSES PEMBAYARAN --}}
                            
                            {{-- 1. STATUS PEMBAYARAN --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Status Pembayaran <span class="text-red-500">*</span></label>
                                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-bold" 
                                    {{ $tagihan->status == 'Lunas' ? 'disabled' : '' }}> {{-- Jika Lunas, disable edit --}}
                                    
                                    <option value="Belum Lunas" {{ (old('status', $tagihan->status) == 'Belum Lunas') ? 'selected' : '' }}>❌ Belum Lunas</option>
                                    <option value="Lunas" {{ (old('status', $tagihan->status) == 'Lunas') ? 'selected' : '' }}>✅ Lunas (Bayar Sekarang)</option>
                                </select>
                                
                                {{-- Trik agar value tetap terkirim meski disabled --}}
                                @if($tagihan->status == 'Lunas')
                                    <input type="hidden" name="status" value="Lunas">
                                @endif
                            </div>

                            {{-- 2. METODE PEMBAYARAN --}}
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Metode Pembayaran</label>
                                <select name="metode_pembayaran" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    {{ $tagihan->status == 'Lunas' ? 'disabled' : '' }}>
                                    
                                    <option value="">-- Pilih Metode --</option>
                                    <option value="Tunai (Cash)" {{ (old('metode_pembayaran', $tagihan->metode_pembayaran) == 'Tunai (Cash)') ? 'selected' : '' }}>💵 Tunai (Cash)</option>
                                    <option value="Transfer Bank" {{ (old('metode_pembayaran', $tagihan->metode_pembayaran) == 'Transfer Bank') ? 'selected' : '' }}>🏦 Transfer Bank</option>
                                    <option value="Transfer (Via Mobile)" {{ (old('metode_pembayaran', $tagihan->metode_pembayaran) == 'Transfer (Via Mobile)') ? 'selected' : '' }}>📱 Transfer (Via Mobile)</option>
                                </select>
                            </div>

                            {{-- 3. BUKTI PEMBAYARAN (UPLOAD FOTO) --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-gray-700 mb-1">
                                    Bukti Pembayaran (Wajib)
                                    <span class="text-xs font-normal text-gray-500">- Upload foto uang cash atau struk transfer</span>
                                </label>

                                {{-- Jika sudah ada foto, tampilkan --}}
                                @if($tagihan->bukti_pembayaran)
                                    <div class="mb-3 p-2 border rounded bg-gray-50 inline-block">
                                        <p class="text-xs text-gray-500 mb-1">Bukti Tersimpan:</p>
                                        {{-- Pastikan nanti jalankan: php artisan storage:link --}}
                                        <img src="{{ asset('storage/' . $tagihan->bukti_pembayaran) }}" alt="Bukti Bayar" class="h-40 rounded shadow-sm object-cover bg-white border">
                                    </div>
                                @endif

                                {{-- Input File (Disembunyikan jika sudah Lunas agar tidak diganti sembarangan) --}}
                                @if($tagihan->status != 'Lunas')
                                    <input type="file" name="bukti_pembayaran" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100 border p-2 rounded-md
                                    "/>
                                    <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah bukti yang sudah ada.</p>
                                @else
                                    <p class="text-sm text-green-600 italic">🔒 Bukti pembayaran terkunci karena status sudah Lunas.</p>
                                @endif
                            </div>

                            {{-- 4. CATATAN --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Tambahan</label>
                                <textarea name="keterangan" rows="2" class="w-full rounded-md border-gray-300 shadow-sm" {{ $tagihan->status == 'Lunas' ? 'disabled' : '' }}>{{ old('keterangan', $tagihan->keterangan) }}</textarea>
                            </div>

                        </div>

                        {{-- TOMBOL AKSI --}}
                        <div class="flex justify-end mt-8 gap-3 border-t pt-4">
                            <a href="{{ route('tagihan.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md font-bold hover:bg-gray-300 transition">
                                Kembali
                            </a>

                            {{-- Tombol Simpan HANYA muncul jika BELUM LUNAS (Agar tidak diutak-atik lagi) --}}
                            @if($tagihan->status != 'Lunas')
                                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md font-bold hover:bg-green-700 shadow-lg transition transform hover:scale-105">
                                    Verifikasi & Simpan Lunas
                                </button>
                            @endif
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>