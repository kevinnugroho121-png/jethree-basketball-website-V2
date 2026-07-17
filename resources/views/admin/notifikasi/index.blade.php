<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Riwayat Pengumuman (Broadcast)') }}
            </h2>
            <a href="{{ route('notifikasi.create') }}" class="inline-flex items-center gap-1 h-9 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                Buat Pengumuman Baru
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

                {{-- TABEL --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="px-3 py-3 text-center font-semibold w-10">No</th>
                                <th class="px-4 py-3 font-semibold w-40">Tanggal & Waktu</th>
                                <th class="px-4 py-3 font-semibold w-40">Pengirim</th>
                                
                                {{-- 💡 TAMBAHAN BARU: Header Pengirim --}}
                                <th class="px-4 py-3 font-semibold w-44">Penerima</th>
                                <th class="px-4 py-3 font-semibold min-w-[250px]">Judul & Isi Pesan</th>
                                <th class="px-3 py-3 font-semibold text-center w-32">Status</th>
                                <th class="px-3 py-3 font-semibold text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($notifikasis as $index => $notif)
                                <tr class="hover:bg-green-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">

                                    <td class="px-3 py-2 text-center text-gray-500 font-medium">
                                        {{ $notifikasis->firstItem() + $index }}
                                    </td>

                                    <td class="px-4 py-2 text-xs font-mono text-gray-600">
                                        {{ $notif->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    

                                    {{-- 💡 TAMBAHAN BARU: Kolom Data Pengirim Notifikasi --}}
                                    <td class="px-4 py-2">
                                        @if($notif->sender)
                                            <div class="font-semibold text-gray-900">{{ $notif->sender->name }}</div>
                                            <div class="text-[10px] text-emerald-600 font-bold uppercase mt-0.5">
                                                Role: {{ $notif->sender->role }}
                                            </div>
                                        @else
                                            {{-- Fallback jika notifikasi otomatis dari sistem (seperti tagihan baru) --}}
                                            <div class="font-semibold text-gray-400 italic">Sistem Auto</div>
                                            <div class="text-[10px] text-gray-400 font-bold uppercase mt-0.5">
                                                Role: System
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2">
                                        {{-- 💡 PERBAIKAN: Jika user_id bernilai null, tampilkan sebagai Global Broadcast --}}
                                        @if(is_null($notif->user_id))
                                            <div class="font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded text-xs inline-block">
                                                Semua User
                                            </div>
                                            <div class="text-[10px] text-gray-500 font-bold uppercase mt-1">
                                                Role: Semua Role
                                            </div>
                                        @else
                                            <div class="font-semibold text-gray-900">{{ $notif->user->name ?? 'User Terhapus' }}</div>
                                            <div class="text-[10px] text-blue-600 font-semibold uppercase mt-0.5">
                                                Role: {{ $notif->user->role ?? '-' }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2">
                                        <div class="font-bold text-blue-800 mb-0.5">{{ $notif->judul }}</div>
                                        <div class="text-xs text-gray-500 italic">
                                            {{ Str::limit($notif->pesan, 100) }}
                                        </div>
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        @if($notif->is_read)
                                            <span class="inline-block text-green-700 font-bold bg-green-100 px-2 py-1 rounded border border-green-200 text-[10px] uppercase whitespace-nowrap">✅ Sudah Dibaca</span>
                                        @else
                                            <span class="inline-block text-amber-700 font-bold bg-amber-100 px-2 py-1 rounded border border-amber-200 text-[10px] uppercase whitespace-nowrap">⏳ Belum Dibaca</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <form action="{{ route('notifikasi.destroy', $notif->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus riwayat pengumuman ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 bg-red-50 text-red-600 px-2 py-1.5 rounded-lg border border-red-200 hover:bg-red-600 hover:text-white transition text-[10px] font-bold shadow-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                HAPUS
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-400">
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">📭</span>
                                            <p class="font-medium">Belum ada riwayat pengumuman terkirim.</p>
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
                        Menampilkan <span class="font-semibold text-gray-700">{{ $notifikasis->firstItem() ?? 0 }}</span> -
                        <span class="font-semibold text-gray-700">{{ $notifikasis->lastItem() ?? 0 }}</span>
                        dari <span class="font-semibold text-gray-700">{{ $notifikasis->total() }}</span> data
                    </p>

                    <div class="flex items-center gap-1">
                        {{-- Prev --}}
                        @if ($notifikasis->onFirstPage())
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&laquo;</span>
                        @else
                            <a href="{{ $notifikasis->appends(request()->query())->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&laquo;</a>
                        @endif

                        {{-- Logika Pembatasan Angka Halaman (Sliding Window) --}}
                        @php
                            $currentPage = $notifikasis->currentPage();
                            $lastPage = $notifikasis->lastPage();
                            $startPage = max($currentPage - 2, 1);
                            $endPage = min($currentPage + 2, $lastPage);
                        @endphp

                        @if ($startPage > 1)
                            <a href="{{ $notifikasis->appends(request()->query())->url(1) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">1</a>
                            @if ($startPage > 2)
                                <span class="px-3 py-1.5 text-xs text-gray-400">...</span>
                            @endif
                        @endif

                        @foreach ($notifikasis->appends(request()->query())->getUrlRange($startPage, $endPage) as $page => $url)
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
                            <a href="{{ $notifikasis->appends(request()->query())->url($lastPage) }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $lastPage }}</a>
                        @endif

                        {{-- Next --}}
                        @if ($notifikasis->hasMorePages())
                            <a href="{{ $notifikasis->appends(request()->query())->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                        @else
                            <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>