<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Rekap Kehadiran Pelatih') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.rekap-pelatih') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
                        <label class="font-medium text-gray-700 whitespace-nowrap">Filter Periode:</label>
                        
                        <select name="bulan" class="w-full sm:w-auto border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                            @foreach($namaBulan as $key => $nama)
                                <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>

                        <select name="tahun" class="w-full sm:w-auto border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm">
                            @for($i = date('Y'); $i >= 2024; $i--)
                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>

                        <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 transition ease-in-out duration-150">
                            Tampilkan
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                
                {{-- PERUBAHAN 1: Header Tabel Ditambah Tombol Preview Semua (Tanpa tab baru) --}}
                <div class="p-6 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h3 class="text-lg font-bold text-gray-800">
                        Data Kinerja Bulan <span class="text-green-600">{{ $namaBulan[$bulan] }} {{ $tahun }}</span>
                    </h3>
                    
                    <a href="{{ route('admin.rekap-pelatih.preview-semua', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Preview PDF Semua
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Pelatih</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Total Jadwal</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-green-600 uppercase tracking-wider">Hadir (Ngabsen)</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-red-600 uppercase tracking-wider">Hangus (Alpa)</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-yellow-600 uppercase tracking-wider">Belum Mulai</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Tingkat Kinerja</th>
                                {{-- PERUBAHAN 2: Tambah Kolom Aksi --}}
                                <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($rekapData as $index => $data)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900">{{ $data['nama'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                        {{ $data['total_jadwal'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                            {{ $data['hadir'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-red-100 text-red-800">
                                            {{ $data['hangus'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $data['belum_mulai'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($data['persentase'] >= 80)
                                            <span class="text-xl font-bold text-green-600">{{ $data['persentase'] }}%</span>
                                        @elseif($data['persentase'] >= 50)
                                            <span class="text-xl font-bold text-yellow-500">{{ $data['persentase'] }}%</span>
                                        @else
                                            <span class="text-xl font-bold text-red-600">{{ $data['persentase'] }}%</span>
                                        @endif
                                    </td>
                                    
                                    {{-- PERUBAHAN 3: Tombol Preview PDF per Pelatih (Tanpa tab baru) --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('admin.rekap-pelatih.preview-pelatih', ['id' => $data['id'], 'bulan' => $bulan, 'tahun' => $tahun]) }}" class="inline-flex items-center px-3 py-1 bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-600 hover:text-white rounded-md text-xs font-semibold transition-colors duration-200">
                                            Preview PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- PERUBAHAN 4: colspan menjadi 8 karena nambah kolom Aksi --}}
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500 font-medium">
                                        Belum ada data pelatih untuk bulan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>