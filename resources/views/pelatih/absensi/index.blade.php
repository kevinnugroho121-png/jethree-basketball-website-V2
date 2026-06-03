<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Jadwal Latihan (Absensi)') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="w-full px-2">

            <div class="bg-white shadow-md rounded-xl overflow-hidden">

                {{-- Header Card --}}
                <div class="bg-green-600 px-6 py-3 flex justify-between items-center">
                    <h3 class="text-white font-semibold text-sm tracking-wide">
                        Halo, Coach {{ Auth::user()->name }}!
                    </h3>
                    <span class="text-green-100 text-xs hidden sm:inline">Silakan pilih jadwal untuk mengisi absen.</span>
                </div>

                {{-- TOOLBAR FILTER LENGKAP & RAPI --}}
                <div class="px-5 py-4 border-b border-gray-200 bg-gray-50">
                    <form method="GET" action="{{ route('pelatih.absensi.index') }}" class="flex flex-col md:flex-row flex-wrap items-end gap-4 w-full">
                        
                        {{-- 1. Input Pencarian (Lokasi) --}}
                        <div class="w-full md:w-auto flex-1 min-w-[200px]">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Cari Lokasi</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-full h-9 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3 shadow-sm"
                                placeholder="Ketik nama lapangan...">
                        </div>

                        {{-- 2. Filter Bulan --}}
                        <div class="w-full md:w-auto min-w-[130px]">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Bulan</label>
                            <select name="bulan" class="block w-full h-9 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3 shadow-sm">
                                <option value="Semua" {{ request('bulan') == 'Semua' ? 'selected' : '' }}>Semua Bulan</option>
                                <option value="01" {{ request('bulan') == '01' ? 'selected' : '' }}>Januari</option>
                                <option value="02" {{ request('bulan') == '02' ? 'selected' : '' }}>Februari</option>
                                <option value="03" {{ request('bulan') == '03' ? 'selected' : '' }}>Maret</option>
                                <option value="04" {{ request('bulan') == '04' ? 'selected' : '' }}>April</option>
                                <option value="05" {{ request('bulan') == '05' ? 'selected' : '' }}>Mei</option>
                                <option value="06" {{ request('bulan') == '06' ? 'selected' : '' }}>Juni</option>
                                <option value="07" {{ request('bulan') == '07' ? 'selected' : '' }}>Juli</option>
                                <option value="08" {{ request('bulan') == '08' ? 'selected' : '' }}>Agustus</option>
                                <option value="09" {{ request('bulan') == '09' ? 'selected' : '' }}>September</option>
                                <option value="10" {{ request('bulan') == '10' ? 'selected' : '' }}>Oktober</option>
                                <option value="11" {{ request('bulan') == '11' ? 'selected' : '' }}>November</option>
                                <option value="12" {{ request('bulan') == '12' ? 'selected' : '' }}>Desember</option>
                            </select>
                        </div>

                        {{-- 3. Filter Kategori --}}
                        <div class="w-full md:w-auto min-w-[120px]">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Kategori (KU)</label>
                            <select name="kategori" class="block w-full h-9 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3 shadow-sm">
                                <option value="Semua" {{ request('kategori') == 'Semua' ? 'selected' : '' }}>Semua KU</option>
                                <option value="KU-10" {{ request('kategori') == 'KU-10' ? 'selected' : '' }}>KU-10</option>
                                <option value="KU-12" {{ request('kategori') == 'KU-12' ? 'selected' : '' }}>KU-12</option>
                                <option value="KU-14" {{ request('kategori') == 'KU-14' ? 'selected' : '' }}>KU-14</option>
                                <option value="KU-16" {{ request('kategori') == 'KU-16' ? 'selected' : '' }}>KU-16</option>
                                <option value="KU-18" {{ request('kategori') == 'KU-18' ? 'selected' : '' }}>KU-18</option>
                            </select>
                        </div>

                        {{-- 4. Filter Status Absen --}}
                        <div class="w-full md:w-auto min-w-[140px]">
                            <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status</label>
                            <select name="status_absen" class="block w-full h-9 rounded-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3 shadow-sm">
                                <option value="Semua" {{ request('status_absen') == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                                <option value="Sudah" {{ request('status_absen') == 'Sudah' ? 'selected' : '' }}>Sudah Diabsen</option>
                                <option value="Belum" {{ request('status_absen') == 'Belum' ? 'selected' : '' }}>Belum Diabsen</option>
                            </select>
                        </div>

                        {{-- Tombol Aksi (Terapkan & Reset) --}}
                        <div class="flex items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
                            <button type="submit" class="flex-1 md:flex-none h-9 px-5 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                Terapkan
                            </button>

                            @if(request('search') || (request('kategori') && request('kategori') != 'Semua') || (request('status_absen') && request('status_absen') != 'Semua') || (request('bulan') && request('bulan') != 'Semua'))
                                <a href="{{ route('pelatih.absensi.index') }}" class="h-9 px-4 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-bold rounded-lg transition flex items-center justify-center">
                                    Reset
                                </a>
                            @endif
                        </div>

                    </form>
                </div>

                {{-- TABEL --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-gray-800 text-white">
                                <th class="px-5 py-3 font-semibold">Tanggal Latihan</th>
                                <th class="px-5 py-3 font-semibold text-center w-28">Kategori</th>
                                <th class="px-5 py-3 font-semibold min-w-[150px]">Lokasi</th>
                                <th class="px-5 py-3 font-semibold w-32">Waktu</th>
                                {{-- KOLOM BARU UNTUK REVISI 7 --}}
                                <th class="px-5 py-3 font-semibold text-center min-w-[140px]">Kehadiran</th>
                                <th class="px-5 py-3 font-semibold text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($jadwals as $item)
                                @php
                                    $warnaKU = match($item->kategori) {
                                        'KU-10' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                        'KU-12' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                        'KU-14' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                        'KU-16' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                        'KU-18' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700'],
                                        default  => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                                    };

                                    // LOGIKA 7 HARI (Sisa Waktu)
                                    $tglLatihan = \Carbon\Carbon::parse($item->tanggal)->startOfDay();
                                    $hariIni = \Carbon\Carbon::now()->startOfDay();
                                    $selisihHari = $tglLatihan->diffInDays($hariIni, false);
                                    $isExpired = $selisihHari > 7;

                                    // DATA SUPER DARI CONTROLLER BARU
                                    $isSudahDiabsen = $item->total_diabsen > 0;
                                    
                                    // [SIHIR KUNING] Jadwal Hari Ini disorot Kuning & Border Tebel
                                    $rowClass = 'transition-colors duration-150 ';
                                    if ($item->is_today) {
                                        $rowClass .= 'bg-yellow-100 hover:bg-yellow-200 border-l-4 border-yellow-500';
                                    } else {
                                        $rowClass .= $loop->even ? 'bg-gray-50 hover:bg-green-50' : 'bg-white hover:bg-green-50';
                                    }
                                @endphp

                                <tr class="{{ $rowClass }}">

                                    <td class="px-5 py-2 font-semibold text-gray-900 {{ $item->is_today ? 'border-l-0' : '' }}">
                                        <div class="flex flex-col gap-1 items-start">
                                            <span>{{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('dddd, D MMMM Y') }}</span>
                                            
                                            {{-- BADGE KEDIP HARI INI --}}
                                            @if($item->is_today)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-extrabold bg-red-500 text-white shadow-sm animate-pulse mt-0.5">
                                                    JADWAL HARI INI
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-5 py-2 text-center">
                                        <span class="inline-block {{ $warnaKU['bg'] }} {{ $warnaKU['text'] }} text-xs font-bold px-2 py-1 rounded">
                                            {{ $item->kategori }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-2 text-gray-600">
                                        <div class="flex items-center gap-2 text-xs">
                                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ $item->lokasi }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-2 font-mono text-xs text-gray-700">
                                        {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}
                                    </td>

                                    {{-- ISI KOLOM BARU: REKAP HADIR CEPAT --}}
                                    <td class="px-5 py-2 text-center">
                                        @if($isSudahDiabsen)
                                            <div class="text-[11px] font-bold text-black-700 bg-green-50 rounded-lg px-2 py-1.5 inline-block border border-green-200 shadow-sm">
                                                <span class="text-green-700 text-sm">{{ $item->total_hadir }}</span> <span class="text-gray-400">/ {{ $item->total_anak }}</span>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-white-400 bg-red-100 px-2 py-1 rounded border border-red-200 font-extrabold bg-red-500">BELUM</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-2 text-center">
                                        @if($isExpired)
                                            {{-- Jika Expired, Tombol Jadi Merah Pucat --}}
                                            <a href="{{ route('pelatih.absensi.create', $item->id) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 text-[11px] font-bold rounded-lg transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                                Terkunci
                                            </a>
                                        @else
                                            {{-- Normal Button --}}
                                            <a href="{{ route('pelatih.absensi.create', $item->id) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold rounded-lg transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Input Absen
                                            </a>
                                            {{-- BADGE SISA WAKTU --}}
                                            @if($selisihHari >= 0 && $selisihHari <= 7 && !$isSudahDiabsen)
                                                <div class="mt-1 text-[10px] text-orange-600 font-bold bg-orange-100 rounded px-1 py-0.5 inline-block">
                                                    Sisa {{ 7 - $selisihHari }} Hari
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    {{-- Colspan diubah jadi 6 karena tambah 1 kolom --}}
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">📭</span>
                                            <p class="font-medium">Belum ada jadwal latihan yang ditugaskan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER PAGINATION --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">{{ $jadwals->firstItem() }}</span> -
                        <span class="font-semibold text-gray-700">{{ $jadwals->lastItem() }}</span>
                        dari <span class="font-semibold text-gray-700">{{ $jadwals->total() }}</span> data
                    </p>

                    <div class="flex items-center gap-1">
                        @if ($jadwals->onFirstPage())
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&laquo;</span>
                        @else
                            <a href="{{ $jadwals->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&laquo;</a>
                        @endif

                        @foreach ($jadwals->getUrlRange(1, $jadwals->lastPage()) as $page => $url)
                            @if ($page == $jadwals->currentPage())
                                <span class="px-3 py-1.5 text-xs text-white bg-green-600 border border-green-600 rounded-lg font-bold shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($jadwals->hasMorePages())
                            <a href="{{ $jadwals->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                        @else
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>