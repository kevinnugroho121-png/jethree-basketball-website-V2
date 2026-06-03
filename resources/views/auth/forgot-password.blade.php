<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Jethree Academy</title>
    
    {{-- FONT: Inter & Montserrat --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Montserrat:wght@700;800;900&display=swap" rel="stylesheet">
    
    {{-- TAILWIND CSS --}}
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
                            600: '#2a5d40', // Forest Green
                            700: '#204a32',
                            navy: '#0b132b', // Midnight Navy
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            /* BACKGROUND FOTO LAPANGAN BASKET (Senada dengan Login) */
            background-image: url('{{ asset('images/ibl.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        
        /* LAPISAN OVERLAY MIDNIGHT NAVY GELAP */
        .overlay-navy {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #0b132b; /* Midnight Navy */
            opacity: 0.85; /* Tingkat kegelapan 85% */
            z-index: -1;
        }

        /* EFEK GLOW UNTUK TOMBOL CTA */
        .btn-glow { box-shadow: 0 0 15px rgba(42, 93, 64, 0.4); }
        .btn-glow:hover { box-shadow: 0 0 25px rgba(42, 93, 64, 0.6); }

        /* ANIMASI MUNCUL */
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 overflow-x-hidden">

    {{-- Selimut Gelap untuk foto lapangan basket --}}
    <div class="overlay-navy"></div>

    {{-- KARTU FORM (Kotak Putih Melayang) --}}
    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 sm:p-10 border border-slate-100 relative z-10 animate-fade-in-up">
        
        {{-- HEADER KARTU --}}
        <div class="flex flex-col items-center mb-6 text-center">
            <img src="{{ asset('images/logo_j3.png') }}" alt="Logo Jethree" class="h-16 w-auto object-contain mb-4 drop-shadow-md" onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=J3';">
            <h1 class="font-montserrat text-2xl font-black text-jethree-navy tracking-tight uppercase">
                Lupa Password?
            </h1>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-2">Jethree Academy</p>
        </div>

        {{-- TEKS PETUNJUK (Translated & Styled) --}}
        <div class="mb-6 text-sm text-slate-500 font-medium leading-relaxed text-center px-2">
            Masukkan alamat email Anda yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang sandi Anda.
        </div>

        {{-- PESAN STATUS (Jika Email Berhasil Dikirim) --}}
        @if (session('status'))
            <div class="mb-6 text-center bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-bold shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- FORM INPUT --}}
        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            {{-- INPUT EMAIL (Floating Label Style) --}}
            <div class="relative">
                <input id="email" class="peer block w-full px-5 py-4 rounded-xl border-2 border-slate-200 bg-slate-50 placeholder-transparent focus:border-jethree-600 focus:bg-white focus:outline-none focus:ring-0 transition-colors font-semibold text-slate-800" 
                       type="email" name="email" value="{{ old('email') }}" required autofocus 
                       placeholder="Email" />
                <label for="email" class="absolute left-4 -top-2.5 bg-white px-1.5 text-[10px] font-black text-slate-400 uppercase tracking-widest transition-all peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-placeholder-shown:text-slate-400 peer-placeholder-shown:font-semibold peer-placeholder-shown:normal-case peer-focus:-top-2.5 peer-focus:text-[10px] peer-focus:text-jethree-600 peer-focus:font-black peer-focus:uppercase peer-focus:tracking-widest rounded">
                    Alamat Email
                </label>
                @if ($errors->has('email'))
                    <p class="mt-1.5 text-xs text-red-600 font-bold ml-1">🚫 {{ $errors->first('email') }}</p>
                @endif
            </div>

            {{-- TOMBOL SUBMIT --}}
            <button type="submit" class="btn-glow w-full py-4 rounded-xl bg-gradient-to-r from-jethree-600 to-jethree-700 text-white font-black tracking-widest hover:from-jethree-700 hover:to-jethree-800 transition transform hover:-translate-y-1 mt-6 uppercase text-sm">
                Kirim Link Reset Password
            </button>

            {{-- TOMBOL KEMBALI --}}
            <div class="pt-6 mt-4 border-t border-slate-100 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 text-slate-400 hover:text-jethree-navy font-bold transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Halaman Login
                </a>
            </div>
            
        </form>
    </div>

</body>
</html>