<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Progres Atlet') }}
        </h2>
    </x-slot>

    <div class="w-full h-[calc(100vh-70px)] bg-gray-50 p-4 flex flex-col overflow-hidden">

        @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center">
                <p class="font-bold">✅ {{ session('success') }}</p>
                <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">&times;</button>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm flex flex-col h-full overflow-hidden">

            {{-- TOOLBAR FILTER --}}
            <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <form action="{{ route('pelatih.progres.index') }}" method="GET" class="flex flex-col md:flex-row items-end gap-3 w-full">
                    
                    {{-- Judul (Hanya muncul di desktop biar rapi) --}}
                    <div class="hidden md:block mr-2">
                        <h3 class="font-bold text-gray-800 text-sm">Filter Atlet</h3>
                    </div>

                    {{-- 1. Filter Kategori --}}
                    <div class="w-full md:w-40">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kategori</label>
                        <select name="kategori" class="w-full h-9 rounded-lg border-gray-300 text-xs focus:ring-green-500 focus:border-green-500 shadow-sm">
                            <option value="">Semua Kategori</option>
                            <option value="KU-10" {{ request('kategori') == 'KU-10' ? 'selected' : '' }}>KU-10</option>
                            <option value="KU-12" {{ request('kategori') == 'KU-12' ? 'selected' : '' }}>KU-12</option>
                            <option value="KU-14" {{ request('kategori') == 'KU-14' ? 'selected' : '' }}>KU-14</option>
                            <option value="KU-16" {{ request('kategori') == 'KU-16' ? 'selected' : '' }}>KU-16</option>
                            <option value="KU-18" {{ request('kategori') == 'KU-18' ? 'selected' : '' }}>KU-18</option>
                        </select>
                    </div>

                    {{-- 2. Pencarian Nama --}}
                    <div class="w-full md:w-64">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Atlet</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama atlet..."
                               class="w-full h-9 rounded-lg border-gray-300 text-xs focus:ring-green-500 focus:border-green-500 shadow-sm">
                    </div>

                    {{-- 3. Tombol Aksi --}}
                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="submit" class="flex-1 md:flex-none h-9 px-6 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold rounded-lg transition shadow-md">
                            Cari
                        </button>
                        @if(request('search') || request('kategori'))
                            <a href="{{ route('pelatih.progres.index') }}" class="h-9 px-4 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-lg flex items-center transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

                <div class="hidden md:block">
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Atlet Binaan</h3>
                    <p class="text-xs text-gray-500">Pilih atlet untuk mengisi rapor</p>
                </div>

                <form action="{{ route('pelatih.progres.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">

                    <select name="kategori" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm focus:ring-green-500 focus:border-green-500 cursor-pointer">
                        <option value="">Semua Kategori</option>
                        <option value="KU-10" {{ request('kategori') == 'KU-10' ? 'selected' : '' }}>KU-10 Mix</option>
                        <option value="KU-12" {{ request('kategori') == 'KU-12' ? 'selected' : '' }}>KU-12</option>
                        <option value="KU-14" {{ request('kategori') == 'KU-14' ? 'selected' : '' }}>KU-14</option>
                        <option value="KU-16" {{ request('kategori') == 'KU-16' ? 'selected' : '' }}>KU-16</option>
                        <option value="KU-18" {{ request('kategori') == 'KU-18' ? 'selected' : '' }}>KU-18</option>
                    </select>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atlet"
                               class="pl-10 rounded-lg border-gray-300 text-sm focus:ring-green-500 focus:border-green-500 w-full sm:w-64">
                    </div>

                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                        Cari
                    </button>

                    @if(request('search') || request('kategori'))
                        <a href="{{ route('pelatih.progres.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg text-sm font-bold text-center transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- TABEL --}}
            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-white uppercase bg-green-600 sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-6 py-3 text-center w-12">No</th>
                            <th class="px-6 py-3">Nama Atlet</th>
                            <th class="px-6 py-3">Kategori</th>
                            <th class="px-6 py-3">Posisi</th>
                            <th class="px-6 py-3 text-center w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($atlets as $index => $atlet)
                            @php
                                $warnaKU = match($atlet->kategori) {
                                    'KU-10' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                    'KU-12' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                    'KU-14' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                    'KU-16' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                    'KU-18' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700'],
                                    default  => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                                };
                            @endphp
                            <tr class="hover:bg-green-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">

                                <td class="px-6 py-3 text-center font-medium text-gray-900">
                                    {{ $atlets->firstItem() + $index }}
                                </td>

                                <td class="px-6 py-3">
                                    <div class="font-bold text-gray-800">{{ $atlet->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-400">Gender: {{ $atlet->jenis_kelamin }}</div>
                                </td>

                                <td class="px-6 py-3">
                                    <span class="inline-block {{ $warnaKU['bg'] }} {{ $warnaKU['text'] }} text-xs font-bold px-2 py-1 rounded">
                                        {{ $atlet->kategori }}
                                    </span>
                                </td>

                                <td class="px-6 py-3 text-gray-600">
                                    {{ $atlet->posisi ?? 'Belum Ditentukan' }}
                                </td>

                                <td class="px-6 py-3 text-center">
                                    <a href="{{ route('pelatih.progres.create', $atlet->id) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-md transform hover:scale-105">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Rapor
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="text-4xl">📭</span>
                                        <p class="font-semibold">Atlet tidak ditemukan.</p>
                                        <p class="text-sm">Coba ubah kata kunci pencarian atau filter.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER PAGINATION (Sudah Diperbarui) --}}
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500">
                    Menampilkan <span class="font-semibold text-gray-700">{{ $atlets->firstItem() ?? 0 }}</span> -
                    <span class="font-semibold text-gray-700">{{ $atlets->lastItem() ?? 0 }}</span>
                    dari <span class="font-semibold text-gray-700">{{ $atlets->total() }}</span> data
                </p>

                <div class="flex items-center gap-1">
                    {{-- Tombol Sebelumnya (Prev) --}}
                    @if ($atlets->onFirstPage())
                        <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&laquo;</span>
                    @else
                        <a href="{{ $atlets->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&laquo;</a>
                    @endif

                    {{-- Logika Pembatasan Angka Halaman (Sliding Window) --}}
                    @php
                        $currentPage = $atlets->currentPage();
                        $lastPage = $atlets->lastPage();
                        $startPage = max($currentPage - 2, 1);
                        $endPage = min($currentPage + 2, $lastPage);
                    @endphp

                    {{-- Menampilkan Halaman 1 & Titik-titik Awal --}}
                    @if ($startPage > 1)
                        <a href="{{ $atlets->appends(request()->query())->url(1) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">1</a>
                        @if ($startPage > 2)
                            <span class="px-3 py-1.5 text-xs text-gray-400">...</span>
                        @endif
                    @endif

                    {{-- Menampilkan Range Halaman di Tengah --}}
                    @foreach ($atlets->appends(request()->query())->getUrlRange($startPage, $endPage) as $page => $url)
                        @if ($page == $currentPage)
                            <span class="px-3 py-1.5 text-xs text-white bg-green-600 border border-green-600 rounded-lg font-bold shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Menampilkan Titik-titik Akhir & Halaman Terakhir --}}
                    @if ($endPage < $lastPage)
                        @if ($endPage < $lastPage - 1)
                            <span class="px-3 py-1.5 text-xs text-gray-400">...</span>
                        @endif
                        <a href="{{ $atlets->appends(request()->query())->url($lastPage) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $lastPage }}</a>
                    @endif

                    {{-- Tombol Selanjutnya (Next) --}}
                    @if ($atlets->hasMorePages())
                        <a href="{{ $atlets->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                    @else
                        <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>