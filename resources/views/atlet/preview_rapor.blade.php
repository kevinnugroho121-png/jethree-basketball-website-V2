<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Preview Laporan') }}
            </h2>
            <a href="{{ route('atlet.dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm font-bold hover:bg-gray-300 transition">
                &larr; KEMBALI KE DASHBOARD
            </a>
        </div>
    </x-slot>

    <div class="py-6 px-4 h-screen">
        <div class="max-w-7xl mx-auto h-full pb-20"> <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg h-full border border-gray-200 flex flex-col">
                
                {{-- Header Frame --}}
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <span class="text-sm text-gray-500 font-medium">📄 Dokumen Rapor / Portfolio</span>
                    
                    {{-- Tombol Download Manual (Opsional) --}}
                    <a href="{{ route('atlet.cetak_rapor') }}" download class="text-sm text-blue-600 hover:underline font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Download File PDF
                    </a>
                </div>

                {{-- IFRAME PDF (INILAH "FRAME" NYA) --}}
                <iframe src="{{ route('atlet.cetak_rapor') }}" class="w-full flex-1" style="height: 800px;" frameborder="0">
                </iframe>
                
            </div>
        </div>
    </div>
</x-app-layout>