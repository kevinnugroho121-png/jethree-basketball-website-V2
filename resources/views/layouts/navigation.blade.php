<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 h-16 flex-shrink-0 z-20 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
        <div class="flex justify-between h-full">
            
            {{-- 1. BAGIAN KIRI (JUDUL APLIKASI - KHUSUS MOBILE) --}}
            <div class="flex items-center md:hidden">
                <span class="font-bold text-lg text-gray-800">Jethree App</span>
            </div>
            
            <div class="hidden md:flex items-center">
            </div>

            {{-- 2. BAGIAN KANAN (PROFIL USER + LONCENG NOTIF) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                
                {{-- A. MENU DROPDOWN PROFIL --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-green-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold mr-2 shadow-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Saya') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-red-600 hover:text-red-800">
                                {{ __('Keluar Aplikasi') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                {{-- B. PEMBATAS --}}
                <div class="h-6 w-px bg-gray-200"></div>

                {{-- C. LONCENG NOTIFIKASI (LOGIKA 24 JAM) --}}
                @php
                    // 1. Hitung Badge Merah (HANYA notif yang benar-benar belum dibaca)
                    $notifCount = \App\Models\Notifikasi::where('user_id', Auth::id())
                                        ->where('is_read', false)
                                        ->count();

                    // 2. Ambil List Dropdown (Notif 24 Jam Terakhir ATAU yang belum dibaca)
                    $listNotifs = \App\Models\Notifikasi::where('user_id', Auth::id())
                        ->where(function($query) {
                            $query->where('created_at', '>=', \Carbon\Carbon::now()->subHours(24))
                                  ->orWhere('is_read', false);
                        })
                        ->orderBy('created_at', 'desc')
                        ->take(10) // Maksimal 10 biar nggak kepanjangan
                        ->get();

                    // Logika Route Tombol "Lihat Semua" Berdasarkan Role
                    $notifRoute = '#';
                    if(Auth::user()->role == 'admin') {
                        $notifRoute = route('notifikasi.index'); 
                    } elseif(Auth::user()->role == 'atlet') {
                        $notifRoute = route('notifikasi.index.user'); 
                    }
                @endphp

                <x-dropdown align="right" width="96">
                    <x-slot name="trigger">
                        <button class="relative p-2 text-gray-500 hover:text-green-600 focus:outline-none transition rounded-full hover:bg-gray-100">
                            {{-- Ikon Lonceng --}}
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            
                            {{-- Badge Angka Merah --}}
                            @if($notifCount > 0)
                                <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full animate-bounce">
                                    {{ $notifCount }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        {{-- DIV PEMBUNGKUS BARU UNTUK MELEBARKAN KOTAK NOTIFIKASI --}}
                        <div class="w-80 sm:w-96">
                            
                            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                <h3 class="font-bold text-sm text-gray-700">Aktivitas Terbaru</h3>
                                <span class="text-[10px] text-gray-400 font-medium shrink-0">24 Jam Terakhir</span>
                            </div>
                            
                            <div class="max-h-72 overflow-y-auto custom-scroll">
                                @forelse($listNotifs as $notif)
                                    {{-- Visual Beda untuk Notif Belum Dibaca vs Sudah Dibaca --}}
                                    <a href="{{ $notifRoute }}" class="block px-4 py-3 border-b border-gray-50 transition hover:bg-gray-100 {{ !$notif->is_read ? 'bg-green-50/40' : 'opacity-70' }}">
                                        <div class="flex justify-between items-start mb-1 gap-2">
                                            <p class="text-xs font-bold text-gray-800">{{ $notif->judul }}</p>
                                            {{-- Titik merah kecil kalau belum dibaca --}}
                                            @if(!$notif->is_read)
                                                <span class="w-2 h-2 rounded-full bg-red-500 mt-1 shrink-0"></span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-gray-600 mt-0.5 line-clamp-2">{{ $notif->pesan }}</p>
                                        <span class="text-[9px] text-green-600 mt-1.5 block font-medium">{{ $notif->created_at->diffForHumans() }}</span>
                                    </a>
                                @empty
                                    <div class="px-4 py-8 text-center">
                                        <span class="text-3xl block mb-2 opacity-50">📭</span>
                                        <p class="text-xs text-gray-400 font-medium">Belum ada aktivitas baru<br>dalam 24 jam terakhir.</p>
                                    </div>
                                @endforelse
                            </div>

                            {{-- Tombol Lihat Semua --}}
                            <div class="border-t border-gray-100 text-center">
                                <a href="{{ $notifRoute }}" class="block px-4 py-2.5 text-xs font-bold text-green-600 hover:text-green-800 hover:bg-green-50 transition">
                                    Lihat Semua Pengumuman
                                </a>
                            </div>

                        </div> {{-- TUTUP DIV PEMBUNGKUS BARU --}}
                    </x-slot>
                </x-dropdown>

            </div>

            {{-- 3. TOMBOL HAMBURGER (KHUSUS MOBILE) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- 4. MENU MOBILE (RESPONSIVE) --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            {{-- MENU KALENDER (Bisa diakses Admin, Pelatih, dan Atlet yang SUDAH AKTIF) --}}
            @if(in_array(Auth::user()->role, ['admin', 'pelatih']) || (Auth::user()->role == 'atlet' && Auth::user()->atlet && Auth::user()->atlet->status != 'Pending'))
                <x-responsive-nav-link :href="route('kalender.index')" :active="request()->routeIs('kalender.index')">
                    📅 Kalender Latihan
                </x-responsive-nav-link>
            @endif

            {{-- MENU ADMIN (MOBILE) --}}
            @if(Auth::user()->role == 'admin')
                <div class="px-4 pt-2 pb-1 text-xs font-bold text-gray-400 uppercase border-t border-gray-100 mt-2">Menu Admin</div>
                <x-responsive-nav-link :href="route('atlet.index')" :active="request()->routeIs('atlet.*')">Manajemen Atlet</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pelatih.index')" :active="request()->routeIs('pelatih.*')">Manajemen Pelatih</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('jadwal.index')" :active="request()->routeIs('jadwal.*')">Manajemen Jadwal</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tagihan.index')" :active="request()->routeIs('tagihan.*')">Keuangan</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifikasi.index')" :active="request()->routeIs('notifikasi.*')">Broadcast</x-responsive-nav-link>
            @endif
            
            {{-- MENU PELATIH (MOBILE) --}}
            @if(Auth::user()->role == 'pelatih')
                <div class="px-4 pt-2 pb-1 text-xs font-bold text-gray-400 uppercase border-t border-gray-100 mt-2">Menu Pelatih</div>
                <x-responsive-nav-link :href="route('pelatih.dashboard')" :active="request()->routeIs('pelatih.dashboard')">Dashboard Pelatih</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pelatih.progres.index')" :active="request()->routeIs('pelatih.progres.*')">Input Progres</x-responsive-nav-link>
            @endif
            
            {{-- MENU OWNER (MOBILE) --}}
            @if(Auth::user()->role == 'owner')
                <x-responsive-nav-link :href="route('owner.dashboard')" :active="request()->routeIs('owner.dashboard')">Owner Dashboard</x-responsive-nav-link>
            @endif

            {{-- MENU ATLET (MOBILE) --}}
            @if(Auth::user()->role == 'atlet')
                <div class="px-4 pt-2 pb-1 text-xs font-bold text-gray-400 uppercase border-t border-gray-100 mt-2">Menu Atlet</div>
                <x-responsive-nav-link :href="route('atlet.dashboard')" :active="request()->routeIs('atlet.dashboard')">Dashboard Atlet</x-responsive-nav-link>
                
                {{-- Hanya tampilkan pengumuman mobile jika tidak pending --}}
                @if(Auth::user()->atlet && Auth::user()->atlet->status != 'Pending')
                    <x-responsive-nav-link :href="route('notifikasi.index.user')" :active="request()->routeIs('notifikasi.index.user')">Pengumuman</x-responsive-nav-link>
                @endif
            @endif
        </div>

        {{-- USER INFO (MOBILE) --}}
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>