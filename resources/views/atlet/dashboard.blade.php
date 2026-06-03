<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Atlet') }}
        </h2>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- WRAPPER --}}
    <div class="py-6 px-4 sm:px-6 lg:px-8 bg-gray-50 min-h-screen">
        
        {{-- JIKA ERROR (BELUM TERHUBUNG DATA ATLET) --}}
        @if(isset($error))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Perhatian!</p>
                <p>{{ $error }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- === BAGIAN KIRI (8 Kolom) === --}}
            <div class="lg:col-span-8 flex flex-col gap-6">
                
                {{-- 1. HEADER CARD (SELALU TAMPIL) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Halo, {{ Auth::user()->name }}! </h3>
                        <p class="text-sm text-gray-500 mt-1">Selamat datang di dashboard atlet.</p>
                        <div class="mt-3 flex gap-2">
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full border border-blue-100">
                                {{ $atlet->kategori_hitung ?? '-' }}
                            </span>
                            <span class="px-3 py-1 bg-purple-50 text-purple-700 text-xs font-bold rounded-full border border-purple-100">
                                {{ $atlet->posisi ?? '-' }}
                            </span>
                        </div>
                    </div>
                    
                    {{-- TOMBOL CETAK LAPORAN (HANYA TAMPIL JIKA AKTIF) --}}
                    @if(Auth::user()->atlet && Auth::user()->atlet->status != 'Pending')
                        <div>
                            <a href="{{ route('atlet.preview_rapor') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                CETAK LAPORAN
                            </a>
                        </div>
                    @endif
                </div>

                {{-- ========================================================= --}}
                {{-- LOGIKA GEMBOK: JIKA PENDING TAMPILKAN ALERT, JIKA AKTIF TAMPILKAN FITUR --}}
                {{-- ========================================================= --}}
                @if(Auth::user()->atlet && Auth::user()->atlet->status == 'Pending')
                    
                    {{-- TAMPILAN TERKUNCI --}}
                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-8 text-center shadow-sm">
                        <div class="text-6xl mb-4">🔒</div>
                        <h2 class="text-2xl font-extrabold text-red-800 mb-2">Akses Terkunci!</h2>
                        <p class="text-red-600 mb-6 font-medium">
                            Maaf, fitur Jadwal Latihan, Grafik Performa, dan Evaluasi Coach belum bisa diakses.<br>
                            Silakan selesaikan <b class="underline">Tagihan Pendaftaran & SPP</b> Anda terlebih dahulu.
                        </p>
                    </div>

                @else

                    {{-- 2. STATISTIK ABSENSI --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        {{-- Hadir --}}
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $hadir ?? 0 }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Hadir</div>
                        </div>
                        {{-- Izin --}}
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $izin ?? 0 }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Izin</div>
                        </div>
                        {{-- Sakit --}}
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ $sakit ?? 0 }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sakit</div>
                        </div>
                        {{-- Alpha --}}
                        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $alpha ?? 0 }}</div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Alpha</div>
                        </div>
                    </div>

                    {{-- 3. GRAFIK PERKEMBANGAN --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-gray-800">📈 Tren Performa Latihan</h4>
                            <div class="flex gap-3 text-xs">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> Avg Skill</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> IQ & Mental</span>
                            </div>
                        </div>
                        <div class="relative h-64 w-full">
                            @if(isset($chart_labels) && count($chart_labels) > 0)
                                <canvas id="raporChart"></canvas>
                            @else
                                <div class="h-full flex flex-col items-center justify-center text-gray-400 border-2 border-dashed border-gray-100 rounded-lg bg-gray-50">
                                    <span class="text-sm">Belum ada data latihan untuk ditampilkan grafik.</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 4. RIWAYAT LATIHAN & EVALUASI HARIAN --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                📝 Riwayat Latihan & Evaluasi Coach
                            </h4>
                        </div>
                        
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="w-full text-sm text-left text-gray-600">
                                <thead class="text-xs text-white uppercase bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-3">Tanggal</th>
                                        <th class="px-4 py-3 text-center">Absen</th>
                                        <th class="px-2 py-3 text-center bg-indigo-900 w-16 text-[10px] text-white">Dribble</th>
                                        <th class="px-2 py-3 text-center bg-indigo-800 w-16 text-[10px] text-white">Pass</th>
                                        <th class="px-2 py-3 text-center bg-indigo-900 w-16 text-[10px] text-white">Shoot</th>
                                        <th class="px-2 py-3 text-center bg-yellow-600 text-black w-16 font-bold text-[10px]">IQ</th>
                                        <th class="px-4 py-3 min-w-[200px]">Catatan Coach</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($riwayat_latihan) && count($riwayat_latihan) > 0)
                                        @foreach($riwayat_latihan as $row)
                                            <tr class="bg-white border-b hover:bg-gray-50 transition">
                                                <td class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap text-xs">
                                                    {{ $row->jadwal ? \Carbon\Carbon::parse($row->jadwal->tanggal)->translatedFormat('d M Y') : $row->created_at->format('d M Y') }}
                                                    <div class="text-[10px] text-gray-500">
                                                        {{ $row->jadwal ? substr($row->jadwal->jam_mulai, 0, 5) : '-' }} WIB
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    @if($row->status == 'H') <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded">H</span>
                                                    @elseif($row->status == 'S') <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded">S</span>
                                                    @elseif($row->status == 'I') <span class="bg-blue-100 text-blue-800 text-[10px] font-bold px-2 py-0.5 rounded">I</span>
                                                    @else <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded">A</span>
                                                    @endif
                                                </td>
                                                <td class="px-2 py-4 text-center font-bold text-gray-700 bg-indigo-50 border-r border-white text-xs">{{ $row->nilai_dribble ?? '-' }}</td>
                                                <td class="px-2 py-4 text-center font-bold text-gray-700 bg-indigo-50 border-r border-white text-xs">{{ $row->nilai_pass ?? '-' }}</td>
                                                <td class="px-2 py-4 text-center font-bold text-gray-700 bg-indigo-50 border-r border-white text-xs">{{ $row->nilai_shoot ?? '-' }}</td>
                                                <td class="px-2 py-4 text-center font-bold text-gray-900 bg-yellow-50 text-xs">{{ $row->nilai_iq ?? '-' }}</td>
                                                <td class="px-4 py-4 italic text-gray-600 text-xs">{{ $row->catatan ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 text-sm">
                                                Belum ada riwayat latihan yang tercatat.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                
                @endif
                {{-- END LOGIKA GEMBOK --}}

            </div> 


            {{-- === BAGIAN KANAN (4 Kolom) === --}}
            <div class="lg:col-span-4 flex flex-col gap-6">
                
                {{-- 1. STATUS TAGIHAN SPP (SELALU TAMPIL AGAR BISA BAYAR) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Status Keuangan</h4>
                    
                    @if(isset($tagihan_pending) && $tagihan_pending > 0)
                        <div class="w-16 h-16 mx-auto bg-red-100 text-red-600 rounded-full flex items-center justify-center text-2xl mb-3">⚠️</div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">Tunggakan</h2>
                        <p class="text-sm text-red-600 font-medium mb-4">
                            {{ $tagihan_pending }} bulan belum lunas
                        </p>
                        <a href="{{ route('atlet.tagihan.index') }}" class="block w-full bg-red-600 text-white text-sm font-bold px-4 py-2.5 rounded-lg hover:bg-red-700 transition">
                            Bayar Sekarang
                        </a>
                    @else
                        <div class="w-16 h-16 mx-auto bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl mb-3">✅</div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-1">Lunas</h2>
                        <p class="text-sm text-green-600 font-medium mb-4">Administrasi aman!</p>
                        <button class="w-full bg-gray-100 text-gray-400 text-sm font-bold px-4 py-2.5 rounded-lg cursor-not-allowed">
                            Tidak Ada Tagihan
                        </button>
                    @endif
                </div>

                {{-- 2. STATUS MATERI LATIHAN (DIKUNCI JIKA PENDING) --}}
                @if(Auth::user()->atlet && Auth::user()->atlet->status != 'Pending')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Status Silabus Materi</h4>
                        
                        @if(isset($materiTertinggal) && $materiTertinggal > 0)
                            <div class="w-16 h-16 mx-auto bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-2xl mb-3">📚</div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-1">Tertinggal</h2>
                            <p class="text-sm text-orange-600 font-medium mb-4">
                                Kamu melewatkan <strong>{{ $materiTertinggal }} materi</strong>.
                            </p>
                            <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                Fokus kejar ketertinggalan saat sesi latihan ya!
                            </div>
                        @else
                            <div class="w-16 h-16 mx-auto bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl mb-3">🎯</div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-1">Sejajar</h2>
                            <p class="text-sm text-green-600 font-medium mb-4">Materi kamu sejajar dengan tim.</p>
                            <div class="text-xs text-gray-500 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                Pertahankan semangat latihanmu!
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 3. PENGUMUMAN (SELALU TAMPIL) --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full min-h-[250px]">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-t-xl">
                        <h5 class="font-bold text-sm text-gray-700">📢 Pengumuman</h5>
                        <a href="{{ route('notifikasi.index.user') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="p-4 space-y-4 flex-1">
                        @if(isset($notifikasis) && $notifikasis->count() > 0)
                            @foreach($notifikasis as $info)
                                <div class="pb-3 border-b border-gray-50 last:border-0 last:pb-0">
                                    <p class="text-sm font-bold text-gray-800 line-clamp-1">{{ $info->judul }}</p>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($info->pesan ?? $info->isi ?? '', 60) }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1 text-right">{{ $info->created_at->diffForHumans() }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="h-full flex flex-col items-center justify-center text-gray-400 text-sm">
                                Tidak ada info baru.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div> 
    </div>

    {{-- SCRIPT CHART JS (HANYA DIRENDER JIKA BUKAN PENDING) --}}
    @if(Auth::user()->atlet && Auth::user()->atlet->status != 'Pending')
        @if(isset($chart_labels) && count($chart_labels) > 0)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('raporChart');
                    if(ctx) {
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @json($chart_labels),
                                datasets: [
                                    {
                                        label: 'Rata-rata Skill',
                                        data: @json($data_teknik),
                                        borderColor: '#3b82f6', // Biru
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        borderWidth: 3,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointBackgroundColor: '#fff',
                                        pointBorderColor: '#3b82f6',
                                        pointBorderWidth: 2
                                    },
                                    {
                                        label: 'IQ & Mental',
                                        data: @json($data_iq), // Menggunakan Data IQ (bukan fisik lagi)
                                        borderColor: '#eab308', // Kuning Emas
                                        backgroundColor: 'rgba(234, 179, 8, 0.1)',
                                        borderWidth: 3,
                                        tension: 0.4,
                                        pointRadius: 4,
                                        pointBackgroundColor: '#fff',
                                        pointBorderColor: '#eab308',
                                        pointBorderWidth: 2
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }, // Legend custom di HTML
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                return context.dataset.label + ': ' + context.parsed.y;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    y: { beginAtZero: true, max: 100, grid: { color: '#f3f4f6' }, ticks: { stepSize: 20 } },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    }
                });
            </script>
        @endif
    @endif
</x-app-layout>