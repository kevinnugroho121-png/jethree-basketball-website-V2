<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Keuangan & SPP') }}
            </h2>
            <a href="{{ route('tagihan.create') }}" class="inline-flex items-center gap-1 h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-md transition">
                + Buat Tagihan
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="w-full px-2">

            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="mb-3 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 text-sm shadow-sm rounded flex justify-between items-center">
                    <span>✅ {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 font-bold px-2 hover:text-green-900">×</button>
                </div>
            @endif

            <div class="bg-white shadow-md rounded-xl overflow-hidden">

                {{-- TOOLBAR FILTER (Diperbarui: Tambah Filter Kategori & Status) --}}
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50 flex justify-between items-center flex-wrap gap-3">
                    
                    <form method="GET" action="{{ route('tagihan.index') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        
                        {{-- 1. Kotak Pencarian --}}
                        <div class="flex rounded-lg shadow-sm">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-36 sm:w-40 h-9 rounded-l-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3"
                                placeholder="Cari atlet...">
                            <button type="submit" class="bg-white border border-l-0 border-gray-300 h-9 px-2 rounded-r-lg hover:bg-gray-100 text-gray-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>

                        {{-- 2. Filter Kategori (Auto Submit saat dipilih) --}}
                        <select name="kategori" onchange="this.form.submit()" class="block h-9 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3 shadow-sm">
                            <option value="Semua" {{ request('kategori') == 'Semua' ? 'selected' : '' }}>Semua Kategori</option>
                            <option value="KU-10" {{ request('kategori') == 'KU-10' ? 'selected' : '' }}>KU-10</option>
                            <option value="KU-12" {{ request('kategori') == 'KU-12' ? 'selected' : '' }}>KU-12</option>
                            <option value="KU-14" {{ request('kategori') == 'KU-14' ? 'selected' : '' }}>KU-14</option>
                            <option value="KU-16" {{ request('kategori') == 'KU-16' ? 'selected' : '' }}>KU-16</option>
                            <option value="KU-18" {{ request('kategori') == 'KU-18' ? 'selected' : '' }}>KU-18</option>
                        </select>

                        {{-- 3. Filter Status Pembayaran (Auto Submit) --}}
                        <select name="status_bayar" onchange="this.form.submit()" class="block h-9 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3 shadow-sm">
                            <option value="Semua" {{ request('status_bayar') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="Menunggu Verifikasi" {{ request('status_bayar') == 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                            <option value="Nunggak" {{ request('status_bayar') == 'Nunggak' ? 'selected' : '' }}>Ada Tunggakan</option>
                            <option value="Lunas" {{ request('status_bayar') == 'Lunas' ? 'selected' : '' }}>Lunas Semua</option>
                        </select>

                        {{-- Tombol Reset (Muncul jika ada filter aktif) --}}
                        @if(request('search') || (request('kategori') && request('kategori') != 'Semua') || (request('status_bayar') && request('status_bayar') != 'Semua'))
                            <a href="{{ route('tagihan.index') }}" class="text-xs text-red-500 hover:underline font-bold ml-1">Reset</a>
                        @endif

                    </form>

                    {{-- Tombol Preview PDF --}}
                    <a href="{{ route('tagihan.preview', request()->query()) }}" class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1 w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Preview PDF
                    </a>
                </div>

                {{-- TABEL REKAP SPP PER ATLET --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="px-3 py-3 text-center font-semibold w-10">No</th>
                                <th class="px-4 py-3 font-semibold min-w-[180px]">Nama Atlet</th>
                                {{-- KOLOM BARU SEBELAH KANAN NAMA --}}
                                <th class="px-4 py-3 font-semibold text-left w-32">Kategori</th>
                                <th class="px-4 py-3 font-semibold text-center w-32">Total Tagihan</th>
                                <th class="px-4 py-3 font-semibold text-center w-32">Rekapitulasi</th>
                                <th class="px-4 py-3 font-semibold text-center w-32">Nunggak</th>
                                <th class="px-4 py-3 font-semibold text-center w-40">Bayar Terakhir</th>
                                <th class="px-3 py-3 font-semibold text-center w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($atlets as $index => $atlet)
                                @php
                                    $totalTagihan = $atlet->tagihans->count();
                                    $lunas = $atlet->tagihans->where('status', 'Lunas')->count();
                                    
                                    // Hitung spesifik yang nunggak dan yang butuh verifikasi
                                    $belumLunas = $atlet->tagihans->where('status', 'Belum Lunas')->count();
                                    $menungguVerif = $atlet->tagihans->where('status', 'Menunggu Verifikasi')->count();
                                    
                                    $bayarTerakhir = $atlet->tagihans->where('status', 'Lunas')->sortByDesc('tanggal_lunas')->first();

                                    // SIHIR KUNING: Jika ada yang butuh verifikasi, warnai kuning pekat.
                                    $rowClass = 'transition-colors duration-150 ';
                                    if ($menungguVerif > 0) {
                                        $rowClass .= 'bg-yellow-100 hover:bg-yellow-200 border-l-4 border-yellow-500'; 
                                    } else {
                                        $rowClass .= $loop->even ? 'bg-gray-50 hover:bg-green-50' : 'bg-white hover:bg-green-50';
                                    }
                                @endphp

                                <tr class="{{ $rowClass }}">

                                    <td class="px-3 py-3 text-center text-gray-500 font-medium {{ $menungguVerif > 0 ? 'border-l-0' : '' }}">
                                        {{ $atlets->firstItem() + $index }}
                                    </td>

                                    {{-- Kolom Nama Atlet Bersih --}}
                                    <td class="px-4 py-3 font-semibold text-gray-800">
                                        <div class="flex items-center gap-2">
                                            {{ $atlet->nama_lengkap }}
                                            
                                            {{-- Badge "CEK PEMBAYARAN" berkedip --}}
                                            @if($menungguVerif > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-extrabold bg-red-500 text-white shadow-sm animate-pulse">
                                                    CEK
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Kolom Baru Mengambil Data Real Kategori Atlet --}}
                                    <td class="px-4 py-3 text-sm text-gray-600 font-medium">
                                        <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-md text-xs font-semibold border border-gray-200">
                                            {{ $atlet->kategori ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center font-bold text-gray-700">
                                        {{ $totalTagihan }} Bulan
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-block text-[11px] font-bold px-2 py-1 rounded {{ $lunas == $totalTagihan && $totalTagihan > 0 ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                            {{ $lunas }} / {{ $totalTagihan }} Lunas
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if($belumLunas > 0)
                                            <span class="inline-block text-[11px] font-bold px-2 py-1 rounded bg-red-100 text-red-800 border border-red-200 animate-pulse">
                                                {{ $belumLunas }} Bulan
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-xs">-</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center text-xs">
                                        @if($bayarTerakhir && $bayarTerakhir->tanggal_lunas)
                                            <div class="font-semibold text-green-600">
                                                {{ \Carbon\Carbon::parse($bayarTerakhir->tanggal_lunas)->format('d/m/Y') }}
                                            </div>
                                        @else
                                            <span class="italic text-gray-400">Belum ada</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-3 text-center">
                                        {{-- Tombol Detail: Nanti arahnya ke halaman daftar SPP khusus atlet ini --}}
                                        <a href="{{ route('tagihan.show', $atlet->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-800 hover:bg-gray-900 text-white text-[11px] font-bold rounded-lg shadow-sm transition">
                                            Detail SPP
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">📭</span>
                                            <p class="font-medium">Data atlet tidak ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER PAGINATION (Sistem Sliding Window Dipertahankan) --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">{{ $atlets->firstItem() ?? 0 }}</span> -
                        <span class="font-semibold text-gray-700">{{ $atlets->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold text-gray-700">{{ $atlets->total() }}</span> Atlet
                    </p>

                    <div class="flex items-center gap-1">
                        {{-- Prev --}}
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

                        @if ($startPage > 1)
                            <a href="{{ $atlets->appends(request()->query())->url(1) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">1</a>
                            @if ($startPage > 2)
                                <span class="px-3 py-1.5 text-xs text-gray-400">...</span>
                            @endif
                        @endif

                        @foreach ($atlets->appends(request()->query())->getUrlRange($startPage, $endPage) as $page => $url)
                            @if ($page == $currentPage)
                                <span class="px-3 py-1.5 text-xs text-white bg-green-600 border border-green-600 rounded-lg font-bold shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="px-3 py-1.5 text-xs text-gray-400">...</span>
                            @endif
                            <a href="{{ $atlets->appends(request()->query())->url($lastPage) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $lastPage }}</a>
                        @endif

                        {{-- Next --}}
                        @if ($atlets->hasMorePages())
                            <a href="{{ $atlets->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                        @else
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>