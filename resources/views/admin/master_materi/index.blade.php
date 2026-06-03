<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-10">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ ('Silabus Materi Latihan') }}
            </h2>
            <a href="{{ route('master-materi.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                + Tambah Silabus
            </a>
        </div>
    </x-slot>

    <div class="py-1">
        <div class="mx-auto px-4 sm:px-2 lg:px-2">

            {{-- Notifikasi Sukses --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="px-5 py-3 text-center font-semibold tracking-wide w-12">No</th>
                                <th class="px-5 py-3 text-center font-semibold tracking-wide">Kategori</th>
                                <th class="px-5 py-3 text-center font-semibold tracking-wide">Pertemuan Ke</th>
                                <th class="px-5 py-3 font-semibold tracking-wide">Judul Materi Latihan</th>
                                <th class="px-5 py-3 text-center font-semibold tracking-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($materis as $materi)
                                <tr class="hover:bg-green-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="px-5 py-2 text-center text-gray-500 font-medium">{{ $materis->firstItem() + $loop->index }}</td>
                                    
                                    
                                    
                                    <td class="px-5 py-2 text-center">
                                        @php
                                            $warna = match($materi->kategori) {
                                                'KU-10' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                                'KU-12' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                                'KU-14' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                                'KU-16' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                                'KU-18' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700'],
                                                default  => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                                            };
                                        @endphp
                                        <span class="inline-block {{ $warna['bg'] }} {{ $warna['text'] }} text-xs font-bold px-2 py-1 rounded">
                                            {{ $materi->kategori }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-2 text-center font-semibold {{ $warna['text'] }}">Ke-{{ $materi->pertemuan_ke }}</td>
                                                                        
                                    
                                    <td class="px-5 py-2 text-gray-800">{{ $materi->judul_materi }}</td>
                                    <td class="px-5 py-2 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('master-materi.edit', $materi->id) }}" 
                                            style="background-color: #EAB308; color: white;"
                                            class="font-medium rounded-lg text-xs px-3 py-1.5 transition shadow-sm">
                                            Edit
                                        </a>
                                            <form action="{{ route('master-materi.destroy', $materi->id) }}" method="POST" 
                                                  onsubmit="return confirm('Yakin ingin menghapus materi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center gap-1 text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-xs px-3 py-1.5 transition shadow-sm">
                                                        Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">📭</span>
                                            <span>Belum ada data silabus materi.</span>
                                            <a href="{{ route('master-materi.create') }}" class="text-blue-500 hover:underline text-sm">+ Tambah Silabus sekarang</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Footer Pagination --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">{{ $materis->firstItem() }}</span> - 
                        <span class="font-semibold text-gray-700">{{ $materis->lastItem() }}</span> 
                        dari <span class="font-semibold text-gray-700">{{ $materis->total() }}</span> data
                    </p>

                    {{-- Custom Pagination --}}
                    <div class="flex items-center gap-1">
                        {{-- Tombol Prev --}}
                        @if ($materis->onFirstPage())
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&laquo;</span>
                        @else
                            <a href="{{ $materis->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&laquo;</a>
                        @endif

                        {{-- Nomor Halaman (Otomatis Titik-Titik) --}}
                        @php
                            $currentPage = $materis->currentPage();
                            $lastPage = $materis->lastPage();
                        @endphp

                        @for ($page = 1; $page <= $lastPage; $page++)
                            {{-- Tampilkan angka halaman awal, akhir, dan 1 angka di kiri-kanan halaman aktif --}}
                            @if ($page == 1 || $page == $lastPage || abs($page - $currentPage) <= 1)
                                @if ($page == $currentPage)
                                    <span class="px-3 py-1.5 text-xs text-white bg-green-600 border border-green-600 rounded-lg font-bold shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $materis->url($page) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $page }}</a>
                                @endif
                            
                            {{-- Tampilkan titik-titik di awal --}}
                            @elseif ($page == 2 && $currentPage > 3)
                                <span class="px-3 py-1.5 text-xs text-gray-400 bg-white border border-gray-200 rounded-lg">...</span>
                            
                            {{-- Tampilkan titik-titik di akhir --}}
                            @elseif ($page == $lastPage - 1 && $currentPage < $lastPage - 2)
                                <span class="px-3 py-1.5 text-xs text-gray-400 bg-white border border-gray-200 rounded-lg">...</span>
                            @endif
                        @endfor

                        {{-- Tombol Next --}}
                        @if ($materis->hasMorePages())
                            <a href="{{ $materis->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                        @else
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>