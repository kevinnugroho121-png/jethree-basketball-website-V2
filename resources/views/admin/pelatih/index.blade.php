<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Data Pelatih (Coach)') }}
            </h2>
            <a href="{{ route('pelatih.create') }}" class="inline-flex items-center gap-1 h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                Tambah Coach
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

            <div class="bg-white shadow-md rounded-xl overflow-hidden">

                {{-- TOOLBAR FILTER --}}
                <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                    <form method="GET" action="{{ route('pelatih.index') }}" class="flex flex-wrap items-center gap-2">

                        <input type="text" name="search" value="{{ request('search') }}"
                            class="h-9 w-48 sm:w-56 text-sm border border-gray-300 rounded-lg px-3 focus:border-green-500 focus:ring-green-500 shadow-sm placeholder-gray-400"
                            placeholder="Cari Nama Coach...">

                        <select name="status" class="h-9 text-sm border border-gray-300 rounded-lg px-3 bg-white shadow-sm focus:border-green-500 focus:ring-green-500 cursor-pointer">
                            <option value="">- Status -</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Non-Aktif" {{ request('status') == 'Non-Aktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>

                        <button type="submit" class="h-9 px-4 bg-gray-700 text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition shadow-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            CARI
                        </button>

                        <a href="{{ route('pelatih.index') }}" class="h-9 px-4 bg-white border border-red-300 text-red-600 text-xs font-bold rounded-lg hover:bg-red-50 transition shadow-sm flex items-center">
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
                                <th class="px-4 py-3 font-semibold min-w-[180px]">Nama Coach</th>
                                <th class="px-4 py-3 font-semibold min-w-[180px]">Email Login</th>
                                <th class="px-4 py-3 font-semibold w-36">No. HP (WA)</th>
                                <th class="px-4 py-3 font-semibold w-36">Lisensi</th>
                                <th class="px-3 py-3 text-center font-semibold w-24">Status</th>
                                <th class="px-3 py-3 text-center font-semibold w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pelatihs as $index => $pelatih)
                                <tr class="hover:bg-green-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">

                                    <td class="px-3 py-2 text-center text-gray-500 font-medium">
                                        {{ $pelatihs->firstItem() + $index }}
                                    </td>

                                    <td class="px-4 py-2 font-semibold text-gray-900">
                                        {{ $pelatih->nama_lengkap }}
                                        <div class="text-[10px] text-gray-400 font-normal mt-0.5">
                                            Lahir: {{ \Carbon\Carbon::parse($pelatih->tanggal_lahir)->format('d-m-Y') }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-2 font-mono text-xs text-gray-600">
                                        {{ $pelatih->user->email ?? '-' }}
                                    </td>

                                    <td class="px-4 py-2 font-mono text-xs text-gray-700">
                                        {{ $pelatih->no_hp ?? '-' }}
                                    </td>

                                    <td class="px-4 py-2 text-xs">
                                        @if($pelatih->lisensi)
                                            <span class="bg-blue-100 text-blue-800 text-[10px] font-semibold px-2 py-1 rounded">{{ $pelatih->lisensi }}</span>
                                        @else
                                            <span class="text-gray-400 italic">-</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        @if($pelatih->status == 'Aktif')
                                            <span class="text-green-700 font-bold bg-green-50 px-2 py-1 rounded border border-green-200 text-[10px]">AKTIF</span>
                                        @else
                                            <span class="text-red-700 font-bold bg-red-50 px-2 py-1 rounded border border-red-200 text-[10px] uppercase">{{ $pelatih->status }}</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <div class="flex justify-center items-center gap-1">
                                            {{-- Detail --}}
                                            <a href="{{ route('pelatih.show', $pelatih->id) }}" class="p-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg border border-blue-200 transition shadow-sm" title="Lihat Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </a>
                                            {{-- Edit --}}
                                            <a href="{{ route('pelatih.edit', $pelatih->id) }}" class="p-1.5 bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-lg border border-yellow-200 transition shadow-sm" title="Edit Data">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </a>
                                            {{-- Hapus --}}
                                            <form action="{{ route('pelatih.destroy', $pelatih->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus coach ini? Akun login juga akan terhapus.');" class="inline-block">
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
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">📭</span>
                                            <p class="font-medium">Data pelatih tidak ditemukan.</p>
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
                        Menampilkan <span class="font-semibold text-gray-700">{{ $pelatihs->firstItem() }}</span> -
                        <span class="font-semibold text-gray-700">{{ $pelatihs->lastItem() }}</span>
                        dari <span class="font-semibold text-gray-700">{{ $pelatihs->total() }}</span> data
                    </p>

                    <div class="flex items-center gap-1">
                        @if ($pelatihs->onFirstPage())
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&laquo;</span>
                        @else
                            <a href="{{ $pelatihs->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&laquo;</a>
                        @endif

                        @foreach ($pelatihs->appends(request()->query())->getUrlRange(1, $pelatihs->lastPage()) as $page => $url)
                            @if ($page == $pelatihs->currentPage())
                                <span class="px-3 py-1.5 text-xs text-white bg-green-600 border border-green-600 rounded-lg font-bold shadow-sm">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($pelatihs->hasMorePages())
                            <a href="{{ $pelatihs->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                        @else
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>