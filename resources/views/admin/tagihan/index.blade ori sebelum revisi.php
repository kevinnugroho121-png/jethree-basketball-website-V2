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

                {{-- TOOLBAR FILTER --}}
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('tagihan.index') }}" class="flex flex-wrap items-center gap-2">

                        <div class="flex rounded-lg shadow-sm">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="block w-36 sm:w-48 h-9 rounded-l-lg border border-gray-300 focus:border-green-500 focus:ring-green-500 text-xs px-3"
                                placeholder="Cari atlet...">
                            <button type="submit" class="bg-white border border-l-0 border-gray-300 h-9 px-2 rounded-r-lg hover:bg-gray-100 text-gray-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>

                        <select name="bulan" class="h-9 rounded-lg border border-gray-300 text-xs focus:ring-green-500 focus:border-green-500 bg-white cursor-pointer px-2 min-w-[100px]">
                            <option value="">- Bulan -</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endforeach
                        </select>

                        <select name="tahun" class="h-9 rounded-lg border border-gray-300 text-xs focus:ring-green-500 focus:border-green-500 bg-white cursor-pointer px-2 w-20">
                            <option value="">- Thn -</option>
                            @foreach(range(date('Y'), 2024) as $y)
                                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>

                        <select name="status" class="h-9 rounded-lg border border-gray-300 text-xs focus:ring-green-500 focus:border-green-500 bg-white cursor-pointer px-2 w-28">
                            <option value="">- Status -</option>
                            <option value="Belum Lunas" {{ request('status') == 'Belum Lunas' ? 'selected' : '' }}>❌ Belum</option>
                            <option value="Menunggu Verifikasi" {{ request('status') == 'Menunggu Verifikasi' ? 'selected' : '' }}>⏳ Diproses</option>
                            <option value="Lunas" {{ request('status') == 'Lunas' ? 'selected' : '' }}>✅ Lunas</option>
                        </select>

                        <button type="submit" class="h-9 px-4 bg-gray-800 hover:bg-gray-900 text-white text-xs font-bold rounded-lg shadow-sm transition">
                            FILTER
                        </button>

                        <a href="{{ route('tagihan.preview', request()->query()) }}" class="h-9 px-4 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Preview PDF
                        </a>

                    </form>
                </div>

                {{-- TABEL --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="px-3 py-3 text-center font-semibold w-10">No</th>
                                <th class="px-3 py-3 font-semibold w-24">Tanggal</th>
                                <th class="px-4 py-3 font-semibold min-w-[150px]">Nama Atlet</th>
                                <th class="px-4 py-3 font-semibold">Jenis Tagihan</th>
                                <th class="px-4 py-3 font-semibold text-right w-32">Nominal</th>
                                <th class="px-3 py-3 font-semibold text-center w-28">Status</th>
                                <th class="px-3 py-3 font-semibold text-center w-44">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tagihans as $index => $tagihan)
                                @php
                                    $no_hp = preg_replace('/^0/', '62', $tagihan->atlet->no_hp ?? '0');

                                    $pesanTagih = "Halo " . ($tagihan->atlet->nama_lengkap ?? 'Atlet') . ",\n\n" .
                                                  "Kami mengingatkan tagihan *" . ($tagihan->judul_tagihan ?? 'SPP') . "* sebesar *Rp " . number_format($tagihan->nominal, 0, ',', '.') . "* statusnya BELUM LUNAS.\n" .
                                                  "Mohon segera melakukan pembayaran ya. Terima kasih! 🙏🏀";

                                    $pesanLunas = "Halo " . ($tagihan->atlet->nama_lengkap ?? 'Atlet') . ",\n\n" .
                                                  "Terima kasih! Pembayaran *" . ($tagihan->judul_tagihan ?? 'SPP') . "* sebesar *Rp " . number_format($tagihan->nominal, 0, ',', '.') . "* telah kami terima (LUNAS).\n" .
                                                  "Semangat latihannya! 💪🔥";
                                @endphp
                                <tr class="hover:bg-green-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">

                                    <td class="px-3 py-2 text-center text-gray-500 font-medium">
                                        {{ $tagihans->firstItem() + $index }}
                                    </td>

                                    <td class="px-3 py-2 text-gray-600 text-xs">
                                        {{ \Carbon\Carbon::parse($tagihan->tanggal_tagihan)->format('d/m/Y') }}
                                    </td>

                                    <td class="px-4 py-2 font-semibold text-gray-800">
                                        {{ $tagihan->atlet->nama_lengkap ?? 'Atlet Terhapus' }}
                                        <div class="text-[10px] text-gray-400 font-normal">{{ $tagihan->atlet->kategori_umur ?? '-' }}</div>
                                    </td>

                                    <td class="px-4 py-2 text-gray-700">
                                        {{ $tagihan->judul_tagihan }}
                                        <div class="text-[10px] text-gray-400 italic">{{ $tagihan->metode_pembayaran ?? '-' }}</div>
                                    </td>

                                    <td class="px-4 py-2 text-right font-mono font-bold text-gray-900">
                                        Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        @if($tagihan->status == 'Lunas')
                                            <span class="inline-block text-[10px] font-bold px-2 py-1 rounded bg-green-100 text-green-800 border border-green-200">LUNAS</span>
                                            <div class="text-[9px] text-green-600 mt-0.5">
                                                {{ $tagihan->tanggal_lunas ? \Carbon\Carbon::parse($tagihan->tanggal_lunas)->format('d/m/y') : '' }}
                                            </div>
                                        @elseif($tagihan->status == 'Menunggu Verifikasi')
                                            {{-- INI TAMBAHANNYA: Biar warnanya kuning pas lagi diproses --}}
                                            <span class="inline-block text-[10px] font-bold px-2 py-1 rounded bg-yellow-100 text-yellow-800 border border-yellow-200">DIPROSES</span>
                                        @else
                                            <span class="inline-block text-[10px] font-bold px-2 py-1 rounded bg-red-100 text-red-800 border border-red-200">BELUM LUNAS</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <div class="flex justify-center items-center gap-1.5 flex-wrap">

                                            {{-- Tombol WA --}}
                                            @if($tagihan->status == 'Belum Lunas')
                                                <a href="https://wa.me/{{ $no_hp }}?text={{ urlencode($pesanTagih) }}" target="_blank"
                                                   class="flex items-center justify-center w-7 h-7 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-sm" title="Kirim Tagihan via WA">
                                                    📱
                                                </a>
                                            @else
                                                <a href="https://wa.me/{{ $no_hp }}?text={{ urlencode($pesanLunas) }}" target="_blank"
                                                   class="flex items-center justify-center w-7 h-7 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-sm" title="Kirim Bukti Lunas via WA">
                                                    📱
                                                </a>
                                            @endif

                                            {{-- Tombol Cek Bukti / Edit --}}
                                            @if($tagihan->bukti_pembayaran)
                                                {{-- Tombol Cek Bukti (Selalu Muncul Jika Ada Bukti) --}}
                                                <a href="{{ route('tagihan.bukti', $tagihan->id) }}"
                                                   class="flex items-center px-2 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 text-[10px] font-bold transition">
                                                    📄 Cek
                                                </a>

                                                {{-- Tombol Verifikasi OK (Hanya Muncul Jika Belum Lunas) --}}
                                                @if($tagihan->status != 'Lunas')
                                                    <form action="{{ route('tagihan.verifikasi_lunas', $tagihan->id) }}" method="POST"
                                                          onsubmit="return confirm('Yakin ingin memverifikasi tagihan ini menjadi LUNAS?');" style="display:inline-block;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit"
                                                            class="flex items-center px-2 py-1 bg-green-50 text-green-700 border border-green-200 rounded-lg hover:bg-green-100 text-[10px] font-bold transition">
                                                            ✅ OK
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                {{-- Jika tidak ada bukti / bayar manual --}}
                                                <a href="{{ route('tagihan.edit', $tagihan->id) }}"
                                                   class="flex items-center justify-center w-7 h-7 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-lg border border-yellow-300" title="Edit / Bayar Manual">
                                                    ✏️
                                                </a>
                                            @endif

                                            {{-- Hapus --}}
                                            <form action="{{ route('tagihan.destroy', $tagihan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus tagihan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-center w-7 h-7 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg border border-red-300" title="Hapus">
                                                    ✕
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">📭</span>
                                            <p class="font-medium">Data tagihan tidak ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- FOOTER PAGINATION (Sistem Sliding Window) --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">{{ $tagihans->firstItem() ?? 0 }}</span> -
                        <span class="font-semibold text-gray-700">{{ $tagihans->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold text-gray-700">{{ $tagihans->total() }}</span> data
                    </p>

                    <div class="flex items-center gap-1">
                        {{-- Prev --}}
                        @if ($tagihans->onFirstPage())
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&laquo;</span>
                        @else
                            <a href="{{ $tagihans->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&laquo;</a>
                        @endif

                        {{-- Logika Pembatasan Angka Halaman (Sliding Window) --}}
                        @php
                            $currentPage = $tagihans->currentPage();
                            $lastPage = $tagihans->lastPage();
                            $startPage = max($currentPage - 2, 1);
                            $endPage = min($currentPage + 2, $lastPage);
                        @endphp

                        @if ($startPage > 1)
                            <a href="{{ $tagihans->appends(request()->query())->url(1) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">1</a>
                            @if ($startPage > 2)
                                <span class="px-3 py-1.5 text-xs text-gray-400">...</span>
                            @endif
                        @endif

                        @foreach ($tagihans->appends(request()->query())->getUrlRange($startPage, $endPage) as $page => $url)
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
                            <a href="{{ $tagihans->appends(request()->query())->url($lastPage) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $lastPage }}</a>
                        @endif

                        {{-- Next --}}
                        @if ($tagihans->hasMorePages())
                            <a href="{{ $tagihans->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                        @else
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>