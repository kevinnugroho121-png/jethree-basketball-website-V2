<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manajemen Jadwal Latihan') }}
            </h2>
            <a href="{{ route('jadwal.create') }}" class="inline-flex items-center gap-1 h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                Tambah Jadwal
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="w-full px-2">

            {{-- Notifikasi Sukses --}}
            @if(session('success'))
                <div class="mb-3 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 text-sm shadow-sm rounded flex justify-between items-center">
                    <span>✅ <strong>Berhasil!</strong> {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 font-bold px-2 hover:text-green-900">×</button>
                </div>
            @endif

            {{-- Alert Mode Absensi --}}
            @if(request('fokus') == 'absensi')
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-3 mb-3 rounded flex items-center justify-between shadow-sm animate-pulse">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">📋</span>
                        <div>
                            <h3 class="font-bold text-indigo-900 text-sm">Mode Penilaian & Absensi</h3>
                            <p class="text-xs text-indigo-700">
                                Silakan klik tombol <span class="font-bold bg-white border border-indigo-200 px-1 rounded text-indigo-800">📋 Absen</span> pada jadwal di bawah.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-md rounded-xl overflow-hidden">

                {{-- TOOLBAR FILTER --}}
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('jadwal.index') }}" class="flex flex-wrap items-center gap-2">

                        <select name="kategori" class="h-9 text-sm border border-gray-300 rounded-lg px-3 bg-white shadow-sm focus:border-green-500 focus:ring-green-500 cursor-pointer">
                            <option value="">- Semua Kategori -</option>
                            <option value="Semua Umur" {{ request('kategori') == 'Semua Umur' ? 'selected' : '' }}>Semua Umur</option>
                            <option value="KU-10" {{ request('kategori') == 'KU-10' ? 'selected' : '' }}>KU-10</option>
                            <option value="KU-12" {{ request('kategori') == 'KU-12' ? 'selected' : '' }}>KU-12</option>
                            <option value="KU-14" {{ request('kategori') == 'KU-14' ? 'selected' : '' }}>KU-14</option>
                            <option value="KU-16" {{ request('kategori') == 'KU-16' ? 'selected' : '' }}>KU-16</option>
                            <option value="KU-18" {{ request('kategori') == 'KU-18' ? 'selected' : '' }}>KU-18</option>
                        </select>

                        <div class="flex items-center gap-1 bg-white border border-gray-300 rounded-lg px-3 h-9 shadow-sm">
                            <input type="date" name="mulai_tanggal" value="{{ request('mulai_tanggal') }}" class="border-none text-sm focus:ring-0 p-0 w-32 text-gray-600">
                            <span class="text-gray-400 font-bold">-</span>
                            <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="border-none text-sm focus:ring-0 p-0 w-32 text-gray-600">
                        </div>

                        <button type="submit" class="h-9 px-4 bg-gray-700 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition shadow-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            FILTER
                        </button>

                        <a href="{{ route('jadwal.index') }}" class="h-9 px-4 bg-white border border-red-300 text-red-600 text-xs font-bold rounded-lg hover:bg-red-50 transition shadow-sm flex items-center">
                            RESET
                        </a>
                    </form>
                </div>

                {{-- TABEL --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="px-3 py-3 text-center font-semibold w-10">No</th>
                                <th class="px-4 py-3 font-semibold w-44">Tanggal & Jam</th>
                                <th class="px-4 py-3 font-semibold min-w-[160px]">Materi</th>
                                <th class="px-4 py-3 text-center font-semibold w-24">Kategori</th>
                                <th class="px-4 py-3 font-semibold min-w-[140px]">Pelatih</th>
                                <th class="px-4 py-3 font-semibold min-w-[160px]">Lokasi</th>
                                <th class="px-3 py-3 text-center font-semibold w-24">Status</th>
                                <th class="px-3 py-3 text-center font-semibold w-44">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($jadwals as $index => $jadwal)
                                @php
                                    $warnaKU = match($jadwal->kategori) {
                                        'KU-10' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                        'KU-12' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                        'KU-14' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                        'KU-16' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                        'KU-18' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700'],
                                        default  => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                                    };
                                @endphp
                                <tr class="hover:bg-green-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">

                                    <td class="px-3 py-2 text-center text-gray-500 font-medium">
                                        {{ $jadwals->firstItem() + $index }}
                                    </td>

                                    <td class="px-4 py-2">
                                        <div class="font-bold text-gray-900 text-sm leading-tight">
                                            {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d M Y') }}
                                        </div>
                                        <div class="text-xs text-blue-700 font-bold mt-1 bg-blue-50 w-fit px-1.5 py-0.5 rounded border border-blue-100">
                                            ⏰ {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-2 text-gray-700 text-sm italic">
                                        {{ $jadwal->materi ?? '-' }}
                                    </td>

                                    <td class="px-4 py-2 text-center">
                                        <span class="inline-block {{ $warnaKU['bg'] }} {{ $warnaKU['text'] }} text-xs font-bold px-2 py-1 rounded uppercase">
                                            {{ $jadwal->kategori }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-2">
                                        @if($jadwal->pelatih)
                                            <div class="font-semibold text-gray-900">Coach {{ $jadwal->pelatih->nama_lengkap }}</div>
                                        @else
                                            <span class="text-red-400 italic text-xs">Belum ditentukan</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2 text-xs text-gray-600 leading-relaxed">
                                        {{ $jadwal->lokasi }}
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        @if($jadwal->status == 'Aktif')
                                            <span class="text-green-700 font-bold bg-green-50 px-2 py-1 rounded border border-green-200 text-[10px]">AKTIF</span>
                                        @else
                                            <span class="text-red-700 font-bold bg-red-50 px-2 py-1 rounded border border-red-200 text-[10px] uppercase">{{ $jadwal->status }}</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <div class="flex justify-center items-center gap-1">
                                            {{-- Tombol Absen --}}
                                            <a href="{{ route('absensi.create', $jadwal->id) }}" 
                                               class="inline-flex items-center gap-1 bg-indigo-600 text-white px-2.5 py-1.5 rounded-lg hover:bg-indigo-700 transition text-[11px] font-bold shadow-sm">
                                               ABSEN
                                            </a>
                                            {{-- Edit --}}
                                            <a href="{{ route('jadwal.edit', $jadwal->id) }}" class="p-1.5 bg-yellow-50 text-yellow-600 border border-yellow-200 rounded-lg hover:bg-yellow-500 hover:text-white transition shadow-sm" title="Edit Jadwal">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            {{-- Hapus --}}
                                            <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm" title="Hapus Jadwal">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">📭</span>
                                            <p class="font-medium">Belum ada jadwal latihan yang dibuat.</p>
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
                            <a href="{{ $jadwals->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&laquo;</a>
                        @endif

                        {{-- Nomor Halaman (Otomatis Titik-Titik & Aman untuk Filter) --}}
                        @php
                            $currentPage = $jadwals->currentPage();
                            $lastPage = $jadwals->lastPage();
                        @endphp

                        @for ($page = 1; $page <= $lastPage; $page++)
                            @if ($page == 1 || $page == $lastPage || abs($page - $currentPage) <= 1)
                                @if ($page == $currentPage)
                                    <span class="px-3 py-1.5 text-xs text-white bg-green-600 border border-green-600 rounded-lg font-bold shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $jadwals->appends(request()->query())->url($page) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $page }}</a>
                                @endif
                            @elseif ($page == 2 && $currentPage > 3)
                                <span class="px-3 py-1.5 text-xs text-gray-400 bg-white border border-gray-200 rounded-lg">...</span>
                            @elseif ($page == $lastPage - 1 && $currentPage < $lastPage - 2)
                                <span class="px-3 py-1.5 text-xs text-gray-400 bg-white border border-gray-200 rounded-lg">...</span>
                            @endif
                        @endfor

                        @if ($jadwals->hasMorePages())
                            <a href="{{ $jadwals->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                        @else
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>