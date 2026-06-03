<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengumuman') }}
        </h2>
    </x-slot>

    <div class="py-1">
        <div class="w-full px-2">

            <div class="mb-2 px-1">
                <h3 class="text-lg font-bold text-gray-800">Riwayat Notifikasi</h3>
            </div>

            <div class="bg-white shadow-md rounded-xl overflow-hidden border border-gray-100">

                @forelse($notifikasis as $notifikasi)
                    <div class="px-5 py-1 border-b border-gray-100 last:border-0 transition-all duration-200 flex gap-3 sm:gap-4 relative group
                                {{ $notifikasi->is_read == 0 ? 'bg-blue-50/40' : 'bg-white hover:bg-slate-50' }}">

                        {{-- Garis kiri notif baru --}}
                        @if($notifikasi->is_read == 0)
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 rounded-r-full"></div>
                        @endif

                        {{-- Ikon --}}
                        <div class="flex-shrink-0 mt-0.5">
                            @if($notifikasi->is_read == 0)
                                <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                            @else
                                <div class="w-9 h-9 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center border border-gray-200 group-hover:bg-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7.171-2.653a2 2 0 011.378 0l7.172 2.653c.52.192.89.686.89 1.664V19a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                            @endif
                        </div>

                        {{-- Teks --}}
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-1 gap-1 sm:gap-0">
                                <h3 class="font-semibold text-base {{ $notifikasi->is_read == 0 ? 'text-gray-900' : 'text-gray-700' }}">
                                    {{ $notifikasi->judul }}
                                    @if($notifikasi->is_read == 0)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 uppercase tracking-wider">
                                            Baru
                                        </span>
                                    @endif
                                </h3>
                                <span class="text-[11px] font-medium text-gray-400 whitespace-nowrap flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $notifikasi->created_at->format('d M Y, H:i') }}
                                </span>
                            </div>

                            <p class="text-gray-600 leading-relaxed text-sm">
                                {{ $notifikasi->pesan }}
                            </p>

                            @if(!empty($notifikasi->link))
                                <div class="mt-2.5">
                                    <a href="{{ url($notifikasi->link) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                                        Lihat Detail
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                            <svg class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Belum ada pengumuman</h3>
                        <p class="mt-1 text-sm text-gray-500">Semua riwayat notifikasi akan tersimpan di sini.</p>
                    </div>
                @endforelse

                {{-- Pagination --}}
                @if($notifikasis->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-xs text-gray-500">
                            Menampilkan <span class="font-semibold text-gray-700">{{ $notifikasis->firstItem() }}</span> -
                            <span class="font-semibold text-gray-700">{{ $notifikasis->lastItem() }}</span>
                            dari <span class="font-semibold text-gray-700">{{ $notifikasis->total() }}</span> data
                        </p>

                        <div class="flex items-center gap-1">
                            @if ($notifikasis->onFirstPage())
                                <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&laquo;</span>
                            @else
                                <a href="{{ $notifikasis->previousPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&laquo;</a>
                            @endif

                            @foreach ($notifikasis->getUrlRange(1, $notifikasis->lastPage()) as $page => $url)
                                @if ($page == $notifikasis->currentPage())
                                    <span class="px-3 py-1.5 text-xs text-white bg-green-600 border border-green-600 rounded-lg font-bold shadow-sm">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-green-50 hover:text-green-700 hover:border-green-300 transition">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($notifikasis->hasMorePages())
                                <a href="{{ $notifikasis->nextPageUrl() }}" class="px-3 py-1.5 text-xs text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition">&raquo;</a>
                            @else
                                <span class="px-3 py-1.5 text-xs text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">&raquo;</span>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>