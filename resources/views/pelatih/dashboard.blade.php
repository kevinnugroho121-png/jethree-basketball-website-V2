<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pelatih') }}
        </h2>
    </x-slot>

    {{-- CONTAINER UTAMA: Full Height & No Scroll --}}
    <div class="w-full h-[calc(100vh-70px)] bg-gray-50 p-4 flex flex-col gap-4 overflow-hidden">

        @if(isset($error))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                <p class="font-bold">Error Akun</p>
                <p>{{ $error }}</p>
            </div>
        @else

            {{-- 1. HEADER STATS (3 Kotak) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 h-24 flex-shrink-0">
                
                {{-- Kotak 1: Profil (SUDAH DIPERBAIKI: Text Hitam, Border Biru) --}}
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-blue-600 flex items-center justify-between">
                    <div>
                        {{-- Text jadi abu-abu gelap agar terbaca --}}
                        <p class="text-xs font-bold text-gray-400 uppercase">Login Sebagai</p>
                        {{-- Nama jadi hitam tebal --}}
                        <h3 class="text-xl font-bold text-gray-800 truncate w-40">{{ $pelatih->nama_lengkap }}</h3>
                    </div>
                    <div class="p-2 bg-blue-50 rounded-full text-blue-600">
                        <span class="text-2xl">🏀</span>
                    </div>
                </div>

                {{-- Kotak 2: Jadwal Hari Ini --}}
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-green-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Jadwal Hari Ini</p>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $jadwal_hari_ini->count() }} <span class="text-sm font-normal text-gray-500">Sesi</span></h3>
                    </div>
                    <div class="p-2 bg-green-50 rounded-full text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>

                {{-- Kotak 3: Total Sesi Bulan Ini --}}
                <div class="bg-white rounded-xl p-4 shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase">Total Sesi (Bulan Ini)</p>
                        <h3 class="text-3xl font-bold text-gray-800">{{ $sesi_bulan_ini }}</h3>
                    </div>
                    <div class="p-2 bg-purple-50 rounded-full text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                </div>
            </div>

            {{-- 2. MAIN CONTENT (Tabel & Pengumuman) --}}
            <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-4 min-h-0">
                
                {{-- KOLOM KIRI: TABEL JADWAL (Lebar 2/3) --}}
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                            📅 Jadwal Latihan Mendatang
                        </h4>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full font-bold">Terbaru</span>
                    </div>

                    <div class="flex-1 overflow-y-auto p-0">
                        <table class="w-full text-sm text-left text-gray-500 table-fixed"> 
                            <thead class="text-xs text-gray-700 uppercase bg-white sticky top-0 shadow-sm z-10">
                                <tr>
                                    <th class="px-4 py-3 w-32">Waktu</th>
                                    <th class="px-4 py-3 w-24">Kategori</th>
                                    <th class="px-4 py-3">Lokasi</th> 
                                    <th class="px-4 py-3 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($jadwal_mendatang as $jadwal)
                                    <tr class="hover:bg-blue-50 transition {{ $jadwal->tanggal == date('Y-m-d') ? 'bg-green-50' : 'bg-white' }}">
                                        
                                        {{-- WAKTU --}}
                                        <td class="px-4 py-3 align-middle">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-800 text-xs">
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal)->isoFormat('dddd, D MMM') }}
                                                </span>
                                                <span class="text-[10px] text-blue-600 font-bold mt-0.5">
                                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- KATEGORI --}}
                                        <td class="px-4 py-3 align-middle">
                                            <span class="bg-indigo-100 text-indigo-800 text-[10px] font-bold px-2 py-1 rounded border border-indigo-200">
                                                {{ $jadwal->kategori }}
                                            </span>
                                        </td>

                                        {{-- LOKASI --}}
                                        <td class="px-4 py-3 align-middle">
                                            <p class="truncate w-full text-xs text-gray-600" title="{{ $jadwal->lokasi }}">
                                                {{ $jadwal->lokasi }}
                                            </p>
                                        </td>

                                        {{-- AKSI --}}
                                        <td class="px-4 py-3 text-center align-middle">
                                            <a href="{{ route('pelatih.absensi.create', $jadwal->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-lg font-bold text-[10px] text-white uppercase tracking-widest hover:bg-blue-700 transition shadow-sm">
                                                📝 Absen
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <p>Tidak ada jadwal.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- KOLOM KANAN: PENGUMUMAN --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full overflow-hidden">
                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                        <h4 class="font-bold text-gray-800 flex items-center gap-2">
                            📢 Pengumuman
                        </h4>
                    </div>

                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        @forelse($notifikasis as $notifikasi)
                            <div class="border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                <h5 class="font-bold text-sm text-gray-800 mb-1">{{ $notifikasi->judul }}</h5>
                                <p class="text-xs text-gray-600 leading-relaxed line-clamp-3">{{ $notifikasi->isi }}</p>
                                <span class="text-[10px] text-gray-400 mt-2 block text-right">
                                    {{ $notifikasi->created_at->diffForHumans() }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <span class="text-3xl block mb-2">📭</span>
                                <span class="text-sm">Belum ada info baru.</span>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        @endif

    </div>
</x-app-layout>