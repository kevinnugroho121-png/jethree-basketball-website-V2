<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Absensi & Penilaian Performa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- HEADER INFO JADWAL --}}
            <div class="mb-6 bg-indigo-50 border-l-4 border-indigo-500 p-4 rounded shadow-sm flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l, d F Y') }}
                    </h3>
                    <p class="text-gray-600">
                        Kategori: <span class="font-bold">{{ $jadwal->kategori }}</span> | 
                        Coach: <span class="font-bold">{{ $jadwal->pelatih->nama_lengkap ?? '-' }}</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        Pukul {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB
                    </p>
                </div>
                <div class="text-right mt-2 md:mt-0">
                    <span class="text-xs font-bold text-gray-500 uppercase">Mode Penilaian</span>
                    <p class="text-sm font-semibold text-indigo-700">Evaluasi Skill (0-100) & IQ Basket</p>
                </div>
            </div>

            {{-- FORM PENILAIAN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('absensi.store', $jadwal->id) }}" method="POST" class="p-6">
                    @csrf
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            {{-- JUDUL KOLOM --}}
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs uppercase w-10">No</th>
                                    <th class="px-3 py-3 text-left text-xs uppercase w-48">Nama Atlet</th>
                                    <th class="px-3 py-3 text-center text-xs uppercase w-32">Kehadiran</th>
                                    
                                    {{-- KOLOM NILAI SKILL (Background Biru Gelap) --}}
                                    <th class="px-2 py-3 text-center text-xs uppercase w-20 bg-indigo-900">Dribble <br><span class="text-[10px] text-gray-300">(0-100)</span></th>
                                    <th class="px-2 py-3 text-center text-xs uppercase w-20 bg-indigo-900">Pass <br><span class="text-[10px] text-gray-300">(0-100)</span></th>
                                    <th class="px-2 py-3 text-center text-xs uppercase w-20 bg-indigo-900">Shoot <br><span class="text-[10px] text-gray-300">(0-100)</span></th>
                                    
                                    {{-- KOLOM NILAI PERILAKU (Background Kuning Gelap) --}}
                                    <th class="px-2 py-3 text-center text-xs uppercase w-20 bg-yellow-700 text-white">IQ/Att <br><span class="text-[10px] text-gray-200">(0-100)</span></th>
                                    
                                    <th class="px-3 py-3 text-left text-xs uppercase">Catatan Coach</th>
                                </tr>
                            </thead>

                            {{-- ISI TABEL --}}
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @forelse($atlets as $index => $atlet)
                                    @php
                                        // Ambil data nilai lama jika sudah pernah diisi
                                        $absen = $atlet->absensis->where('jadwal_id', $jadwal->id)->first();
                                    @endphp

                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-3 py-4 text-center">{{ $loop->iteration }}</td>
                                        
                                        {{-- NAMA ATLET --}}
                                        <td class="px-3 py-4 font-bold text-gray-700">
                                            {{ $atlet->nama_lengkap }}
                                            <div class="text-xs text-gray-400 font-normal">{{ $atlet->posisi ?? '-' }}</div>
                                        </td>

                                        {{-- 1. KEHADIRAN (Dropdown) --}}
                                        <td class="px-3 py-4">
                                            <select name="absensi[{{ $atlet->id }}][status]" class="w-full text-xs rounded border-gray-300 focus:ring-indigo-500">
                                                <option value="Hadir" {{ ($absen->status ?? '') == 'Hadir' ? 'selected' : '' }}>Hadir ✅</option>
                                                <option value="Sakit" {{ ($absen->status ?? '') == 'Sakit' ? 'selected' : '' }}>Sakit 🤒</option>
                                                <option value="Izin"  {{ ($absen->status ?? '') == 'Izin' ? 'selected' : '' }}>Izin 📩</option>
                                                <option value="Alpha" {{ ($absen->status ?? '') == 'Alpha' ? 'selected' : '' }}>Alpha ❌</option>
                                            </select>
                                        </td>

                                        {{-- 2. INPUT NILAI SKILL --}}
                                        {{-- Dribble --}}
                                        <td class="px-2 py-4">
                                            <input type="number" min="0" max="100" name="absensi[{{ $atlet->id }}][nilai_dribbling]" 
                                                value="{{ $absen->nilai_dribbling ?? '' }}" 
                                                class="w-full text-center text-xs rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="-">
                                        </td>
                                        {{-- Passing --}}
                                        <td class="px-2 py-4">
                                            <input type="number" min="0" max="100" name="absensi[{{ $atlet->id }}][nilai_passing]" 
                                                value="{{ $absen->nilai_passing ?? '' }}" 
                                                class="w-full text-center text-xs rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="-">
                                        </td>
                                        {{-- Shooting --}}
                                        <td class="px-2 py-4">
                                            <input type="number" min="0" max="100" name="absensi[{{ $atlet->id }}][nilai_shooting]" 
                                                value="{{ $absen->nilai_shooting ?? '' }}" 
                                                class="w-full text-center text-xs rounded border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="-">
                                        </td>
                                        {{-- IQ Basket / Attitude (Warna beda biar notice) --}}
                                        <td class="px-2 py-4 bg-yellow-50">
                                            <input type="number" min="0" max="100" name="absensi[{{ $atlet->id }}][nilai_perilaku]" 
                                                value="{{ $absen->nilai_perilaku ?? '' }}" 
                                                class="w-full text-center text-xs rounded border-yellow-400 focus:border-yellow-600 font-bold text-yellow-700 focus:ring-yellow-500" placeholder="-">
                                        </td>

                                        {{-- 3. CATATAN --}}
                                        <td class="px-3 py-4">
                                            <input type="text" name="absensi[{{ $atlet->id }}][catatan]" 
                                                value="{{ $absen->catatan ?? '' }}" 
                                                class="w-full text-xs rounded border-gray-300 shadow-sm focus:border-indigo-500" placeholder="Evaluasi singkat...">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-6 text-gray-500 italic">
                                            Tidak ada atlet aktif di kategori <strong>{{ $jadwal->kategori }}</strong>.<br>
                                            Silakan tambahkan atlet terlebih dahulu.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('jadwal.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 font-bold text-sm transition">
                            Batal
                        </a>
                        
                        @if($atlets->count() > 0)
                            <button type="submit" class="px-6 py-2 bg-indigo-700 text-white rounded-md hover:bg-indigo-800 font-bold text-sm shadow-lg transition transform hover:scale-105">
                                💾 SIMPAN PENILAIAN & ABSENSI
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>