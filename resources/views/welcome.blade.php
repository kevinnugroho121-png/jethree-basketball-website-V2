<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Jethree Basketball') }}</title>

    {{-- FONT: Inter & Montserrat --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        montserrat: ['Montserrat', 'sans-serif'],
                    },
                    colors: {
                        jethree: {
                            50: '#f2f7f4',
                            100: '#e1efe6',
                            500: '#3a7a55',
                            600: '#2a5d40', // Forest Green
                            700: '#204a32',
                            navy: '#0b132b', // Midnight Navy
                            gold: '#d4af37', // Gold
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Base class untuk elemen yang akan di-animasikan saat di-scroll */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
        /* Efek Glow untuk Tombol CTA */
        .btn-glow {
            box-shadow: 0 0 20px rgba(42, 93, 64, 0.4);
        }
        .btn-glow:hover {
            box-shadow: 0 0 30px rgba(42, 93, 64, 0.6);
        }
    </style>
</head>
<body class="antialiased font-sans text-gray-800 bg-white">

    {{-- 1. NAVBAR --}}
    <nav class="fixed w-full z-50 top-0 transition-all duration-300 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ secure_asset('images/logo_j3.png') }}" alt="Logo Jethree" class="w-12 h-12 object-contain group-hover:scale-105 transition-transform">
                    <span class="font-montserrat font-extrabold text-xl tracking-tight text-jethree-navy uppercase">
                        Jethree <span class="text-jethree-600">Academy</span>
                    </span>
                </a>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="font-bold text-gray-600 hover:text-jethree-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="hidden md:block px-5 py-2 text-sm font-bold text-jethree-navy hover:text-jethree-600 transition uppercase tracking-wide">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-2.5 text-sm bg-jethree-600 text-white font-bold rounded-lg hover:bg-jethree-700 transition shadow-md uppercase tracking-wide">Daftar</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    {{-- 2. HERO SECTION (UPGRADE: MAJESTIC DARK OVERLAY) --}}
    <section id="home" class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-jethree-navy">
        <div class="absolute inset-0 z-0">
            {{-- Foto Lapangan Basket --}}
            <img src="{{ asset('images/merak.jpg') }}" alt="Background Basket" class="w-full h-full object-cover">
            {{-- Selimut Navy Gelap 85% --}}
            <div class="absolute inset-0 bg-jethree-navy/85"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center reveal active">
            
            {{-- Badge (Diubah ke Dark Mode) --}}
            <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold mb-8 tracking-widest uppercase shadow-sm backdrop-blur-sm">
                <span class="w-2 h-2 bg-[#4ade80] rounded-full animate-ping"></span>
                <span class="w-2 h-2 bg-jethree-500 rounded-full absolute"></span>
                Pendaftaran Atlet Baru Dibuka
            </span>

            {{-- Headline Utama (Teks Putih) --}}
            <h1 class="font-montserrat text-4xl sm:text-6xl md:text-7xl font-black text-white tracking-tighter mb-6 leading-[1.1] uppercase drop-shadow-lg">
                Cetak Prestasi, <br class="hidden md:block">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#4ade80] to-jethree-500">Bangun Karakter Juara</span>
            </h1>

            <p class="mt-6 text-lg md:text-xl text-slate-300 max-w-3xl mx-auto mb-12 leading-relaxed font-medium">
                Akademi basket modern yang berfokus pada pengembangan fundamental dan <strong class="text-white font-bold">didukung Sistem Informasi Terintegrasi</strong> untuk memantau progres atlet secara real-time.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-5 w-full sm:w-auto">
                <a href="{{ route('register') }}" class="btn-glow px-8 py-4 bg-gradient-to-r from-jethree-600 to-[#1e442e] text-white font-montserrat font-bold rounded-xl border border-jethree-500 hover:-translate-y-1 transition-all duration-300 text-center uppercase tracking-wide text-sm relative overflow-hidden group">
                    <span class="relative z-10">Gabung Sekarang</span>
                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out"></div>
                </a>
                
                {{-- Tombol Secondary (Dark Mode Transparan) --}}
                <a href="#program" class="px-8 py-4 bg-transparent text-white font-montserrat font-bold rounded-xl shadow-sm border-2 border-slate-400 hover:border-white hover:bg-white hover:text-jethree-navy backdrop-blur-sm transition-all duration-300 text-center uppercase tracking-wide text-sm">
                    Lihat Program
                </a>
            </div>
        </div>
    </section>

    {{-- 3. STATISTIK --}}
    <section class="py-12 bg-jethree-navy text-white border-y-4 border-jethree-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-gray-700">
                <div>
                    <div class="font-montserrat text-3xl md:text-5xl font-black text-jethree-gold mb-1">50+</div>
                    <div class="text-xs md:text-sm text-gray-400 font-bold uppercase tracking-widest">Atlet Aktif</div>
                </div>
                <div>
                    <div class="font-montserrat text-3xl md:text-5xl font-black text-jethree-gold mb-1">5+</div>
                    <div class="text-xs md:text-sm text-gray-400 font-bold uppercase tracking-widest">Pelatih Lisensi</div>
                </div>
                <div>
                    <div class="font-montserrat text-3xl md:text-5xl font-black text-jethree-gold mb-1">100%</div>
                    <div class="text-xs md:text-sm text-gray-400 font-bold uppercase tracking-widest">Rapor Digital</div>
                </div>
                <div>
                    <div class="font-montserrat text-3xl md:text-5xl font-black text-jethree-gold mb-1">4</div>
                    <div class="text-xs md:text-sm text-gray-400 font-bold uppercase tracking-widest">Kategori Umur</div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. FEATURES --}}
    <section id="keunggulan" class="py-24 bg-gray-50 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 reveal">
                <h2 class="font-montserrat text-3xl md:text-4xl font-black text-jethree-navy mb-4 uppercase tracking-tight">Kenapa Jethree Berbeda?</h2>
                <p class="text-gray-500 text-lg font-medium">Kami mengkombinasikan pelatihan fisik di lapangan dengan teknologi manajemen data modern.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {{-- Kartu 1 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal" style="transition-delay: 100ms;">
                    <div class="w-14 h-14 bg-jethree-50 text-jethree-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h3 class="font-montserrat text-xl font-bold text-jethree-navy mb-3">Kurikulum Terstruktur</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Materi latihan disusun sistematis sesuai usia (SD, SMP, SMA) untuk memaksimalkan potensi atlet secara bertahap.</p>
                </div>

                {{-- Kartu 2 --}}
                <div class="bg-white p-8 rounded-2xl shadow-md border-2 border-jethree-600 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 reveal" style="transition-delay: 200ms;">
                    <div class="absolute top-0 right-0 bg-jethree-600 text-white text-[10px] uppercase tracking-wider font-bold px-4 py-1.5 rounded-bl-xl">Unggulan</div>
                    <div class="w-14 h-14 bg-jethree-50 text-jethree-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="font-montserrat text-xl font-bold text-jethree-navy mb-3">Pantau via Aplikasi</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Orang tua dapat memantau <b class="text-gray-700">Absensi</b>, <b class="text-gray-700">Grafik Progres Latihan</b>, dan Rapor anak secara <i>real-time</i>.</p>
                </div>

                {{-- Kartu 3 --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal" style="transition-delay: 300ms;">
                    <div class="w-14 h-14 bg-jethree-50 text-jethree-600 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h3 class="font-montserrat text-xl font-bold text-jethree-navy mb-3">Administrasi Mudah</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Cek tagihan SPP bulanan, upload bukti bayar, dan terima notifikasi lunas otomatis langsung dari sistem.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. PROGRAM KELAS --}}
    <section id="program" class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 reveal">
                <span class="text-jethree-600 font-bold tracking-widest uppercase text-xs">Integrasi Digital</span>
                <h2 class="font-montserrat text-3xl md:text-4xl font-black text-jethree-navy mt-2 uppercase tracking-tight">Fitur Unggulan Sistem</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="border border-gray-200 rounded-3xl p-8 text-center hover:border-jethree-600 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal" style="transition-delay: 100ms;">
                    <h3 class="font-montserrat text-xl font-bold text-jethree-navy mb-6">Pantau Progres Real-time</h3>
                    <ul class="text-gray-500 space-y-4 mb-8 text-sm text-left">
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Data perkembangan atlet terekam digital</li>
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Rapor evaluasi berkala</li>
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Transparansi nilai untuk orang tua</li>
                    </ul>
                </div>

                <div class="border-2 border-jethree-600 bg-jethree-50 rounded-3xl p-8 text-center transform md:-translate-y-4 shadow-xl relative reveal" style="transition-delay: 200ms;">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-jethree-600 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Most Active</div>
                    <h3 class="font-montserrat text-xl font-bold text-jethree-navy mb-6">Jadwal & Notifikasi</h3>
                    <ul class="text-gray-700 space-y-4 mb-8 text-sm text-left font-medium">
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Notifikasi jadwal otomatis</li>
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Pengingat tagihan SPP bulanan</li>
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Sistem presensi digital pelatih & atlet</li>
                    </ul>
                </div>

                <div class="border border-gray-200 rounded-3xl p-8 text-center hover:border-jethree-600 hover:shadow-xl hover:-translate-y-2 transition-all duration-300 reveal" style="transition-delay: 300ms;">
                    <h3 class="font-montserrat text-xl font-bold text-jethree-navy mb-6">Latihan Berbasis Data</h3>
                    <ul class="text-gray-500 space-y-4 mb-8 text-sm text-left">
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Materi menyesuaikan hasil evaluasi sistem</li>
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Fokus pada fundamental & kebutuhan atlet</li>
                        <li class="flex items-start gap-3"><svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Histori latihan tersimpan aman di cloud</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. MOBILE APP TEASER --}}
    <section class="py-24 bg-jethree-navy text-white overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="md:w-1/2 text-center md:text-left reveal">
                    <h2 class="font-montserrat text-3xl md:text-5xl font-black mb-6 leading-tight uppercase tracking-tight">Latihan Lebih Mudah via <span class="text-jethree-600">Jethree Mobile</span></h2>
                    <p class="text-gray-400 text-lg mb-10 leading-relaxed font-medium">
                        Kami menyediakan aplikasi khusus untuk Orang Tua dan Pelatih. Pantau jadwal, bayar SPP, dan lihat perkembangan anak dalam satu genggaman layar pintar.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center md:items-start gap-3">
                        <a href="{{ asset('downloads/jethree.apk') }}" 
                           download="jethree-academy.apk" 
                           class="btn-glow inline-flex items-center gap-3 px-6 py-3.5 bg-jethree-600 hover:bg-jethree-700 text-white font-montserrat font-bold rounded-xl border border-jethree-500 shadow-lg hover:-translate-y-0.5 transition-all text-sm uppercase tracking-wide">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/>
                            </svg>
                            <span>Download APK (v1.0.0)</span>
                        </a>
                        <span class="text-xs text-slate-400 mt-1 self-center md:self-auto">
                            
                        </span>
                    </div>
                </div>
                
                {{-- Mockup HP --}}
                <div class="md:w-1/2 flex justify-center reveal" style="transition-delay: 200ms;">
                    <div class="relative w-[280px] h-[550px] bg-gray-900 rounded-[3rem] border-[10px] border-gray-800 shadow-2xl overflow-hidden transform md:rotate-2 hover:rotate-0 transition duration-500">
                        <div class="absolute top-0 inset-x-0 h-6 bg-gray-800 rounded-b-xl w-32 mx-auto z-20"></div>
                        <div class="w-full h-full bg-white flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('images/mobile-screen.png') }}" 
                                 alt="JeThree Mobile App" 
                                 class="w-full h-full object-cover object-top"
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/280x550?text=JeThree+App';">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. FAT FOOTER PROFESIONAL --}}
    <footer class="bg-[#070d1e] pt-16 pb-8 border-t border-gray-800 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                
                {{-- Kolom 1: Brand & About --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <img src="{{ secure_asset('images/logo_j3.png') }}" alt="Logo Jethree" class="w-12 h-12 grayscale opacity-70">
                        <span class="font-montserrat font-black text-2xl text-white uppercase tracking-widest">Jethree <span class="text-jethree-600">Academy</span></span>
                    </div>
                    <p class="text-sm leading-relaxed font-medium">
                        Akademi bola basket modern pertama yang mengintegrasikan pelatihan fisik fundamental dengan teknologi sistem informasi manajemen atlet secara real-time.
                    </p>
                </div>

                {{-- Kolom 2: Kontak & Alamat --}}
                <div>
                    <h3 class="text-white font-montserrat font-bold uppercase tracking-wider mb-6">Hubungi Kami</h3>
                    <ul class="space-y-4 text-sm font-medium">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Jl. Raya Wates - Kediri No.19, Jayaraya, Wates, Kec. Wates, Kabupaten Kediri, Jawa Timur 64174</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>+62 856-0460-4406</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-jethree-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>jethreeacademy@gmail.com</span>
                        </li>
                    </ul>
                </div>

                {{-- Kolom 3: Sosial Media --}}
                <div>
                    <h3 class="text-white font-montserrat font-bold uppercase tracking-wider mb-6">Sosial Media</h3>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-jethree-600 hover:text-white transition duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/jethreebasketballacademy" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-jethree-600 hover:text-white transition duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center hover:bg-jethree-600 hover:text-white transition duration-300">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Copyright Area --}}
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 font-medium text-sm">&copy; {{ date('Y') }} Jethree Basketball Academy. All rights reserved.</p>
                <div class="flex gap-6 text-sm font-medium">
                    <a href="#" class="hover:text-white transition">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-white transition">Kebijakan Privasi</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- SCRIPT: Intersection Observer untuk Animasi Scroll --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15 // Elemen akan muncul saat 15% bagiannya masuk ke layar
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Optional: Berhenti mengawasi setelah animasi selesai agar tidak berulang
                        // observer.unobserve(entry.target); 
                    }
                });
            }, observerOptions);

            const revealElements = document.querySelectorAll('.reveal');
            revealElements.forEach((el) => observer.observe(el));
        });
    </script>
</body>
</html>