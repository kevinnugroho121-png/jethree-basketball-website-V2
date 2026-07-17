<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Preview Laporan Keuangan') }}
            </h2>
            {{-- Tombol Kembali --}}
            <a href="{{ route('tagihan.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-bold hover:bg-gray-300 transition">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    {{-- KONTEN UTAMA: IFRAME PDF --}}
    <div class="py-6 px-4 sm:px-6 lg:px-8 h-screen"> {{-- h-screen agar tinggi full --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full border border-gray-300 flex flex-col">
            
            {{-- Toolbar di atas PDF dengan Filter Interaktif --}}
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex flex-wrap justify-between items-center gap-3">
                
                {{-- Form Filter Bulan & Tahun Internal --}}
                <form method="GET" action="{{ route('tagihan.preview') }}" class="flex flex-wrap items-center gap-2">
                    {{-- Jaga agar filter kategori dari halaman depan tidak hilang --}}
                    @if(request('kategori'))
                        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                    @endif

                    @php
                        $namaBulanIndo = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                    @endphp

                    <select name="bulan" onchange="this.form.submit()" class="block h-9 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3 shadow-sm">
                        <option value="">Semua Bulan</option>
                        @foreach($namaBulanIndo as $angka => $nama)
                            <option value="{{ $angka }}" {{ request('bulan') == $angka ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>

                    <select name="tahun" onchange="this.form.submit()" class="block h-9 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3 shadow-sm">
                        @for($y = date('Y') - 1; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>

                <span class="text-sm text-gray-500">
                    Menampilkan Laporan: 
                    <td class="font-bold text-gray-800">
                        {{ request('bulan') ? $namaBulanIndo[request('bulan')] : 'Semua Bulan' }} 
                        {{ request('tahun') ?? date('Y') }}
                    </td>
                </span>
                
                <a href="{{ route('tagihan.cetak_pdf', array_merge(request()->query(), ['download' => 'true'])) }}" class="text-blue-600 hover:text-blue-800 text-sm font-bold underline">
                    Download File Asli
                </a>
            </div>

            {{-- 
                IFRAME SAKTI 
                src mengarah ke route 'tagihan.cetak_pdf' dengan membawa semua filter (bulan/tahun)
            --}}
            <iframe 
                src="{{ route('tagihan.cetak_pdf', request()->query()) }}" 
                class="w-full flex-1" 
                style="height: 75vh;" {{-- Tinggi 75% layar --}}
                frameborder="0">
            </iframe>

        </div>
    </div>
</x-app-layout>