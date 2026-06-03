<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white shadow-sm sm:rounded-lg border border-gray-200 overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center bg-white gap-4">
                    
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <h2 class="font-bold text-xl text-gray-800">{{ $title }}</h2>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ $pdfUrl }}" download="Laporan_Rekap.pdf" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm transition text-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download PDF
                        </a>
                        
                        <a href="{{ route('admin.rekap-pelatih', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-md border border-gray-300 shadow-sm transition text-sm flex items-center">
                            x Tutup
                        </a>
                    </div>

                </div>

                <div class="w-full bg-gray-800" style="height: 80vh; min-height: 600px;">
                    <iframe src="{{ $pdfUrl }}#toolbar=1" class="w-full h-full" style="border: none;"></iframe>
                </div>

            </div>
            
        </div>
    </div>
</x-app-layout>