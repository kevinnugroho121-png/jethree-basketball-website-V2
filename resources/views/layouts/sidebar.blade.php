{{-- 
    SIDEBAR REDESIGN: PERSISTENT STATE, COMPACT, FLOATING BUTTONS
--}}
<aside 
    x-data="{ 
        sidebarOpen: localStorage.getItem('sidebarOpen') === null ? true : localStorage.getItem('sidebarOpen') === 'true',
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
        }
    }"
    :class="sidebarOpen ? 'w-56' : 'w-20'" 
    class="bg-white border-r border-gray-200 hidden md:flex flex-col h-full shadow-xl fixed md:relative z-30 transition-all duration-300 ease-in-out font-sans"
>
    
    {{-- =================================== --}}
    {{-- HEADER: LOGO & HAMBURGER TOGGLE --}}
    {{-- =================================== --}}
    <div class="h-20 flex items-center justify-between bg-white px-4 shrink-0 mb-2">
        
        {{-- Brand & Logo --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 overflow-hidden whitespace-nowrap transition-all" 
           :class="!sidebarOpen ? 'justify-center w-full' : ''">
            
            {{-- Logo --}}
            <img src="{{ asset('images/logo_j3.png') }}" alt="Logo" class="object-contain transition-all duration-300"
                 :class="sidebarOpen ? 'w-8 h-8' : 'w-9 h-9'">
            
            {{-- Teks App --}}
            <div x-show="sidebarOpen" class="transition-opacity duration-300 flex flex-col">
                <span class="font-bold text-lg text-gray-800 tracking-tight leading-none">
                    J3 <span class="text-green-600"> App</span>
                </span>
            </div>
        </a>

        {{-- Tombol Hamburger (Strip 3) --}}
        <button x-show="sidebarOpen" @click="toggleSidebar()" 
                class="p-1.5 rounded-md text-gray-400 hover:bg-green-50 hover:text-green-600 transition focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    {{-- Tombol Pembuka (Saat Sidebar Tertutup) --}}
    <button x-show="!sidebarOpen" @click="toggleSidebar()" 
            class="mx-auto w-10 h-10 flex items-center justify-center text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition mb-4"
            title="Buka Menu">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>


    {{-- =================================== --}}
    {{-- MENU SIDEBAR (SCROLLABLE) --}}
    {{-- =================================== --}}
    <div class="flex-1 overflow-y-auto overflow-x-hidden bg-white px-3 space-y-1.5 pb-10">

        {{-- =================================== --}}
        {{-- 1. MENU KHUSUS ADMIN --}}
        {{-- =================================== --}}
        @if(Auth::user()->role == 'admin')
            
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('admin.dashboard') 
                          ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                          : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Dashboard Admin</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">D</span>
            </a>

            {{-- Label Kategori --}}
            <div x-show="sidebarOpen" class="mt-4 mb-2 px-2 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">
                Master Data
            </div>
            <div x-show="!sidebarOpen" class="my-3 h-px bg-gray-100 mx-2"></div>

            {{-- Atlet --}}
            <a href="{{ route('atlet.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('atlet.*') 
                          ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                          : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Data Atlet</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">A</span>
            </a>

            {{-- Pelatih --}}
            <a href="{{ route('pelatih.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('pelatih.*') 
                          ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                          : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Data Pelatih</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">P</span>
            </a>

            {{-- Master Materi --}}
            <a href="{{ route('master-materi.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('master-materi.*') 
                          ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                          : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Master Materi</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">M</span>
            </a>

            {{-- Jadwal --}}
            <a href="{{ route('jadwal.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('jadwal.*') 
                          ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                          : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Manajemen Jadwal</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">J</span>
            </a>

            {{-- Absensi Kehadiran --}}
            <a href="{{ route('absensi.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group mt-2
                      {{ request()->routeIs('absensi.*')
                          ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                          : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Absensi Kehadiran</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">A</span>
            </a>

            {{-- Rekap Pelatih --}}
            <a href="{{ route('admin.rekap-pelatih') }}" 
            class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                   {{ request()->routeIs('admin.rekap-pelatih*') 
                       ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                       : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
            :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Rekap Pelatih</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">R</span>
            </a>

            {{-- Label Kategori --}}
            <div x-show="sidebarOpen" class="mt-4 mb-2 px-2 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">
                Keuangan & Sistem
            </div>
            <div x-show="!sidebarOpen" class="my-3 h-px bg-gray-100 mx-2"></div>

            {{-- Keuangan --}}
            <a href="{{ route('tagihan.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('tagihan.*') 
                          ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                          : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Keuangan (SPP)</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">K</span>
            </a>

            {{-- Broadcast --}}
            <a href="{{ route('notifikasi.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('notifikasi.*') 
                          ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                          : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Broadcast Info</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">B</span>
            </a>

            {{-- Menu Kalender --}}
            <a href="{{ route('kalender.index') }}" 
            class="mt-1 flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                   {{ request()->routeIs('kalender.*') 
                       ? 'bg-green-600 text-white font-bold border-green-600 shadow-md shadow-green-200' 
                       : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
            :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Kalender Latihan</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">K</span>
            </a>

        {{-- =================================== --}}
        {{-- 2. MENU KHUSUS PELATIH --}}
        {{-- =================================== --}}
        @elseif(Auth::user()->role == 'pelatih')
            
            <div x-show="sidebarOpen" class="mt-4 mb-2 px-2 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Menu Pelatih</div>
            <div x-show="!sidebarOpen" class="my-3 h-px bg-gray-100 mx-2"></div>

            {{-- Dashboard --}}
            <a href="{{ route('pelatih.dashboard') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('pelatih.dashboard') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Dashboard</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">D</span>
            </a>

            {{-- Absensi Kehadiran --}}
            <a href="{{ route('pelatih.absensi.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('pelatih.absensi.*') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Absensi Kehadiran</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">A</span>
            </a>

            {{-- Input Progres --}}
            <a href="{{ route('pelatih.progres.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('pelatih.progres.*') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Input Progres</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">I</span>
            </a>
            
            {{-- Profil --}}
            <a href="{{ route('profile.edit') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('profile.edit') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Profil Saya</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">P</span>
            </a>

            {{-- Kalender Latihan (Pelatih) --}}
            <a href="{{ route('kalender.index') }}" 
            class="mt-1 flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                   {{ request()->routeIs('kalender.*') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
            :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Kalender Latihan</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">K</span>
            </a>

        {{-- =================================== --}}
        {{-- 3. MENU KHUSUS ATLET --}}
        {{-- =================================== --}}
        @elseif(Auth::user()->role == 'atlet')
            
            <div x-show="sidebarOpen" class="mt-4 mb-2 px-2 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Menu Atlet</div>
            <div x-show="!sidebarOpen" class="my-3 h-px bg-gray-100 mx-2"></div>

            {{-- Dashboard (SELALU BISA DIAKSES) --}}
            <a href="{{ route('atlet.dashboard') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('atlet.dashboard') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Dashboard</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">D</span>
            </a>

            {{-- Tagihan & SPP (SELALU BISA DIAKSES SUPAYA BISA BAYAR) --}}
            <a href="{{ route('atlet.tagihan.index') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('atlet.tagihan.index') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Tagihan & SPP</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">T</span>
            </a>
            
            {{-- ========================================== --}}
            {{-- BLOCK BARU: MENU INI DIKUNCI JIKA PENDING  --}}
            {{-- ========================================== --}}
            @if(Auth::user()->atlet && Auth::user()->atlet->status != 'Pending')
                {{-- Pengumuman --}}
                <a href="{{ route('notifikasi.index.user') }}" 
                   class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                          {{ request()->routeIs('notifikasi.index.user') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
                   :class="!sidebarOpen ? 'justify-center' : ''">
                    <span x-show="sidebarOpen" class="text-sm tracking-wide">Pengumuman</span>
                    <span x-show="!sidebarOpen" class="text-lg font-bold">P</span>
                </a>

                {{-- Kalender Latihan (Atlet) --}}
                <a href="{{ route('kalender.index') }}" 
                class="mt-1 flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                       {{ request()->routeIs('kalender.*') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
                :class="!sidebarOpen ? 'justify-center' : ''">
                    <span x-show="sidebarOpen" class="text-sm tracking-wide">Kalender Latihan</span>
                    <span x-show="!sidebarOpen" class="text-lg font-bold">K</span>
                </a>
            @endif

        {{-- =================================== --}}
        {{-- 4. MENU KHUSUS OWNER --}}
        {{-- =================================== --}}
        @elseif(Auth::user()->role == 'owner')
             {{-- Dashboard Owner --}}
            <a href="{{ route('owner.dashboard') }}" 
               class="flex items-center w-full py-3 px-3 rounded-xl border transition-all duration-200 group
                      {{ request()->routeIs('owner.dashboard') ? 'bg-green-600 text-white font-bold border-green-600' : 'bg-white text-gray-600 border-gray-100 hover:border-green-200 hover:bg-green-50 hover:text-green-700 font-medium' }}"
               :class="!sidebarOpen ? 'justify-center' : ''">
                <span x-show="sidebarOpen" class="text-sm tracking-wide">Dashboard Owner</span>
                <span x-show="!sidebarOpen" class="text-lg font-bold">D</span>
            </a>
        @endif

    </div>

    {{-- =================================== --}}
    {{-- LOGOUT BUTTON --}}
    {{-- =================================== --}}
    <div class="border-t border-gray-200 bg-gray-50 p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" 
                    title="Keluar Aplikasi"
                    class="flex items-center w-full py-3 px-3 text-red-600 hover:bg-red-100 hover:text-red-700 hover:border-red-200 border border-transparent rounded-xl transition-colors duration-200 group font-bold"
                    :class="!sidebarOpen ? 'justify-center' : ''">
                
                <span x-show="sidebarOpen" class="text-sm font-bold tracking-wide">KELUAR APLIKASI</span>
                
                <span x-show="!sidebarOpen" class="font-bold text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </span>
            </button>
        </form>
    </div>
</aside>