<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- UBAH DARI max-w-7xl MENJADI w-full AGAR TAMPILAN FULL SCREEN (MENTOK KANAN KIRI) --}}
        <div class="w-full px-4 sm:px-6 lg:px-8">
            
            {{-- ========================================== --}}
            {{-- 1. TAMPILAN KHUSUS ADMIN (DESAIN BARU - LENGKAP) --}}
            {{-- ========================================== --}}
            @if(Auth::user()->role == 'admin')
                
                {{-- SECTION A: KARTU STATISTIK UTAMA (4 KOLOM) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    {{-- Kartu 1: Total Atlet --}}
                    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-blue-500 hover:shadow-md transition flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Total Atlet Aktif</p>
                            {{-- Menggunakan variabel dari Controller, backup ke 0 jika error --}}
                            <h3 class="text-3xl font-bold text-gray-800">{{ $total_atlet ?? 0 }} <span class="text-sm font-normal text-gray-400">Siswa</span></h3>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>

                    {{-- Kartu 2: Total Pelatih --}}
                    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-purple-500 hover:shadow-md transition flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Total Pelatih</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $total_coach ?? 0 }} <span class="text-sm font-normal text-gray-400">Coach</span></h3>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-full text-purple-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>

                    {{-- Kartu 3: Jadwal Aktif --}}
                    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-orange-500 hover:shadow-md transition flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Jadwal Mendatang</p>
                            <h3 class="text-3xl font-bold text-gray-800">{{ $jadwal_aktif ?? 0 }} <span class="text-sm font-normal text-gray-400">Sesi</span></h3>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-full text-orange-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>

                    {{-- Kartu 4: Pendapatan --}}
                    <div class="bg-white rounded-xl p-6 shadow-sm border-l-4 border-green-500 hover:shadow-md transition flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 mb-1">Kas Masuk (Bln Ini)</p>
                            <h3 class="text-2xl font-bold text-green-600">Rp {{ number_format($total_pendapatan ?? 0, 0, ',', '.') }}</h3>
                        </div>
                        <div class="p-3 bg-green-50 rounded-full text-green-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                {{-- SECTION B: GRAFIK & STATISTIK RINCI --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    
                    {{-- KIRI: DEMOGRAFI GENDER (1/3 Lebar) --}}
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-2">
                            <span>👥</span> Komposisi Atlet
                        </h4>
                        
                        <div class="flex items-center justify-center gap-8 mb-6">
                            {{-- Laki-laki --}}
                            <div class="text-center">
                                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center text-3xl mx-auto mb-2 border-2 border-blue-200">👦</div>
                                <span class="block text-2xl font-bold text-gray-800">{{ $atlet_laki ?? 0 }}</span>
                                <span class="text-xs text-gray-500 font-bold">Laki-laki</span>
                            </div>
                            
                            {{-- Divider --}}
                            <div class="h-12 w-px bg-gray-200"></div>

                            {{-- Perempuan --}}
                            <div class="text-center">
                                <div class="w-16 h-16 bg-pink-50 rounded-full flex items-center justify-center text-3xl mx-auto mb-2 border-2 border-pink-200">👧</div>
                                <span class="block text-2xl font-bold text-gray-800">{{ $atlet_perempuan ?? 0 }}</span>
                                <span class="text-xs text-gray-500 font-bold">Perempuan</span>
                            </div>
                        </div>

                        {{-- Progress Bar Gender --}}
                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden flex shadow-inner">
                            <div class="bg-blue-500 h-4" style="width: {{ $persen_laki ?? 0 }}%"></div>
                            <div class="bg-pink-500 h-4" style="width: {{ $persen_perempuan ?? 0 }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2 font-medium">
                            <span>{{ $persen_laki ?? 0 }}% Putra</span>
                            <span>{{ $persen_perempuan ?? 0 }}% Putri</span>
                        </div>
                    </div>

                    {{-- KANAN: Grafik Kelompok Umur (2/3 Lebar) --}}
                    <div class="bg-white rounded-xl shadow-sm p-6 lg:col-span-2 border border-gray-100">
                        <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2 border-b pb-2">
                            <span>📊</span> Sebaran Kelompok Umur (KU)
                        </h4>
                        
                        <div class="space-y-5">
                            @if(isset($ku_stats))
                                @foreach($ku_stats as $ku => $jumlah)
                                    @php
                                        // Hitung lebar bar (persentase dari total atlet)
                                        $total = ($total_atlet ?? 1) > 0 ? $total_atlet : 1;
                                        $width = ($jumlah / $total) * 100;
                                        $colors = ['KU-10' => 'bg-green-500', 'KU-12' => 'bg-yellow-500', 'KU-14' => 'bg-orange-500', 'KU-16' => 'bg-red-500', 'KU-18' => 'bg-purple-500'];
                                        $barColor = $colors[$ku] ?? 'bg-gray-500';
                                    @endphp
                                    
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 font-bold text-gray-600 text-sm">{{ $ku }}</div>
                                        <div class="flex-grow bg-gray-100 rounded-full h-4 overflow-hidden shadow-inner">
                                            <div class="{{ $barColor }} h-4 rounded-full flex items-center justify-end pr-2 text-[10px] text-white font-bold transition-all duration-500" style="width: {{ $width == 0 ? 2 : $width }}%">
                                            </div> 
                                        </div>
                                        <div class="w-24 text-right text-sm font-bold text-gray-800">{{ $jumlah }} Atlet</div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-center text-gray-400 py-4">Data Kelompok Umur belum tersedia.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- SECTION C: JADWAL HARI INI (FULL WIDTH TABLE) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h4 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span>📅</span> Jadwal Latihan Hari Ini
                        </h4>
                        <a href="{{ route('jadwal.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-bold hover:underline">Lihat Semua Jadwal &rarr;</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3">Waktu</th>
                                    <th class="px-6 py-3">Kategori</th>
                                    <th class="px-6 py-3">Pelatih (Coach)</th>
                                    <th class="px-6 py-3">Lokasi</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwal_hari_ini ?? [] as $jadwal)
                                    <tr class="bg-white border-b hover:bg-blue-50 transition">
                                        <td class="px-6 py-4 font-bold text-gray-800">
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full border border-indigo-200">{{ $jadwal->kategori }}</span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-700">Coach {{ $jadwal->pelatih->nama_lengkap ?? 'Belum ditentukan' }}</td>
                                        <td class="px-6 py-4">{{ $jadwal->lokasi }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full animate-pulse flex items-center justify-center gap-1 w-fit mx-auto">
                                                <span class="w-2 h-2 bg-green-500 rounded-full"></span> Aktif
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 bg-white">
                                            <div class="flex flex-col items-center justify-center opacity-50">
                                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span>Tidak ada jadwal latihan hari ini.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- 2. TAMPILAN KHUSUS PELATIH (TETAP SAMA) --}}
            {{-- ========================================== --}}
            @elseif(Auth::user()->role == 'pelatih')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-600">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-bold text-indigo-900">Halo, Coach {{ Auth::user()->name }}! 🏀</h3>
                                <p class="text-gray-600 mt-1">Berikut adalah jadwal melatih Anda yang akan datang.</p>
                            </div>
                            <div class="text-3xl">📋</div>
                        </div>

                        <hr class="mb-6">

                        @if(isset($jadwal_saya) && $jadwal_saya->count() > 0)
                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full bg-white">
                                    <thead class="bg-indigo-50 text-indigo-900 uppercase text-xs font-bold">
                                        <tr>
                                            <th class="py-3 px-4 text-left">Tanggal</th>
                                            <th class="py-3 px-4 text-left">Waktu</th>
                                            <th class="py-3 px-4 text-left">Kategori</th>
                                            <th class="py-3 px-4 text-left">Lokasi</th>
                                            <th class="py-3 px-4 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm divide-y divide-gray-100">
                                        @foreach($jadwal_saya as $jadwal)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="py-3 px-4 font-bold text-gray-700">{{ \Carbon\Carbon::parse($jadwal->tanggal)->isoFormat('dddd, D MMMM Y') }}</td>
                                                <td class="py-3 px-4"><span class="bg-blue-100 text-blue-800 py-1 px-2 rounded text-xs font-bold">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span></td>
                                                <td class="py-3 px-4">{{ $jadwal->kategori }}</td>
                                                <td class="py-3 px-4 text-gray-500">{{ $jadwal->lokasi }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <a href="{{ route('pelatih.absensi.create', $jadwal->id) }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md text-xs font-bold hover:bg-indigo-700 transition shadow-sm">Isi Absensi</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <span class="text-4xl">📅</span>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada jadwal latihan.</h3>
                            </div>
                        @endif
                    </div>
                </div>

            {{-- ========================================== --}}
            {{-- 3. TAMPILAN KHUSUS ATLET (TETAP SAMA) --}}
            {{-- ========================================== --}}
            @else 
                <div class="space-y-6">
                    {{-- A. Header Sapaan --}}
                    <div class="bg-white shadow-sm sm:rounded-lg border-l-4 border-orange-500 p-6 flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}! 🔥</h3>
                            <p class="text-gray-600">Semangat latihan! Berikut statistik perkembanganmu.</p>
                        </div>
                        <div class="hidden md:block text-5xl">🏀</div>
                    </div>

                    {{-- B. Statistik Singkat --}}
                    @if(isset($absensi_saya))
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-lg shadow text-center">
                                <span class="text-gray-500 text-xs font-bold uppercase">Total Latihan</span>
                                <div class="text-2xl font-bold text-gray-800 mt-1">{{ $absensi_saya->count() }}x</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow text-center border-b-4 border-green-500">
                                <span class="text-gray-500 text-xs font-bold uppercase">Hadir</span>
                                <div class="text-2xl font-bold text-green-600 mt-1">{{ $absensi_saya->where('status', 'Hadir')->count() }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow text-center border-b-4 border-yellow-500">
                                <span class="text-gray-500 text-xs font-bold uppercase">Sakit/Izin</span>
                                <div class="text-2xl font-bold text-yellow-600 mt-1">{{ $absensi_saya->whereIn('status', ['Sakit', 'Izin'])->count() }}</div>
                            </div>
                            <div class="bg-white p-4 rounded-lg shadow text-center border-b-4 border-red-500">
                                <span class="text-gray-500 text-xs font-bold uppercase">Alpha</span>
                                <div class="text-2xl font-bold text-red-600 mt-1">{{ $absensi_saya->where('status', 'Alpha')->count() }}</div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- C. Kolom Kiri: RAPOR NILAI (Lebar 2/3) --}}
                        <div class="lg:col-span-2 bg-white shadow-sm sm:rounded-lg p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-bold text-lg text-indigo-900">📈 Rapor Perkembangan</h3>
                                <span class="text-xs bg-indigo-100 text-indigo-800 py-1 px-2 rounded">Terbaru</span>
                            </div>

                            @if(isset($progres_saya) && $progres_saya->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                                <th class="px-3 py-2 text-center text-xs font-bold text-blue-600 uppercase">Teknik</th>
                                                <th class="px-3 py-2 text-center text-xs font-bold text-green-600 uppercase">Fisik</th>
                                                <th class="px-3 py-2 text-center text-xs font-bold text-yellow-600 uppercase">Mental</th>
                                                <th class="px-3 py-2 text-center text-xs font-bold text-purple-600 uppercase">Taktik</th>
                                                <th class="px-3 py-2 text-left text-xs font-bold text-gray-500 uppercase">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 text-sm">
                                            @foreach($progres_saya as $nilai)
                                                <tr>
                                                    <td class="px-3 py-3 font-medium text-gray-700">{{ \Carbon\Carbon::parse($nilai->tanggal)->format('d/m/y') }}</td>
                                                    <td class="px-3 py-3 text-center font-bold text-blue-800">{{ $nilai->teknik }}</td>
                                                    <td class="px-3 py-3 text-center font-bold text-green-800">{{ $nilai->fisik }}</td>
                                                    <td class="px-3 py-3 text-center font-bold text-yellow-800">{{ $nilai->mental }}</td>
                                                    <td class="px-3 py-3 text-center font-bold text-purple-800">{{ $nilai->taktik }}</td>
                                                    <td class="px-3 py-3 text-xs text-gray-500 italic">"{{ $nilai->catatan ?? '-' }}"</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-10 bg-gray-50 rounded border border-dashed">
                                    <p class="text-gray-500">Belum ada data nilai dari pelatih.</p>
                                </div>
                            @endif
                        </div>

                        {{-- D. Kolom Kanan: RIWAYAT ABSENSI (Lebar 1/3) --}}
                        <div class="bg-white shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-bold text-lg text-gray-800 mb-4">📅 Riwayat Absensi</h3>
                            @if(isset($absensi_saya) && $absensi_saya->count() > 0)
                                <ul role="list" class="divide-y divide-gray-200">
                                    @foreach($absensi_saya as $absen)
                                        <li class="py-3 flex justify-between items-center">
                                            <div class="flex items-center">
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($absen->created_at)->format('d M Y') }}</p>
                                                    <p class="text-xs text-gray-500">{{ $absen->jadwal->lokasi ?? 'Latihan Rutin' }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                @if($absen->status == 'Hadir')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Hadir</span>
                                                @elseif($absen->status == 'Sakit')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Sakit</span>
                                                @elseif($absen->status == 'Izin')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Izin</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Alpha</span>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-500 text-center">Belum ada data absensi.</p>
                            @endif
                        </div>
                    </div>

                    {{-- E. INFO PEMBAYARAN --}}
                    <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-lg text-gray-800">💳 Info Pembayaran</h3>
                                <p class="text-sm text-gray-600">Status Tagihan SPP.</p>
                            </div>
                            @if(isset($tagihan_belum) && $tagihan_belum->count() > 0)
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold">
                                    {{ $tagihan_belum->count() }} Belum Lunas
                                </span>
                            @else
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">Lunas Semua ✅</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>