<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Atlet') }}
            </h2>
            <a href="{{ route('atlet.create') }}" class="inline-flex items-center gap-1 h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                Tambah Atlet
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="w-full px-2">

            {{-- Notifikasi Sukses --}}
            @if (session('success'))
                <div class="mb-3 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 text-sm shadow-sm rounded flex justify-between items-center">
                    <span>✅ <strong>Berhasil!</strong> {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 font-bold px-2 hover:text-green-900">×</button>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-3 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 text-sm shadow-sm rounded flex justify-between items-center">
                    <span>❌ <strong>Gagal!</strong> {{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-700 font-bold px-2 hover:text-red-900">×</button>
                </div>
            @endif

            <div class="bg-white shadow-md rounded-xl overflow-hidden">

                {{-- TOOLBAR FILTER --}}
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('atlet.index') }}" class="flex flex-wrap items-center gap-2">
                        
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="h-9 w-48 sm:w-56 text-sm border border-gray-300 rounded-lg px-3 focus:border-green-500 focus:ring-green-500 shadow-sm placeholder-gray-400"
                            placeholder="Cari Nama Atlet...">

                        <select name="kategori" class="h-9 text-sm border border-gray-300 rounded-lg px-3 bg-white shadow-sm focus:border-green-500 focus:ring-green-500 cursor-pointer">
                            <option value="">- Kategori -</option>
                            <option value="KU-10" {{ request('kategori') == 'KU-10' ? 'selected' : '' }}>KU-10</option>
                            <option value="KU-12" {{ request('kategori') == 'KU-12' ? 'selected' : '' }}>KU-12</option>
                            <option value="KU-14" {{ request('kategori') == 'KU-14' ? 'selected' : '' }}>KU-14</option>
                            <option value="KU-16" {{ request('kategori') == 'KU-16' ? 'selected' : '' }}>KU-16</option>
                            <option value="KU-18" {{ request('kategori') == 'KU-18' ? 'selected' : '' }}>KU-18</option>
                        </select>

                        <select name="status" class="h-9 text-sm border border-gray-300 rounded-lg px-3 bg-white shadow-sm focus:border-green-500 focus:ring-green-500 cursor-pointer">
                            <option value="">- Status -</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                            <option value="Keluar" {{ request('status') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                        </select>

                        <button type="submit" class="h-9 px-4 bg-gray-700 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition shadow-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            CARI
                        </button>

                        <a href="{{ route('atlet.index') }}" class="h-9 px-4 bg-white border border-red-300 text-red-600 text-xs font-bold rounded-lg hover:bg-red-50 transition shadow-sm flex items-center">
                            RESET
                        </a>
                    </form>
                </div>

                {{-- TABEL --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="px-2 py-1 text-center font-semibold w-10">No</th>
                                <th class="px-2 py-1 text-center font-semibold w-14">Foto</th>
                                <th class="px-2 py-1 font-semibold min-w-[180px]">Nama Lengkap</th>
                                <th class="px-2 py-1 text-center font-semibold w-20">Umur</th>
                                <th class="px-2 py-1 text-center font-semibold w-24">Kategori</th>
                                <th class="px-2 py-1 font-semibold min-w-[140px]">Sekolah</th>
                                <th class="px-2 py-1 font-semibold w-36">No. HP (WA)</th>
                                <th class="px-2 py-1 text-center font-semibold w-24">Status</th>
                                <th class="px-2 py-1 text-center font-semibold w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($atlets as $index => $atlet)
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
                                    
                                    <td class="px-3 py-2 text-center text-gray-500 font-medium">
                                        {{ $atlets->firstItem() + $index }}
                                    </td>

                                    <td class="px-2 py-2 text-center">
                                        @if ($atlet->foto_profil)
                                            <img src="{{ asset('storage/' . $atlet->foto_profil) }}" alt="Foto" class="h-10 w-10 object-cover rounded-lg mx-auto border border-gray-200 shadow-sm">
                                        @else
                                            <div class="h-10 w-10 bg-gray-100 rounded-lg mx-auto border border-gray-200 flex items-center justify-center text-gray-400 text-[10px]">N/A</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2 font-semibold text-gray-900">
                                        {{ $atlet->nama_lengkap }}
                                        @if($atlet->nama_panggilan)
                                            <span class="text-gray-400 font-normal text-xs ml-1">({{ $atlet->nama_panggilan }})</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        @php $umur = \Carbon\Carbon::parse($atlet->tanggal_lahir)->age; @endphp
                                        <span class="font-bold text-blue-700">{{ $umur }} Thn</span>
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <span class="inline-block {{ $warnaKU['bg'] }} {{ $warnaKU['text'] }} text-xs font-bold px-2 py-1 rounded">
                                            {{ $atlet->kategori ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-2 text-gray-600 text-xs">
                                        <span class="bg-gray-100 px-1 py-0.5 rounded border text-[10px] mr-1">{{ $atlet->jenjang_sekolah }}</span>
                                        {{ $atlet->nama_sekolah ?? '-' }}
                                    </td>

                                    <td class="px-4 py-2 font-mono text-xs text-gray-700">
                                        {{ $atlet->no_hp_atlet ?? '-' }}
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        @if($atlet->status == 'Aktif')
                                            <span class="text-green-700 font-bold bg-green-50 px-2 py-1 rounded border border-green-200 text-[10px]">AKTIF</span>
                                        @else
                                            <span class="text-red-700 font-bold bg-red-50 px-2 py-1 rounded border border-red-200 text-[10px] uppercase">{{ $atlet->status }}</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <div class="flex justify-center items-center gap-1">
                                            {{-- Detail --}}
                                            <a href="{{ route('atlet.show', $atlet->id) }}" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg border border-blue-200 transition shadow-sm" title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            {{-- Edit --}}
                                            <a href="{{ route('atlet.edit', $atlet->id) }}" class="p-1.5 bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-lg border border-yellow-200 transition shadow-sm" title="Edit Data">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            {{-- PDF --}}
                                            <a href="{{ route('atlet.pdf', $atlet->id) }}" class="p-1.5 bg-gray-50 text-gray-600 hover:bg-gray-600 hover:text-white rounded-lg border border-gray-200 transition shadow-sm" title="Cetak PDF">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </a>
                                            
                                            {{-- Kirim Rapor WA (Fonnte) --}}
                                            <form action="{{ route('atlet.kirim-rapor', $atlet->id) }}" method="POST" onsubmit="return confirm('Kirim Rapor PDF ini ke WhatsApp Orang Tua?');" class="inline-block">
                                                @csrf
                                                <button type="submit" class="p-1.5 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg border border-green-200 transition shadow-sm" title="Kirim Rapor ke WA">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                                                </button>
                                            </form>

                                            {{-- Hapus --}}
                                            <form action="{{ route('atlet.destroy', $atlet->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus atlet ini?');" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg border border-red-200 transition shadow-sm" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-10 text-center text-gray-400">
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

                {{-- FOOTER PAGINATION (Sistem Sliding Window) --}}
                <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">{{ $atlets->firstItem() ?? 0 }}</span> -
                        <span class="font-semibold text-gray-700">{{ $atlets->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold text-gray-700">{{ $atlets->total() }}</span> data
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