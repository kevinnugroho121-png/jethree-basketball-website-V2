<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Jethree Basketball Academy') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- CDN Tailwind (Opsional) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out;
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900">

<div class="min-h-screen flex items-center justify-center
            bg-gradient-to-br from-green-50 via-white to-green-100
            px-4 relative overflow-hidden">

    <div class="absolute -top-40 -left-40 w-[28rem] h-[28rem] bg-green-200 rounded-full blur-3xl opacity-40"></div>
    <div class="absolute -bottom-40 -right-40 w-[28rem] h-[28rem] bg-green-300 rounded-full blur-3xl opacity-30"></div>

    <div class="relative w-full max-w-md animate-fade-in-up">

        <div class="flex flex-col items-center mb-6">
            
            {{-- PERUBAHAN DISINI: 
                 Langsung IMG, tanpa DIV pembungkus (bg-white/shadow/border) 
            --}}
            <a href="/" class="flex justify-center">
                <img
                    src="{{ asset('images/logo_j3.png') }}"
                    alt="Jethree Basketball Academy"
                    class="w-32 h-32 sm:w-36 sm:h-36 object-contain drop-shadow-sm hover:scale-105 transition duration-300"
                >
            </a>

            <h1 class="mt-4 text-xl sm:text-2xl font-extrabold text-gray-800 text-center">
                Jethree Basketball Academy
            </h1>

            <p class="mt-1 text-xs sm:text-sm font-semibold text-green-700 uppercase tracking-wide text-center">
                Sistem Informasi Akademi Basket
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl px-6 sm:px-8 py-7 relative overflow-hidden">
            {{-- Hiasan kecil di pojok kartu --}}
            <div class="absolute top-0 right-0 w-16 h-16 bg-green-50 rounded-bl-full -mr-8 -mt-8 opacity-50"></div>
            
            {{ $slot }}
        </div>

        <p class="mt-8 text-center text-xs text-gray-400 font-medium">
            © {{ date('Y') }} Jethree Basketball Academy.
        </p>

    </div>
</div>

</body>
</html>