<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kotak Masuk Pengumuman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-bold mb-4">Riwayat Pesan & Notifikasi</h3>

                    <div class="space-y-4">
                        @forelse($notifikasis as $notif)
                            {{-- Card Notifikasi --}}
                            <div class="border rounded-lg p-4 flex items-start gap-4 transition hover:bg-gray-50 {{ $notif->is_read ? 'bg-white' : 'bg-blue-50 border-blue-200' }}">
                                
                                {{-- Ikon Berdasarkan Tipe --}}
                                <div class="shrink-0 mt-1">
                                    @if($notif->tipe == 'tagihan')
                                        <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                            💰
                                        </div>
                                    @elseif($notif->tipe == 'sukses')
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                            ✅
                                        </div>
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            📢
                                        </div>
                                    @endif
                                </div>

                                {{-- Konten Pesan --}}
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-gray-800">{{ $notif->judul }}</h4>
                                        <span class="text-xs text-gray-500">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-600 text-sm mt-1">{{ $notif->pesan }}</p>
                                    
                                    @if($notif->link)
                                        <a href="{{ $notif->link }}" class="inline-block mt-2 text-sm text-blue-600 hover:underline font-semibold">
                                            Lihat Detail →
                                        </a>
                                    @endif
                                </div>
                                
                                {{-- Status Baca --}}
                                <div>
                                    @if(!$notif->is_read)
                                        <span class="h-3 w-3 bg-red-500 rounded-full block" title="Belum Dibaca"></span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-gray-500">
                                <p>Belum ada notifikasi masuk.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $notifikasis->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>