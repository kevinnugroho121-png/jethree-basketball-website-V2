<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Overview') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- BARIS 1: KARTU STATISTIK UTAMA (Dibuat 3 Kolom agar pas 6 Kotak) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                
                {{-- Card 1: Total Atlet (Aktif) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-blue-500 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-bold text-blue-500 text-uppercase mb-1">Total Atlet (Aktif)</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $totalAtlet }} Orang</div>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>

                {{-- Card 2: Atlet Kurang Aktif (TAMBAHAN DOSEN) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-orange-500 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-bold text-orange-500 text-uppercase mb-1">Atlet Kurang Aktif</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $atletKurangAktif }} Orang</div>
                    </div>
                    <div class="p-3 bg-orange-100 rounded-full text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                    </div>
                </div>

                {{-- Card 3: Atlet Telat Materi (TAMBAHAN DOSEN) --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-red-500 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-bold text-red-500 text-uppercase mb-1">Atlet Telat Materi</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $atletTelatMateri }} Orang</div>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>

                {{-- Card 4: Total Pelatih --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-green-500 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-bold text-green-500 text-uppercase mb-1">Total Pelatih</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $totalPelatih }} Orang</div>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                </div>

                {{-- Card 5: Jadwal Mendatang --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-indigo-500 flex items-center justify-between transition hover:shadow-md">
                    <div>
                        <div class="text-xs font-bold text-indigo-500 text-uppercase mb-1">Jadwal Mendatang</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $jadwalMendatang }} Sesi</div>
                    </div>
                    <div class="p-3 bg-indigo-100 rounded-full text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>

                {{-- Card 6: Pendapatan (BISA DIKLIK) --}}
                <a href="{{ route('tagihan.index') }}" class="block bg-white overflow-hidden shadow-sm sm:rounded-lg p-5 border-l-4 border-yellow-500 flex items-center justify-between transition duration-300 hover:shadow-lg hover:bg-yellow-50 cursor-pointer transform hover:-translate-y-1">
                    <div>
                        <div class="text-xs font-bold text-yellow-500 text-uppercase mb-1">Pendapatan (Bulan Ini)</div>
                        <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</div>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </a>
            </div>

            {{-- BARIS 2: GRAFIK CHART & TABEL --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                
                {{-- KARTU GRAFIK: GENDER --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h6 class="font-bold text-gray-700">Statistik Gender Atlet</h6>
                    </div>
                    <div class="p-6 flex flex-col items-center">
                        <div class="relative h-64 w-full flex justify-center">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <div class="mt-4 text-center text-sm text-gray-600">
                            <span class="mr-4"><i class="inline-block w-3 h-3 bg-blue-500 rounded-full mr-1"></i> Laki-laki: <strong>{{ $cowok }}</strong></span>
                            <span><i class="inline-block w-3 h-3 bg-pink-500 rounded-full mr-1"></i> Perempuan: <strong>{{ $cewek }}</strong></span>
                        </div>
                    </div>
                </div>

                {{-- KARTU GRAFIK: KELOMPOK UMUR --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <h6 class="font-bold text-gray-700">Sebaran Kelompok Umur (KU)</h6>
                    </div>
                    <div class="p-6">
                        <div class="relative h-64 w-full">
                            <canvas id="kuChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BARIS 3: GRAFIK PENDAPATAN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h6 class="font-bold text-gray-700">Tren Pendapatan (6 Bulan Terakhir)</h6>
                </div>
                <div class="p-6">
                    <div class="relative h-72 w-full">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- BARIS 4: JADWAL HARI INI & MENDATANG --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h6 class="font-bold text-gray-700">📅 Jadwal Latihan Terdekat</h6>
                    <a href="{{ route('jadwal.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-700 uppercase font-bold text-xs">
                            <tr>
                                <th class="px-6 py-3">Tanggal & Jam</th>
                                <th class="px-6 py-3">Kategori</th>
                                <th class="px-6 py-3">Materi</th>
                                <th class="px-6 py-3">Pelatih</th>
                                <th class="px-6 py-3">Lokasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($upcomingSchedules as $jadwal)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }}</div>
                                        <div class="text-xs text-blue-600 font-semibold">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-xs font-bold">{{ $jadwal->kategori }}</span>
                                    </td>
                                    <td class="px-6 py-4 truncate max-w-xs">{{ $jadwal->materi ?? '-' }}</td>
                                    <td class="px-6 py-4 font-semibold text-gray-700">{{ $jadwal->pelatih->nama_lengkap ?? 'Admin' }}</td>
                                    <td class="px-6 py-4 text-gray-500">{{ $jadwal->lokasi }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">
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

    {{-- SCRIPT KHUSUS CHART --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 1. DATA GENDER (Doughnut)
        new Chart(document.getElementById('genderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Laki-laki', 'Perempuan'],
                datasets: [{
                    data: [{{ $cowok }}, {{ $cewek }}],
                    backgroundColor: ['#3B82F6', '#EC4899'], // Blue & Pink
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // 2. DATA KELOMPOK UMUR (Bar)
        new Chart(document.getElementById('kuChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($kuLabels) !!}, // ['KU-10', 'KU-12', ...]
                datasets: [{
                    label: "Jumlah Atlet",
                    backgroundColor: "#6366F1", // Indigo
                    data: {!! json_encode($kuValues) !!}, // [5, 10, ...]
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // 3. DATA PENDAPATAN (Line Chart)
        new Chart(document.getElementById('incomeChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($incomeLabels) !!}, // ['Jan 2026', 'Feb 2026', ...]
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: {!! json_encode($incomeValues) !!},
                    borderColor: '#10B981', // Emerald Green
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3 // Garis lengkung halus
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) { return 'Rp ' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>