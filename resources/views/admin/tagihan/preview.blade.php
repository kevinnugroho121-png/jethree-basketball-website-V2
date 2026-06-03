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
            
            {{-- Toolbar di atas PDF (Opsional) --}}
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <span class="text-sm text-gray-500">
                    Menampilkan Laporan: 
                    <b>
                        {{ request('bulan') ? DateTime::createFromFormat('!m', request('bulan'))->format('F') : 'Semua Bulan' }} 
                        {{ request('tahun') ?? date('Y') }}
                    </b>
                </span>
                
                {{-- Tombol Download Langsung --}}
                {{-- PERBAIKAN: Gunakan array_merge untuk menggabungkan query string yang sudah ada dengan parameter download --}}
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