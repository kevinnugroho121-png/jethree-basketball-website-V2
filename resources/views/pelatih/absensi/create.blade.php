<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Penilaian & Absensi') }}
        </h2>
    </x-slot>

    {{-- WRAPPER UTAMA: FULL SCREEN --}}
    <div class="w-full h-[calc(100vh-80px)] flex flex-col bg-gray-50">
        
        {{-- 1. HEADER INFORMASI JADWAL (Fixed Top) --}}
        <div class="bg-white border-b border-gray-200 p-4 shadow-sm z-20">
            <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4">
                
                {{-- Info Kiri --}}
                <div class="flex items-center gap-4">
                    <div class="bg-indigo-600 text-white p-3 rounded-lg text-center min-w-[80px]">
                        <span class="block text-xs font-light uppercase">Kategori</span>
                        <span class="block text-xl font-bold">{{ $jadwal->kategori }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}
                        </h3>
                        <div class="text-sm text-gray-500 flex flex-wrap gap-x-4">
                            <span class="flex items-center gap-1">⏰ {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                            <span class="flex items-center gap-1">📍 {{ $jadwal->lokasi }}</span>
                            <span class="flex items-center gap-1">👤 Coach {{ $jadwal->pelatih->nama_lengkap ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Aksi Kanan (Cari & Set Hadir) --}}
                <div class="flex flex-col sm:flex-row gap-3 w-full xl:w-auto">
                    {{-- Input Pencarian --}}
                    <div class="relative w-full sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        
                        <input type="text" id="searchInput" placeholder="Cari nama atlet" class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm text-center">
                    </div>

                    {{-- Tombol Set Semua Hadir --}}
                    <button type="button" onclick="setSemuaHadir()" class="whitespace-nowrap bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded-lg shadow transition flex items-center justify-center gap-2">
                        ✅ Set Semua Hadir
                    </button>
                </div>
            </div>
        </div>

        {{-- 2. FORM UTAMA (Scrollable Area) --}}
        <form action="{{ route('absensi.store', $jadwal->id) }}" method="POST" class="flex-grow flex flex-col overflow-hidden relative">
            @csrf
            
            {{-- Container Tabel --}}
            <div class="flex-grow overflow-y-auto bg-white">
                <table class="w-full text-sm text-left text-gray-500 relative">
                    {{-- Sticky Header --}}
                    <thead class="text-xs text-white uppercase bg-gray-800 sticky top-0 z-10 shadow-md">
                        <tr>
                            <th scope="col" class="px-4 py-3 w-10 text-center">No</th>
                            <th scope="col" class="px-4 py-3 min-w-[200px]">Nama Atlet</th>
                            <th scope="col" class="px-2 py-3 text-center min-w-[220px]">Status Kehadiran</th>
                            
                            {{-- Group Nilai Skill --}}
                            <th scope="col" class="px-2 py-3 text-center w-20 bg-indigo-900 border-l border-indigo-700">Dribble<br><span class="text-[9px] opacity-70">(0-100)</span></th>
                            <th scope="col" class="px-2 py-3 text-center w-20 bg-indigo-900 border-l border-indigo-700">Pass<br><span class="text-[9px] opacity-70">(0-100)</span></th>
                            <th scope="col" class="px-2 py-3 text-center w-20 bg-indigo-900 border-l border-indigo-700">Shoot<br><span class="text-[9px] opacity-70">(0-100)</span></th>
                            
                            {{-- Group Nilai Perilaku --}}
                            <th scope="col" class="px-2 py-3 text-center w-20 bg-yellow-700 border-l border-yellow-600">IQ/Att<br><span class="text-[9px] opacity-70">(0-100)</span></th>
                            
                            <th scope="col" class="px-4 py-3 min-w-[200px]">Catatan Coach</th>
                        </tr>
                    </thead>
                    
                    <tbody id="tableBody" class="divide-y divide-gray-100">
                        @forelse($atlets as $index => $atlet)
                            @php
                                $absen = $atlet->absensis->where('jadwal_id', $jadwal->id)->first();
                            @endphp

                            <tr class="hover:bg-blue-50 transition border-b border-gray-100 search-item group">
                                {{-- No --}}
                                <td class="px-4 py-3 text-center font-medium text-gray-900 bg-gray-50 group-hover:bg-blue-50">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Nama --}}
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800 text-base name-text">{{ $atlet->nama_lengkap }}</span>
                                        <span class="text-xs text-gray-400">{{ $atlet->posisi ?? '-' }}</span>
                                    </div>
                                    <input type="hidden" name="absensi[{{ $atlet->id }}][atlet_id]" value="{{ $atlet->id }}">
                                </td>

                                {{-- Radio Button Kehadiran (Ganti Dropdown biar cepat) --}}
                                <td class="px-2 py-3">
                                    <div class="flex justify-center items-center gap-1 bg-gray-100 p-1 rounded-lg">
                                        <label class="cursor-pointer flex-1 text-center">
                                            <input type="radio" name="absensi[{{ $atlet->id }}][status]" value="Hadir" class="peer sr-only radio-hadir" {{ ($absen->status ?? 'Hadir') == 'Hadir' ? 'checked' : '' }}>
                                            <div class="px-2 py-1.5 rounded-md text-[10px] font-bold text-gray-500 peer-checked:bg-green-500 peer-checked:text-white transition hover:bg-gray-200">H</div>
                                        </label>
                                        <label class="cursor-pointer flex-1 text-center">
                                            <input type="radio" name="absensi[{{ $atlet->id }}][status]" value="Sakit" class="peer sr-only" {{ ($absen->status ?? '') == 'Sakit' ? 'checked' : '' }}>
                                            <div class="px-2 py-1.5 rounded-md text-[10px] font-bold text-gray-500 peer-checked:bg-yellow-400 peer-checked:text-white transition hover:bg-gray-200">S</div>
                                        </label>
                                        <label class="cursor-pointer flex-1 text-center">
                                            <input type="radio" name="absensi[{{ $atlet->id }}][status]" value="Izin" class="peer sr-only" {{ ($absen->status ?? '') == 'Izin' ? 'checked' : '' }}>
                                            <div class="px-2 py-1.5 rounded-md text-[10px] font-bold text-gray-500 peer-checked:bg-blue-500 peer-checked:text-white transition hover:bg-gray-200">I</div>
                                        </label>
                                        <label class="cursor-pointer flex-1 text-center">
                                            <input type="radio" name="absensi[{{ $atlet->id }}][status]" value="Alpha" class="peer sr-only" {{ ($absen->status ?? '') == 'Alpha' ? 'checked' : '' }}>
                                            <div class="px-2 py-1.5 rounded-md text-[10px] font-bold text-gray-500 peer-checked:bg-red-500 peer-checked:text-white transition hover:bg-gray-200">A</div>
                                        </label>
                                    </div>
                                </td>

                                {{-- Input Nilai (Skill) --}}
                                <td class="px-2 py-3">
                                    <input type="number" min="0" max="100" name="absensi[{{ $atlet->id }}][nilai_dribbling]" 
                                           value="{{ $absen->nilai_dribbling ?? '' }}" 
                                           class="w-full text-center text-sm font-bold text-indigo-700 bg-indigo-50 border-indigo-200 rounded focus:ring-indigo-500 focus:border-indigo-500 p-1.5" placeholder="-">
                                </td>
                                <td class="px-2 py-3">
                                    <input type="number" min="0" max="100" name="absensi[{{ $atlet->id }}][nilai_passing]" 
                                           value="{{ $absen->nilai_passing ?? '' }}" 
                                           class="w-full text-center text-sm font-bold text-indigo-700 bg-indigo-50 border-indigo-200 rounded focus:ring-indigo-500 focus:border-indigo-500 p-1.5" placeholder="-">
                                </td>
                                <td class="px-2 py-3">
                                    <input type="number" min="0" max="100" name="absensi[{{ $atlet->id }}][nilai_shooting]" 
                                           value="{{ $absen->nilai_shooting ?? '' }}" 
                                           class="w-full text-center text-sm font-bold text-indigo-700 bg-indigo-50 border-indigo-200 rounded focus:ring-indigo-500 focus:border-indigo-500 p-1.5" placeholder="-">
                                </td>

                                {{-- Input Nilai (IQ/Attitude) --}}
                                <td class="px-2 py-3 border-l border-gray-100">
                                    <input type="number" min="0" max="100" name="absensi[{{ $atlet->id }}][nilai_perilaku]" 
                                           value="{{ $absen->nilai_perilaku ?? '' }}" 
                                           class="w-full text-center text-sm font-bold text-yellow-700 bg-yellow-50 border-yellow-200 rounded focus:ring-yellow-500 focus:border-yellow-500 p-1.5" placeholder="-">
                                </td>

                                {{-- Catatan --}}
                                <td class="px-4 py-3">
                                    <input type="text" name="absensi[{{ $atlet->id }}][catatan]" 
                                           value="{{ $absen->catatan ?? '' }}" 
                                           class="w-full text-xs border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" placeholder="Catatan evaluasi...">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-10 text-gray-400 italic bg-gray-50">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <span>Tidak ada atlet di kategori ini.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 3. FOOTER STICKY (Tombol Simpan) --}}
            <div class="p-4 bg-white border-t border-gray-200 flex justify-end gap-3 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] z-30">
                <a href="{{ route('jadwal.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition">
                    Batal
                </a>
                @if($atlets->count() > 0)
                    <button type="submit" class="px-8 py-2.5 bg-indigo-700 text-white font-bold rounded-lg shadow-lg hover:bg-indigo-800 hover:shadow-xl transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        SIMPAN DATA
                    </button>
                @endif
            </div>

        </form>
    </div>

    {{-- SCRIPT PENCARIAN & BULK ACTION --}}
    <script>
        // 1. Cari Nama
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('.search-item');

        searchInput.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            tableRows.forEach(row => {
                const name = row.querySelector('.name-text').textContent.toLowerCase();
                if (name.includes(term)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // 2. Set Semua Hadir
        function setSemuaHadir() {
            // Hanya pilih baris yang terlihat (terkena filter search)
            const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
            
            visibleRows.forEach(row => {
                const radioHadir = row.querySelector('.radio-hadir');
                if (radioHadir) {
                    radioHadir.checked = true;
                }
            });

            // Feedback Visual Kecil di Tombol
            const btn = document.querySelector('button[onclick="setSemuaHadir()"]');
            const oriText = btn.innerHTML;
            btn.innerHTML = "✅ Berhasil!";
            setTimeout(() => { btn.innerHTML = oriText; }, 1000);
        }
    </script>
</x-app-layout>