<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Data Coach') }}
        </h2>
    </x-slot>

    <div class="py-8 px-4 max-w-5xl mx-auto">
        
        {{-- ========================================== --}}
        {{-- BAGIAN 1: TOMBOL KEMBALI (Global) --}}
        {{-- ========================================== --}}
        <a href="{{ route('pelatih.index') }}" class="inline-flex items-center mb-6 text-gray-600 hover:text-blue-600 font-medium transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Daftar Coach
        </a>

        {{-- ========================================== --}}
        {{-- BAGIAN 2: CONTAINER PREVIEW PDF (DIPERBESAR & ADA DOWNLOAD) --}}
        {{-- ========================================== --}}
        <div id="pdf-container" class="hidden bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
            {{-- Header Preview dengan Tombol Download & Tutup --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex flex-wrap justify-between items-center gap-3">
                <h3 class="text-lg font-bold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Preview Dokumen Biodata
                </h3>
                
                <div class="flex items-center gap-2">
                    {{-- TOMBOL DOWNLOAD MANUAL --}}
                    <a id="btn-download-pdf" href="" target="_blank" class="text-sm bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-bold transition flex items-center shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download PDF
                    </a>

                    <button type="button" onclick="tutupPdf()" class="text-sm bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 font-bold transition border border-gray-300">
                        x Tutup
                    </button>
                </div>
            </div>
            
            {{-- Area Iframe (Tinggi diperpanjang jadi 1000px) --}}
            <div class="w-full bg-gray-100">
                <iframe id="pdf-frame" src="" style="width: 100%; height: 1200px; border: none;" frameborder="0"></iframe>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- BAGIAN 3: CONTAINER BIODATA UTAMA (DEFAULT) --}}
        {{-- ========================================== --}}
        <div id="biodata-container" class="grid grid-cols-1 md:grid-cols-3 gap-6 transition-all duration-300">
            
            {{-- KARTU KIRI: FOTO & RINGKASAN --}}
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden h-fit">
                <div class="bg-gradient-to-br from-green-500 to-green-600 h-32 relative"></div>
                
                <div class="px-6 pb-6 text-center -mt-16">
                    <div class="relative inline-block">
                        @if($pelatih->foto_profil)
                            <img src="{{ asset('storage/' . $pelatih->foto_profil) }}" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg mx-auto bg-white" alt="Foto">
                        @else
                            <div class="w-32 h-32 rounded-full border-4 border-white shadow-lg mx-auto bg-gray-200 flex items-center justify-center text-gray-500 text-2xl font-bold">
                                {{ substr($pelatih->nama_lengkap, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-gray-800">{{ $pelatih->nama_lengkap }}</h2>
                    <p class="text-sm text-gray-500">Coach</p>

                    <div class="mt-4 flex justify-center">
                        @if($pelatih->status == 'Aktif')
                            <span class="px-4 py-1 bg-green-100 text-green-700 text-sm font-bold rounded-full border border-green-200">AKTIF</span>
                        @else
                            <span class="px-4 py-1 bg-red-100 text-red-700 text-sm font-bold rounded-full border border-red-200">NON-AKTIF</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KARTU KANAN: DETAIL BIODATA --}}
            <div class="md:col-span-2 bg-white rounded-lg shadow-md border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Biodata Lengkap
                    </h3>
                    
                    <div class="flex gap-2">
                        <button type="button" 
        data-url="{{ route('pelatih.cetakPdf', $pelatih->id) }}"
        onclick="bukaPdf(this.dataset.url)" 
        class="text-sm bg-red-100 text-red-700 px-3 py-1 rounded hover:bg-red-200 border border-red-200 font-medium transition flex items-center shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Preview PDF
                        </button>

                        <a href="{{ route('pelatih.edit', $pelatih->id) }}" class="text-sm bg-yellow-100 text-yellow-700 px-3 py-1 rounded hover:bg-yellow-200 border border-yellow-200 font-medium transition flex items-center shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Edit Data
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 gap-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-3 border-b border-gray-100 pb-4">
                            <span class="text-gray-500 text-sm font-medium">Nama Lengkap</span>
                            <span class="sm:col-span-2 text-gray-900 font-semibold">{{ $pelatih->nama_lengkap }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 border-b border-gray-100 pb-4">
                            <span class="text-gray-500 text-sm font-medium">Email Login</span>
                            <span class="sm:col-span-2 text-gray-900 font-mono text-sm bg-gray-50 px-2 py-1 rounded w-fit">
                                {{ $pelatih->user->email ?? '-' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 border-b border-gray-100 pb-4">
                            <span class="text-gray-500 text-sm font-medium">Nomor WhatsApp</span>
                            <span class="sm:col-span-2 text-gray-900 flex items-center">
                                {{ $pelatih->no_hp }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 border-b border-gray-100 pb-4">
                            <span class="text-gray-500 text-sm font-medium">TTL (Tempat, Tgl Lahir)</span>
                            <span class="sm:col-span-2 text-gray-900">
                                {{ $pelatih->tempat_lahir ?? '-' }}, {{ \Carbon\Carbon::parse($pelatih->tanggal_lahir)->translatedFormat('d F Y') }}
                                <span class="text-xs text-gray-500 ml-1">
                                    ({{ \Carbon\Carbon::parse($pelatih->tanggal_lahir)->age }} Tahun)
                                </span>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 border-b border-gray-100 pb-4">
                            <span class="text-gray-500 text-sm font-medium">Lisensi Melatih</span>
                            <span class="sm:col-span-2">
                                @if($pelatih->lisensi)
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-semibold">{{ $pelatih->lisensi }}</span>
                                @else
                                    <span class="text-gray-400 italic">Belum ada lisensi</span>
                                @endif
                            </span>
                        </div>

                        {{-- ⚡ BARU: DATA FOKUS KATEGORI & GENDER PADA DETAIL BIODATA (SINKRON 100%) --}}
                        @if($pelatih->kategori_fokus || $pelatih->gender_fokus)
                            @php
                                // Mengunci keselarasan 5 warna utama aman terkompilasi agar sama dengan list utama
                                $warnaDetailCoach = match (true) {
                                    $pelatih->kategori_fokus == 'KU-10' && $pelatih->gender_fokus == 'Putra' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200'],
                                    $pelatih->kategori_fokus == 'KU-10' && $pelatih->gender_fokus == 'Putri' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
                                    
                                    $pelatih->kategori_fokus == 'KU-12' && $pelatih->gender_fokus == 'Putra' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200'],
                                    $pelatih->kategori_fokus == 'KU-12' && $pelatih->gender_fokus == 'Putri' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
                                    
                                    $pelatih->kategori_fokus == 'KU-14' && $pelatih->gender_fokus == 'Putra' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                                    $pelatih->kategori_fokus == 'KU-14' && $pelatih->gender_fokus == 'Putri' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'border' => 'border-red-200'],
                                    
                                    $pelatih->kategori_fokus == 'KU-16' && $pelatih->gender_fokus == 'Putra' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
                                    $pelatih->kategori_fokus == 'KU-16' && $pelatih->gender_fokus == 'Putri' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'border' => 'border-green-200'],
                                    
                                    $pelatih->kategori_fokus == 'KU-18' && $pelatih->gender_fokus == 'Putra' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
                                    $pelatih->kategori_fokus == 'KU-18' && $pelatih->gender_fokus == 'Putri' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                                    
                                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
                                };
                            @endphp
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-3 border-b border-gray-100 pb-4">
                            <span class="text-gray-500 text-sm font-medium">Kategori Umur Latihan</span>
                            <span class="sm:col-span-2 text-gray-900 font-semibold">
                                <span class="px-2 py-1 rounded text-xs border shadow-sm whitespace-nowrap {{ $warnaDetailCoach['bg'] }} {{ $warnaDetailCoach['text'] }} {{ $warnaDetailCoach['border'] }}">
                                    {{ $pelatih->kategori_fokus ?? '-' }}
                                </span>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 border-b border-gray-100 pb-4">
                            <span class="text-gray-500 text-sm font-medium">Gender Fokus Latihan</span>
                            <span class="sm:col-span-2 text-gray-900 font-semibold">
                                <span class="px-2 py-1 rounded text-xs border shadow-sm whitespace-nowrap {{ $warnaDetailCoach['bg'] }} {{ $warnaDetailCoach['text'] }} {{ $warnaDetailCoach['border'] }}">
                                    {{ $pelatih->gender_fokus ?? '-' }}
                                </span>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3">
                            <span class="text-gray-500 text-sm font-medium">Alamat Domisili</span>
                            <span class="sm:col-span-2 text-gray-900 leading-relaxed">
                                {{ $pelatih->alamat ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- BAGIAN 4: JAVASCRIPT UNTUK TOGGLE --}}
    {{-- ========================================== --}}
    <script>
        function bukaPdf(url) {
            // 1. Set source Iframe ke URL PDF
            document.getElementById('pdf-frame').src = url;

            // 2. Set link download agar sama dengan URL PDF
            document.getElementById('btn-download-pdf').href = url;
            
            // 3. Sembunyikan Konten Biodata
            document.getElementById('biodata-container').classList.add('hidden');
            
            // 4. Tampilkan Container PDF
            document.getElementById('pdf-container').classList.remove('hidden');

            // 5. Scroll ke atas otomatis
            window.scrollTo({top: 0, behavior: 'smooth'});
        }

        function tutupPdf() {
            document.getElementById('pdf-container').classList.add('hidden');
            document.getElementById('pdf-frame').src = "";
            document.getElementById('biodata-container').classList.remove('hidden');
        }
    </script>

</x-app-layout>