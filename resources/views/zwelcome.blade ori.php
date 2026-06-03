<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Jethree Basketball') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        jethree: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a', // Hijau Utama
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="antialiased font-sans text-gray-800 bg-white">

    {{-- ========================================== --}}
    {{-- 1. NAVBAR (Sticky & Glassmorphism) --}}
    {{-- ========================================== --}}
    <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                {{-- Logo (SUDAH DIPERBAIKI: CLEAN LOOK) --}}
                <a href="/" class="flex items-center gap-3">
                    {{-- Logo Transparan Tanpa Kotak --}}
                    <img src="{{ asset('images/logo_j3.png') }}" alt="Logo Jethree" class="w-12 h-12 object-contain">
                    
                    <span class="font-bold text-xl tracking-tight text-gray-900">Jethree Basketball <span class="text-jethree-600">Academy</span></span>
                </a>

                
                {{-- Auth Buttons --}}
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-jethree-600">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="hidden md:block px-4 py-2 text-sm font-semibold text-gray-700 hover:text-jethree-600 border border-gray-300 rounded-lg hover:border-jethree-600 transition">
                                Masuk
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm bg-jethree-600 text-white font-bold rounded-lg hover:bg-jethree-700 transition shadow-lg shadow-green-200">
                                    Daftar Baru
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- ========================================== --}}
    {{-- 2. HERO SECTION (Header) --}}
    {{-- ========================================== --}}
    <section id="home" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        {{-- Background Image Overlay --}}
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=2090&auto=format&fit=crop" alt="Background Basket" class="w-full h-full object-cover opacity-5">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            <span class="inline-flex items-center gap-2 py-1 px-3 rounded-full bg-green-50 border border-green-200 text-jethree-700 text-sm font-bold mb-6">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Ayo Segera Gabung.. Prestasi Menanti Anda
            </span>

            <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold text-gray-900 tracking-tight mb-6 leading-tight">
                Cetak Prestasi, <br class="hidden md:block">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-jethree-600 to-emerald-400">Bangun Karakter Juara</span>
            </h1>

            <p class="mt-4 text-lg md:text-xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                Akademi basket modern yang berfokus pada pengembangan fundamental dan <b>didukung Sistem Informasi Terintegrasi</b> untuk memantau progres atlet secara real-time.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4 w-full sm:w-auto">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-jethree-600 text-white font-bold rounded-xl shadow-xl hover:bg-jethree-700 hover:-translate-y-1 transition text-center">
                    Gabung Sekarang
                </a>
                <a href="#program" class="px-8 py-4 bg-white text-gray-700 font-bold rounded-xl shadow-sm border border-gray-200 hover:bg-gray-50 transition text-center">
                    Lihat Program
                </a>
            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 3. STATISTIK (Social Proof) --}}
    {{-- ========================================== --}}
    <section class="py-10 bg-jethree-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-green-700/50">
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-yellow-400">100+</div>
                    <div class="text-sm md:text-base text-green-100 mt-1">Atlet Aktif</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-yellow-400">10+</div>
                    <div class="text-sm md:text-base text-green-100 mt-1">Pelatih Berlisensi</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-yellow-400">Digital</div>
                    <div class="text-sm md:text-base text-green-100 mt-1">Rapor & Absensi</div>
                </div>
                <div>
                    <div class="text-3xl md:text-4xl font-bold text-yellow-400">3</div>
                    <div class="text-sm md:text-base text-green-100 mt-1">Kategori Umur</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 4. FEATURES (Fokus Sistem TA) --}}
    {{-- ========================================== --}}
    <section id="keunggulan" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Kenapa Jethree Berbeda?</h2>
                <p class="text-gray-500 text-lg">Kami mengkombinasikan pelatihan fisik di lapangan dengan teknologi manajemen data modern.</p>
            </div>

            {{-- GRID SYSTEM (Responsif: 1 kolom di HP, 3 kolom di Laptop) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                {{-- Kartu 1: Kurikulum --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition duration-300">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-2xl mb-6">🏀</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Kurikulum Bertahap</h3>
                    <p class="text-gray-500">Materi latihan disusun sistematis sesuai usia (SD, SMP, SMA) untuk memaksimalkan potensi atlet.</p>
                </div>

                {{-- Kartu 2: Digital Monitoring (HIGHLIGHT TA) --}}
                <div class="bg-white p-8 rounded-2xl shadow-md border-2 border-jethree-100 relative overflow-hidden group hover:border-jethree-500 transition duration-300">
                    <div class="absolute top-0 right-0 bg-jethree-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">Unggulan</div>
                    <div class="w-12 h-12 bg-green-100 text-jethree-600 rounded-lg flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition">📱</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Pantau via Aplikasi</h3>
                    <p class="text-gray-500">Orang tua dapat memantau <b>Absensi</b>, <b>Grafik Progres Latihan</b>, dan Rapor anak secara <i>real-time</i> melalui Smartphone.</p>
                </div>

                {{-- Kartu 3: Transparansi --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition duration-300">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-2xl mb-6">💳</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Administrasi Mudah</h3>
                    <p class="text-gray-500">Cek tagihan SPP bulanan, upload bukti bayar, dan terima notifikasi lunas otomatis dari sistem.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 5. PROGRAM KELAS (Dari Proposal) --}}
    {{-- ========================================== --}}
    <section id="program" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-12 text-center">Kelompok Usia (KU)</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border border-gray-200 rounded-2xl p-6 text-center hover:border-jethree-500 transition cursor-pointer">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Pantau Progres Real-time</h3>
                    <p class="text-sm text-gray-500 mb-4"></p>
                    <ul class="text-gray-600 space-y-2 mb-6 text-sm">
                        <li>✅ Data perkembangan atlet terekam digital</li>
                        <li>✅ Rapor evaluasi berkala</li>
                        <li>✅ Transparansi nilai untuk orang tua</li>
                    </ul>
                    <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold">Standar</span>
                </div>

                <div class="border-2 border-jethree-500 bg-green-50/50 rounded-2xl p-6 text-center transform scale-105 shadow-xl">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Jadwal & Notifikasi Pintar</h3>
                    <p class="text-sm text-gray-500 mb-4"></p>
                    <ul class="text-gray-600 space-y-2 mb-6 text-sm font-medium">
                        <li>✅ Notifikasi jadwal otomatis via WhatsApp</li>
                        <li>✅ Pengingat tagihan SPP</li>
                        <li>✅ Sistem presensi digital</li>
                    </ul>
                    <span class="px-4 py-2 bg-jethree-600 text-white rounded-full text-sm font-semibold">Paling Populer</span>
                </div>

                <div class="border border-gray-200 rounded-2xl p-6 text-center hover:border-jethree-500 transition cursor-pointer">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Latihan Berbasis Data</h3>
                    <p class="text-sm text-gray-500 mb-4"></p>
                    <ul class="text-gray-600 space-y-2 mb-6 text-sm">
                        <li>✅ Materi menyesuaikan hasil evaluasi sistem</li>
                        <li>✅ Fokus pada fundamental & kebutuhan atlet</li>
                        <li>✅ Histori latihan tersimpan aman</li>
                    </ul>
                    <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold">Standar</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 6. MOBILE APP TEASER (Sangat Penting utk TA) --}}
    {{-- ========================================== --}}
    <section class="py-20 bg-gray-900 text-white overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="md:w-1/2 text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Latihan Lebih Mudah dengan Aplikasi Jethree Mobile</h2>
                    <p class="text-gray-400 text-lg mb-8">
                        Kami menyediakan aplikasi Android khusus untuk Orang Tua dan Pelatih. Pantau jadwal, bayar SPP, dan lihat perkembangan anak dalam satu genggaman.
                    </p>
                    <div class="flex justify-center md:justify-start gap-4">
                        <button class="flex items-center gap-3 px-6 py-3 bg-white text-gray-900 rounded-xl font-bold hover:bg-gray-100 transition">
                            Download APK
                        </button>
                    </div>
                </div>
                {{-- Mockup HP Sederhana dengan CSS --}}
                <div class="md:w-1/2 flex justify-center">
                    <div class="relative w-64 h-[500px] bg-gray-800 rounded-[3rem] border-8 border-gray-700 shadow-2xl overflow-hidden">
                        {{-- Screen --}}
                        <div class="w-full h-full bg-white flex flex-col items-center justify-center text-gray-800">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-2xl mb-4">📊</div>
                            <h4 class="font-bold">Rapor Digital</h4>
                            <p class="text-xs text-gray-500 px-8 text-center mt-2">Grafik perkembangan shooting & dribbling ananda Budi meningkat 20% bulan ini.</p>
                            
                            <div class="mt-8 w-40 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="w-3/4 h-full bg-green-500"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 7. FOOTER --}}
    {{-- ========================================== --}}
    <footer class="bg-white py-12 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex justify-center items-center gap-2 mb-4">
                 {{-- Ganti src dengan logo aslimu --}}
                 {{-- <img src="{{ asset('images/logo-jethree.jpg') }}" class="w-8 h-8 grayscale opacity-50"> --}}
                 <span class="font-bold text-xl text-gray-400">Jethree Academy</span>
            </div>
            <p class="text-gray-500 font-medium mb-4">&copy; 2026 Jethree Basketball Academy. All rights reserved.</p>
            <p class="text-gray-400 text-sm">
                Sistem Informasi Manajemen Atlet & Keuangan<br>
            </p>
        </div>
    </footer>

</body>
</html>