<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Overview') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- BARIS 1: KARTU STATISTIK UTAMA (Gaya Premium Modern ala Apex) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                
                {{-- Card 1: Total Atlet (Aktif) --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-1">Total Atlet (Aktif)</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalAtlet }} Orang</div>
                    </div>
                    <div class="p-3.5 bg-blue-50 rounded-xl text-blue-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>

                {{-- Card 2: Atlet Kurang Aktif --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-1">Atlet Kurang Aktif</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $atletKurangAktif }} Orang</div>
                    </div>
                    <div class="p-3.5 bg-orange-50 rounded-xl text-orange-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                    </div>
                </div>

                {{-- Card 3: Atlet Telat Materi --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-1">Atlet Telat Materi</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $atletTelatMateri }} Orang</div>
                    </div>
                    <div class="p-3.5 bg-red-50 rounded-xl text-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>

                {{-- Card 4: Total Pelatih --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-1">Total Pelatih</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalPelatih }} Orang</div>
                    </div>
                    <div class="p-3.5 bg-green-50 rounded-xl text-green-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>

                {{-- Card 5: Jadwal Mendatang --}}
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-1">Jadwal Mendatang</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $jadwalMendatang }} Sesi</div>
                    </div>
                    <div class="p-3.5 bg-indigo-50 rounded-xl text-indigo-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                {{-- Card 6: Pendapatan (BISA DIKLIK) --}}
                <a href="{{ route('tagihan.index') }}" class="block bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between transition duration-300 hover:shadow-md hover:border-emerald-200 cursor-pointer group">
                    <div>
                        <div class="text-xs font-semibold text-gray-400 tracking-wider uppercase mb-1">Pendapatan (Bulan Ini)</div>
                        <div class="text-2xl font-bold text-gray-900 group-hover:text-emerald-600 transition">{{ 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3.5 bg-emerald-50 rounded-xl text-emerald-500 transition group-hover:bg-emerald-500 group-hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </div>
                </a>
            </div>

            {{-- BARIS 2 & 3: KARTU GRAFIK (Gaya Premium Modern ala Apex) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                
                {{-- KARTU GRAFIK: GENDER --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">
                    <div class="mb-5">
                        <h6 class="font-bold text-base text-gray-950">Statistik Gender Atlet</h6>
                    </div>
                    <div class="flex flex-col items-center flex-1 justify-center">
                        <div class="relative h-64 w-full flex justify-center">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <div class="mt-6 text-center text-xs font-medium text-gray-500 flex justify-center items-center gap-4">
                            <span class="flex items-center"><i class="inline-block w-2.5 h-2.5 bg-blue-500 rounded-full mr-1.5"></i> Laki-laki: <strong class="ml-1 text-gray-800">{{ $cowok }}</strong></span>
                            <span class="flex items-center"><i class="inline-block w-2.5 h-2.5 bg-pink-500 rounded-full mr-1.5"></i> Perempuan: <strong class="ml-1 text-gray-800">{{ $cewek }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- KARTU GRAFIK: KELOMPOK UMUR --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="mb-5">
                        <h6 class="font-bold text-base text-gray-950">Sebaran Kelompok Umur (KU)</h6>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="kuChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- BARIS 3: GRAFIK PENDAPATAN --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
                <div class="mb-5">
                    <h6 class="font-bold text-base text-gray-950">Tren Pendapatan (6 Bulan Terakhir)</h6>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>

            {{-- 🟢 SUNTIKAN BARU: GRAFIK KEAKTIFAN & BREAKDOWN PELATIH (KHUSUS ADMIN) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
                {{-- Header + Multi Filter Form --}}
                <div class="border-b border-gray-100 pb-4 mb-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h6 class="font-bold text-base text-gray-950">📊 Tren Keaktifan Mengajar Pelatih</h6>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">Berdasarkan akumulasi presensi sesi latihan yang telah terlaksana</p>
                    </div>
                    
                    {{-- Form Multi-Filter Dinamis --}}
                    <form action="{{ url()->current() }}" method="GET" class="flex flex-wrap gap-2 items-center w-full sm:w-auto">
                        {{-- Filter Bulan & Tahun --}}
                        <input type="month" name="periode" value="{{ $periode }}" onchange="this.form.submit()"
                               class="text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-emerald-500 focus:border-emerald-500 text-gray-600 bg-white cursor-pointer font-semibold shadow-sm">
                        
                        {{-- Filter Opsi Nama Pelatih --}}
                        <select name="pelatih_id" onchange="this.form.submit()" 
                                class="text-xs border border-gray-300 rounded-lg px-2.5 py-1.5 focus:ring-emerald-500 focus:border-emerald-500 text-gray-600 bg-white cursor-pointer font-semibold shadow-sm">
                            <option value="">🌍 Semua Pelatih & Takeover</option>
                            @foreach($list_pelatih as $p)
                                <option value="{{ $p->id }}" {{ request('pelatih_id') == $p->id ? 'selected' : '' }}>
                                    🏀 {{ $p->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>

                        @if(request()->has('periode') || request()->has('pelatih_id'))
                            <a href="{{ url()->current() }}" class="bg-red-50 text-red-600 border border-red-100 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-red-100 transition">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                {{-- Canvas Chart Batang --}}
                <div class="relative h-64 w-full mb-6">
                    <canvas id="coachAttendanceChart"></canvas>
                </div>

                {{-- Tabel Rincian Kontribusi di Bawah Grafik --}}
                <div class="border-t border-gray-100 pt-4">
                    <span class="text-xs font-bold text-gray-400 tracking-wider uppercase block mb-3">📋 Rincian Kontribusi Pelatih (Periode Terfilter)</span>
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full text-sm text-left text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider text-xs">
                                <tr>
                                    <th class="px-6 py-3">Nama Pelatih</th>
                                    <th class="px-6 py-3 text-center">Sesi Terlaksana</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($coach_breakdown as $cb)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-3 font-semibold text-gray-800">{{ $cb->nama_lengkap }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-emerald-600">{{ $cb->total_mengajar }} Sesi</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-6 py-6 text-center text-gray-400 italic">
                                            Tidak ada aktivitas mengajar pelatih yang selesai pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- BARIS 4: TABEL JADWAL (Gaya Premium Modern ala Apex) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 pb-4 border-b border-gray-50 flex justify-between items-center">
                    <h6 class="font-bold text-base text-gray-950">📅 Jadwal Latihan Terdekat</h6>
                    <a href="{{ route('jadwal.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 transition">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-50/70 text-gray-400 font-semibold text-xs tracking-wider uppercase border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-3.5">Tanggal & Jam</th>
                                <th class="px-6 py-3.5">Kategori</th>
                                <th class="px-6 py-3.5">Materi</th>
                                <th class="px-6 py-3.5">Pelatih</th>
                                <th class="px-6 py-3.5">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($upcomingSchedules as $jadwal)
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }}</div>
                                        <div class="text-xs text-emerald-600 font-semibold mt-0.5">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-lg text-xs font-semibold tracking-wide">{{ $jadwal->kategori }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 truncate max-w-xs">{{ $jadwal->materi ?? '-' }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-800">{{ $jadwal->pelatih->nama_lengkap ?? 'Admin' }}</td>
                                    <td class="px-6 py-4 text-gray-400 font-medium">{{ $jadwal->lokasi }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic bg-white">
                                        Tidak ada jadwal latihan dalam waktu dekat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT KHUSUS CHART (Sinkronisasi Palet Warna Premium) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. DATA GENDER (Doughnut - Warna Lebih Lembut Modern)
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $cowok }}, {{ $cewek }}],
                    backgroundColor: ['#0ea5e9', '#f43f5e'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '75%'
            }
        });

        // 2. DATA KELOMPOK UMUR (Bar - Emerald Green dengan Sudut Melengkung)
        new Chart(document.getElementById('kuChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($kuLabels) !!},
                datasets: [{
                    label: "Jumlah Atlet",
                    backgroundColor: "#10b981",
                    hoverBackgroundColor: "#059669",
                    data: {!! json_encode($kuValues) !!},
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1, color: '#9ca3af' },
                        grid: { color: '#f3f4f6' }
                    }
                }
            }
        });

        // 3. DATA PENDAPATAN (Line Chart - Gradasi Clean)
        new Chart(document.getElementById('incomeChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($incomeLabels) !!},
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($incomeValues) !!},
                    borderColor: '#10b981', 
                    backgroundColor: 'rgba(16, 185, 129, 0.04)',
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981',
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.35 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            color: '#9ca3af',
                            callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }
                        },
                        grid: { color: '#f3f4f6' }
                    }
                }
            }
        });

        // 4. CHART KEAKTIFAN COACH (BAR CHART APEX STYLE)
        new Chart(document.getElementById('coachAttendanceChart'), {
            type: 'bar',
            data: {
                labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                datasets: [{
                    label: 'Total Kehadiran Sesi',
                    backgroundColor: '#f59e0b',
                    hoverBackgroundColor: '#d97706',
                    data: {!! json_encode($coach_attendance_data) !!},
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 5, color: '#9ca3af' },
                        grid: { color: '#f3f4f6' }
                    }
                }
            }
        });
    </script>
</x-app-layout>