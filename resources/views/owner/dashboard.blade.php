<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Owner') }}
        </h2>
    </x-slot>

    {{-- LOAD LIBRARY CHART.JS (Via CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. WELCOME BANNER (Desain Jethree Green Premium) --}}
            <div class="rounded-2xl p-8 shadow-xl flex justify-between items-center relative overflow-hidden" 
                 style="background: linear-gradient(135deg, #047857 0%, #064E3B 100%); color: white;">
                <div class="relative z-10">
                    <h3 class="text-3xl font-bold mb-2">Selamat Datang, {{ auth()->user()->name }}! </h3>
                    <p class="opacity-90 font-light tracking-wide"></p>
                </div>
            </div>

            {{-- 2. CARDS STATISTIK (Ringkasan dengan Aksen Garis & Shadow Kuat) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Card 1: Atlet (Aksen Biru) --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg border-t-4 flex items-center gap-5 transition hover:shadow-xl" style="border-top-color: #3B82F6;">
                    <div class="w-16 h-16 flex items-center justify-center text-3xl rounded-full" style="background-color: #EFF6FF; color: #3B82F6;">
                        
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Total Atlet Aktif</p>
                        <h4 class="text-3xl font-extrabold text-gray-800">{{ $atlet_aktif }}</h4>
                    </div>
                </div>

                {{-- Card 2: Pemasukan (Aksen Hijau) --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg border-t-4 flex items-center gap-5 transition hover:shadow-xl" style="border-top-color: #10B981;">
                    <div class="w-16 h-16 flex items-center justify-center text-3xl rounded-full" style="background-color: #ECFDF5; color: #10B981;">
                        
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Total Pemasukan</p>
                        <h4 class="text-3xl font-extrabold text-gray-800">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</h4>
                    </div>
                </div>

                {{-- Card 3: Tunggakan (Aksen Merah) --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg border-t-4 flex items-center gap-5 transition hover:shadow-xl" style="border-top-color: #EF4444;">
                    <div class="w-16 h-16 flex items-center justify-center text-3xl rounded-full" style="background-color: #FEF2F2; color: #EF4444;">
                        
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Belum Lunas</p>
                        <h4 class="text-3xl font-extrabold text-gray-800">Rp {{ number_format($tagihan_belum_lunas, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>

            {{-- 3. AREA GRAFIK (CHARTS) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Grafik Kiri (Line Chart Pemasukan) --}}
                <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                    {{-- Header Card & Filter UI Baru --}}
                    <div class="border-b border-gray-100 pb-4 mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <h4 class="font-bold text-gray-700 text-lg flex items-center gap-2">
                            Tren Pemasukan {{ $filter_tahun }}
                        </h4>
                        
                        {{-- Form Filter UI Menggunakan Input Month --}}
                        <form action="{{ url()->current() }}" method="GET" class="flex gap-2 items-center">
                            <input type="month" name="periode" value="{{ $periode }}" required
                                   class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-green-500 focus:border-green-500 text-gray-600 bg-white cursor-pointer">
                            
                            <button type="submit" class="bg-gray-800 text-white px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-gray-700 transition">
                                Filter
                            </button>

                            {{-- Tombol Reset hanya muncul jika parameter 'periode' ada di URL --}}
                            @if(request()->has('periode'))
                                <a href="{{ url()->current() }}" class="bg-red-50 text-red-600 border border-red-100 px-4 py-1.5 rounded-lg text-sm font-semibold hover:bg-red-100 transition flex items-center justify-center">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    {{-- Canvas Chart --}}
                    <div class="h-64 w-full">
                        <canvas id="incomeChart"></canvas>
                    </div>

                    {{-- Rincian Bawah Grafik (Tetap Sejajar Menyamping / Horizontal) --}}
                    <div class="mt-6 pt-5 border-t border-gray-100 flex flex-row justify-between items-center gap-2 sm:gap-4 text-center">
                        <div class="flex-1 bg-gray-50 rounded-xl py-3 px-1 sm:px-3 border border-gray-100">
                            <p class="text-[10px] sm:text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Sudah Bayar</p>
                            <p class="text-lg sm:text-2xl font-extrabold text-green-600">{{ $sudah_bayar }} <span class="text-[10px] sm:text-xs text-gray-400 font-normal">Atlet</span></p>
                        </div>
                        <div class="flex-1 bg-gray-50 rounded-xl py-3 px-1 sm:px-3 border border-gray-100">
                            <p class="text-[10px] sm:text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Belum Bayar</p>
                            <p class="text-lg sm:text-2xl font-extrabold text-red-500">{{ $belum_bayar }} <span class="text-[10px] sm:text-xs text-gray-400 font-normal">Atlet</span></p>
                        </div>
                        <div class="flex-1 bg-blue-50 rounded-xl py-3 px-1 sm:px-3 border border-blue-100">
                            <p class="text-[10px] sm:text-[11px] text-blue-500 font-bold uppercase tracking-wider mb-1">Total Aktif</p>
                            <p class="text-lg sm:text-2xl font-extrabold text-blue-700">{{ $total_tagihan_bulan }} <span class="text-[10px] sm:text-xs text-blue-400 font-normal">Atlet</span></p>
                        </div>
                    </div>
                </div>

                {{-- Grafik Kanan (Donut Chart Distribusi Kategori) --}}
                <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col">
                    <div class="border-b border-gray-100 pb-4 mb-4">
                        <h4 class="font-bold text-gray-700 text-lg flex items-center gap-2">
                            Distribusi Kategori Atlet
                        </h4>
                    </div>
                    
                    {{-- Canvas Chart Donut & Empty State --}}
                    <div class="flex-grow flex items-center justify-center relative h-56">
                        @if(empty($donut_data))
                            <div class="absolute inset-0 flex flex-col items-center justify-center z-10">
                                <span class="text-3xl mb-2">📭</span>
                                <p class="text-sm text-gray-400 font-medium text-center">Data atlet kosong</p>
                            </div>
                        @endif
                        
                        {{-- ✔️ KODE YANG BENAR (Lebih clean pakai class Tailwind) --}}
                        <div class="h-full w-full relative z-20 {{ empty($donut_data) ? 'opacity-20' : '' }}">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>

                    <div class="mt-4 text-center text-xs font-semibold text-gray-500 bg-gray-50 py-2.5 rounded-xl border border-gray-100">
                        Berdasarkan Atlet Berstatus Aktif
                    </div>
                </div>
            </div>

            {{-- ==================== SUNTIKAN BARU: GRAFIK KEHADIRAN COACH (POIN 50) ==================== --}}
            <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col justify-between">
                {{-- 🟢 PERBAIKAN: Ubah menjadi flex-row agar judul dan dropdown berdampingan rapi --}}
                <div class="border-b border-gray-100 pb-4 mb-4 flex flex-row justify-between items-center gap-4">
                    <h4 class="font-bold text-gray-700 text-lg flex items-center gap-2">
                        📊 Tren Keaktifan Mengajar Pelatih (Presensi Bulanan)
                    </h4>

                    {{-- 🟢 FITUR BARU: Dropdown Filter Nama Pelatih Dinamis --}}
                    <form action="{{ url()->current() }}" method="GET" class="flex items-center">
                        {{-- Mengunci filter periode bulan biar tidak ke-reset saat ganti nama pelatih --}}
                        <input type="hidden" name="periode" value="{{ $periode }}">
                        
                        <select name="pelatih_id" onchange="this.form.submit()" 
                                class="text-xs border border-gray-300 rounded-lg px-3 py-1.5 focus:ring-green-500 focus:border-green-500 text-gray-600 bg-white cursor-pointer font-semibold shadow-sm">
                            <option value="">🌍 Semua Pelatih & Takeover</option>
                            @foreach($list_pelatih as $p)
                                <option value="{{ $p->id }}" {{ request('pelatih_id') == $p->id ? 'selected' : '' }}>
                                    🏀 {{ $p->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="h-64 w-full">
                    <canvas id="coachAttendanceChart"></canvas>
                </div>
                <div class="mt-4 text-center text-xs font-semibold text-gray-400">
                    Menampilkan total akumulasi frekuensi kehadiran seluruh coach Jethree per bulan
                </div>

                {{-- 🟢 SUNTIKAN BARU: TABEL LIST BREAKDOWN MENGAJAR PER PELATIH --}}
                <div class="mt-6 border-t border-gray-100 pt-5">
                    <h5 class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-2">
                        📋 Rincian Kontribusi Pelatih (Bulan & Tahun Terfilter)
                    </h5>
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="w-full text-left text-xs text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 font-bold uppercase tracking-wider">
                                <tr>
                                    <th class="px-4 py-3">Nama Pelatih</th>
                                    <th class="px-4 py-3 text-center">Sesi Terlaksana</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($coach_breakdown as $cb)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $cb->nama_lengkap }}</td>
                                        <td class="px-4 py-3 text-center font-extrabold text-amber-600">{{ $cb->total_mengajar }} Sesi</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-6 text-center text-gray-400 italic">
                                            Tidak ada aktivitas mengajar pelatih yang selesai pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- ========================================================================================= --}}

            {{-- 4. RIWAYAT TRANSAKSI TERAKHIR --}}
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-white">
                    <h4 class="font-bold text-gray-700 text-lg">Transaksi Pembayaran Terbaru</h4>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="uppercase text-xs font-bold text-gray-500" style="background-color: #F8FAFC;">
                            <tr>
                                <th class="px-6 py-4 border-b border-gray-100">Nama Atlet</th>
                                <th class="px-6 py-4 border-b border-gray-100">Keterangan</th>
                                <th class="px-6 py-4 border-b border-gray-100">Tanggal Pelunasan</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-right">Nominal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($riwayat_terbaru as $item)
                                {{-- ✔️ KODE YANG BENAR (Pindahkan hover ke class Tailwind) --}}
                                <tr class="transition hover:bg-[#F8FAFC]">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $item->atlet->user->name ?? $item->atlet->nama_lengkap ?? 'Atlet (Terhapus)' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                                            {{ $item->jenis_tagihan ?? 'SPP ' . $item->bulan . ' ' . $item->tahun }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-medium text-gray-500">
                                        {{ $item->updated_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-extrabold" style="color: #059669;">
                                        + Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <span class="text-4xl mb-3">📭</span>
                                            <p>Belum ada data transaksi masuk.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT RENDERING CHART --}}
    <script>
        // 1. CHART PEMASUKAN (LINE CHART)
        const ctxIncome = document.getElementById('incomeChart').getContext('2d');
        new Chart(ctxIncome, {
            type: 'line',
            data: {
                labels: JSON.parse('{!! json_encode($bulan_label) !!}'),
                datasets: [{
                    label: 'Pemasukan',
                    data: JSON.parse('{!! json_encode($income_data) !!}'),
                    borderColor: '#10B981', 
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4, 
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10B981',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return ' Pendapatan: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        suggestedMax: 1000000, 
                        grid: { borderDash: [4, 4], color: '#f1f5f9' },
                        ticks: {
                            stepSize: 200000,
                            color: '#94a3b8',
                            font: { size: 11 },
                            callback: function(value) {
                                if (value === 0) return 'Rp 0';
                                return 'Rp ' + (value / 1000) + 'k'; 
                            }
                        }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    }
                }
            }
        });

        // 2. CHART DISTRIBUSI KATEGORI ATLET (DOUGHNUT CHART)
        let donutLabels = JSON.parse('{!! json_encode($donut_labels) !!}');
        let donutDataArr = JSON.parse('{!! json_encode($donut_data) !!}');
        
        // Palet warna yang akan me-loop menyesuaikan jumlah KU
        const palette = ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#EF4444'];
        let donutColors = donutDataArr.map((_, index) => palette[index % palette.length]);
        
        // Logika jika data kosong agar tidak blank putih
        if (donutDataArr.length === 0) {
            donutLabels = ['Kosong'];
            donutDataArr = [1];
            donutColors = ['#E2E8F0'];
        }

        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutDataArr,
                    backgroundColor: donutColors, 
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: (donutDataArr[0] === 1 && donutColors[0] === '#E2E8F0') ? 0 : 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', 
                plugins: {
                    legend: { 
                        display: (donutLabels[0] !== 'Kosong'),
                        position: 'bottom', 
                        labels: { usePointStyle: true, padding: 15, color: '#64748B', font: { size: 12 } } 
                    },
                    tooltip: {
                        enabled: (donutLabels[0] !== 'Kosong'),
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.parsed + ' Atlet';
                            }
                        }
                    }
                }
            }
        });

        // ==================== SUNTIKAN SCRIPT BARU: BAR CHART COACH ATTENDANCE (POIN 50) ====================
        const ctxCoach = document.getElementById('coachAttendanceChart').getContext('2d');
        new Chart(ctxCoach, {
            type: 'bar', 
            data: {
                labels: JSON.parse('{!! json_encode($bulan_label) !!}'),
                datasets: [{
                    label: 'Total Kehadiran Sesi',
                    data: JSON.parse('{!! json_encode($coach_attendance_data) !!}'),


                    backgroundColor: '#F59E0B', // Aksen Jingga Premium khas bola basket Jethree
                    borderRadius: 8, // Bikin ujung batang agak tumpul/rounded modern
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [4, 4], color: '#f1f5f9' },
                        ticks: {
                            color: '#94a3b8',
                            stepSize: 5 // Lompatan angka per baris presensi
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8' }
                    }
                }
            }
        });
    </script>
</x-app-layout>