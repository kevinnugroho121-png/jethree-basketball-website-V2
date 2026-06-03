<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Absensi Kehadiran') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="w-full px-2">

            <div class="bg-white shadow-md rounded-xl overflow-hidden">

                {{-- Header Card --}}
                <div class="bg-green-600 px-6 py-3">
                    <h3 class="text-white font-semibold text-sm tracking-wide">📋 Pilih Jadwal Latihan</h3>
                </div>

                <div class="p-4">
                    {{-- Alert Info --}}
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-4 rounded">
                        <p class="text-sm text-blue-700">
                            Silakan pilih jadwal latihan di bawah ini untuk mulai mengisi <strong>Absensi & Nilai</strong> atlet.
                        </p>
                    </div>

                    {{-- Tabel --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="bg-green-600 text-white">
                                    <th class="px-4 py-3 font-semibold">Tanggal & Waktu</th>
                                    <th class="px-4 py-3 font-semibold text-center w-28">Kategori</th>
                                    <th class="px-4 py-3 font-semibold min-w-[200px]">Materi</th>
                                    <th class="px-4 py-3 font-semibold min-w-[180px]">Lokasi</th>
                                    <th class="px-4 py-3 font-semibold text-center w-36">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($jadwals as $jadwal)
                                    @php
                                        $warnaKU = match($jadwal->kategori) {
                                            'KU-10' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
                                            'KU-12' => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
                                            'KU-14' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
                                            'KU-16' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
                                            'KU-18' => ['bg' => 'bg-sky-100', 'text' => 'text-sky-700'],
                                            default  => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'],
                                        };
                                        $route = (Auth::user()->role == 'pelatih')
                                            ? route('pelatih.absensi.create', $jadwal->id)
                                            : route('absensi.create', $jadwal->id);
                                    @endphp
                                    <tr class="hover:bg-green-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">

                                        <td class="px-4 py-2">
                                            <div class="font-bold text-gray-900 text-sm">
                                                {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}
                                            </div>
                                            <div class="text-xs text-blue-700 font-bold mt-1 bg-blue-50 w-fit px-1.5 py-0.5 rounded border border-blue-100">
                                                ⏰ {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-2 text-center">
                                            <span class="inline-block {{ $warnaKU['bg'] }} {{ $warnaKU['text'] }} text-xs font-bold px-2 py-1 rounded">
                                                {{ $jadwal->kategori }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-2 text-sm text-gray-600">
                                            {{ $jadwal->materi ?? '-' }}
                                        </td>

                                        <td class="px-4 py-2 text-sm text-gray-600">
                                            {{ $jadwal->lokasi }}
                                        </td>

                                        <td class="px-4 py-2 text-center">
                                            <a href="{{ $route }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                                Input Absen
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                                            <div class="flex flex-col items-center gap-2">
                                                <span class="text-4xl">📭</span>
                                                <p class="font-medium">Belum ada jadwal latihan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
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

                            {{-- Nomor Halaman (Otomatis Titik-Titik) --}}
                            @php
                                $currentPage = $jadwals->currentPage();
                                $lastPage = $jadwals->lastPage();
                            @endphp

                            @for ($page = 1; $page <= $lastPage; $page++)
                                @if ($page == 1 || $page == $lastPage || abs($page - $currentPage) <= 1)
                                    @if ($page == $currentPage)
                                        <span class="px-3 py-1.5 text-xs text-white bg-green-600 border border-green-600 rounded-lg font-bold shadow-sm">{{ $page }}</span>
                                    @else
                                        <a href="{{ $jadwals->url($page) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $page }}</a>
                                    @endif
                                @elseif ($page == 2 && $currentPage > 3)
                                    <span class="px-3 py-1.5 text-xs text-gray-400 bg-white border border-gray-200 rounded-lg">...</span>
                                @elseif ($page == $lastPage - 1 && $currentPage < $lastPage - 2)
                                    <span class="px-3 py-1.5 text-xs text-gray-400 bg-white border border-gray-200 rounded-lg">...</span>
                                @endif
                            @endfor

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
    </div>
</x-app-layout>