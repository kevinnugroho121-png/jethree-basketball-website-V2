<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Jethree App') }}</title>
        <link rel="icon" type="image/png" href="{{ secure_asset('images/logo_j3.png') }}">

        {{-- FONT: Inter & Montserrat (Senada dengan Landing Page) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- [LIBRARY] SweetAlert2 --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        {{-- [LIBRARY] Animate.css --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        
        {{-- SUNTIKAN CSS: THE MAJESTIC OVERLAY UNTUK DASHBOARD --}}
        <style>
            /* Tema Warna Baru */
            :root {
                --jethree-navy: #0b132b;
                --jethree-green: #2a5d40;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                background-color: #f8fafc; /* Fallback color */
            }

            /* Menimpa font h1, h2, h3 di dashboard agar pakai Montserrat */
            h1, h2, h3, .font-montserrat {
                font-family: 'Montserrat', sans-serif !important;
            }

            /* Container utama untuk background basket */
            .dashboard-bg-wrapper {
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                z-index: -2;
                background-image: url('{{ asset('images/merak.jpg') }}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                opacity: 20; /* Sangat tipis agar tidak mengganggu data tabel */
                filter: grayscale(100%); /* Hitam putih agar elegan */
            }

            /* Lapisan warna solid di bawah foto */
            .dashboard-bg-base {
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                z-index: -3;
                background-color: #f1f5f9; /* Slate 50 - Sangat terang & bersih */
            }

            /* Lapisan gradien halus di atas foto */
            .dashboard-bg-gradient {
                position: fixed;
                top: 0; left: 0; width: 100vw; height: 100vh;
                z-index: -1;
                background: linear-gradient(135deg, rgba(248, 250, 252, 0.9) 0%, rgba(226, 232, 240, 0.6) 100%);
            }
            
            /* Custom Scrollbar untuk area konten utama */
            .main-content-scroll::-webkit-scrollbar { width: 8px; }
            .main-content-scroll::-webkit-scrollbar-track { background: transparent; }
            .main-content-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            .main-content-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        </style>
    </head>
    
    <body class="antialiased text-slate-800">
        
        {{-- LAPISAN BACKGROUND MAJESTIC (Sangat Halus untuk Dashboard) --}}
        <div class="dashboard-bg-base"></div>
        <div class="dashboard-bg-wrapper"></div>
        <div class="dashboard-bg-gradient"></div>
        
        <div class="flex h-screen overflow-hidden relative z-0">
            
            {{-- 1. INCLUDE SIDEBAR (MENU KIRI) --}}
            @include('layouts.sidebar')

            {{-- 2. KONTEN UTAMA (KANAN) --}}
            <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
                
                {{-- Top Header (Navigasi Atas) --}}
                @include('layouts.navigation')

                {{-- Area Scrollable (Konten Dashboard) --}}
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-transparent p-4 lg:p-6 main-content-scroll">
                    
                    {{-- Slot Header (Judul Halaman) --}}
                    @if (isset($header))
                        <div class="mb-6">
                            {{ $header }}
                        </div>
                    @endif

                    {{-- Render Konten --}}
                    {{ $slot ?? '' }}
                    @yield('content')
                    
                </main>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- LOGIC POP-UP NOTIFIKASI OTOMATIS (TETAP AMAN) --}}
        {{-- ========================================================== --}}
        @auth
            @php
                try {
                    $unreadNotif = \App\Models\Notifikasi::where('user_id', Auth::id())
                                                        ->where('is_read', false)
                                                        ->latest()
                                                        ->first();
                } catch (\Exception $e) {
                    $unreadNotif = null;
                }
            @endphp

            @if($unreadNotif)
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        
                        let audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                        audio.play().catch(error => console.log('Autoplay audio blocked'));

                        Swal.fire({
                            title: '{{ $unreadNotif->judul }}',
                            text: '{{ $unreadNotif->pesan }}',
                            icon: '{{ $unreadNotif->tipe == "sukses" ? "success" : ($unreadNotif->tipe == "tagihan" ? "warning" : "info") }}',
                            
                            confirmButtonText: 'Oke, Saya Mengerti',
                            confirmButtonColor: '#2a5d40', // Diubah ke Forest Green
                            
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            
                            showClass: { popup: 'animate__animated animate__fadeInDown' },
                            hideClass: { popup: 'animate__animated animate__fadeOutUp' }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('notifikasi.markRead', $unreadNotif->id) }}";
                            }
                        });
                    });
                </script>
            @endif
        @endauth

    </body>
</html>