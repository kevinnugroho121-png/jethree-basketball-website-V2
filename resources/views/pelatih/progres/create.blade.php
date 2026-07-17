<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Rapor Perkembangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- TABEL ACUAN PENILAIAN --}}
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 mx-4 sm:mx-0 rounded-r shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 w-full">
                        <h3 class="text-sm leading-5 font-bold text-blue-800">
                            Acuan Penilaian (Angka ke Huruf)
                        </h3>
                        <div class="mt-2 grid grid-cols-2 md:grid-cols-5 gap-2 text-center text-xs font-semibold">
                            <div class="bg-green-100 text-green-800 py-1 rounded border border-green-200">A : 85 - 100 <br> (Sangat Baik)</div>
                            <div class="bg-blue-100 text-blue-800 py-1 rounded border border-blue-200">B : 75 - 84 <br> (Baik)</div>
                            <div class="bg-yellow-100 text-yellow-800 py-1 rounded border border-yellow-200">C : 60 - 74 <br> (Cukup)</div>
                            <div class="bg-red-100 text-red-800 py-1 rounded border border-red-200">D : 40 - 59 <br> (Kurang)</div>
                            <div class="bg-gray-200 text-gray-800 py-1 rounded border border-gray-300">E : < 40 <br> (Sangat Kurang)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Header Identitas Atlet --}}
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100 mb-6 flex justify-between items-center">
                        <div>
                            <span class="text-xs text-indigo-500 font-bold uppercase tracking-wide">Nama Atlet</span>
                            <h3 class="font-bold text-2xl text-indigo-900">{{ $atlet->nama_lengkap }}</h3>
                            <p class="text-sm text-gray-600">Kategori: {{ $atlet->kategori }} | Posisi: {{ $atlet->posisi ?? '-' }}</p>
                            {{-- Info Total Sesi Latihan --}}
                            @if(isset($nilai['total_sesi']))
                                <span class="inline-block mt-2 px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">
                                    Data diambil dari {{ $nilai['total_sesi'] }} sesi latihan
                                </span>
                            @endif
                        </div>
                        <div class="text-4xl"></div>
                    </div>


                    {{-- Form Input (Sudah Disterilkan dari Tag Bocor & Grid Dibuat Simetris) --}}
                    <form action="{{ route('pelatih.progres.store') }}" method="POST">
                        @csrf
                        {{-- Data Hidden --}}
                        <input type="hidden" name="atlet_id" value="{{ $atlet->id }}">
                        
                        @if(isset($pelatih))
                            <input type="hidden" name="pelatih_id" value="{{ $pelatih->id }}">
                        @endif

                        {{-- Tanggal --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Penilaian</label>
                            <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full md:w-1/2 h-10 text-sm rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        </div>

                        <hr class="mb-6 border-gray-200">

                        {{-- Input 4 Nilai Rapor --}}
                        <h4 class="font-bold text-lg mb-4 text-gray-800 flex items-center gap-2">
                            Penilaian 
                            <span class="text-sm font-normal text-gray-500">(Otomatis terisi rata-rata latihan)</span>
                        </h4>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                            {{-- Teknik --}}
                            <div>
                                <label class="block text-xs font-bold text-blue-700 uppercase tracking-wider mb-1 flex justify-between">
                                    Teknik 
                                    <span id="hasil_teknik" class="text-gray-400 font-extrabold text-sm">-</span>
                                </label>
                                <span class="text-[11px] text-gray-400 block mb-2">(Dribble, Shoot)</span>
                                <input type="number" id="teknik" name="teknik" min="0" max="100" 
                                       value="{{ old('teknik', $nilai['teknik'] ?? 0) }}" 
                                       oninput="hitungHuruf('teknik')" 
                                       class="w-full h-11 text-center text-xl font-bold text-blue-800 rounded-lg border-blue-200 focus:border-blue-500 shadow-sm" placeholder="0" required>
                            </div>

                            {{-- Fisik --}}
                            <div>
                                <label class="block text-xs font-bold text-green-700 uppercase tracking-wider mb-1 flex justify-between">
                                    Fisik
                                    <span id="hasil_fisik" class="text-gray-400 font-extrabold text-sm">-</span>
                                </label>
                                <span class="text-[11px] text-gray-400 block mb-2">(Speed, Power)</span>
                                <input type="number" id="fisik" name="fisik" min="0" max="100" 
                                       value="{{ old('fisik', $nilai['fisik'] ?? 0) }}" 
                                       oninput="hitungHuruf('fisik')" 
                                       class="w-full h-11 text-center text-xl font-bold text-green-800 rounded-lg border-green-200 focus:border-green-500 shadow-sm" placeholder="0" required>
                            </div>

                            {{-- Mental --}}
                            <div>
                                <label class="block text-xs font-bold text-yellow-700 uppercase tracking-wider mb-1 flex justify-between">
                                    Mental
                                    <span id="hasil_mental" class="text-gray-400 font-extrabold text-sm">-</span>
                                </label>
                                <span class="text-[11px] text-gray-400 block mb-2">(Disiplin, Fokus)</span>
                                <input type="number" id="mental" name="mental" min="0" max="100" 
                                       value="{{ old('mental', $nilai['mental'] ?? 0) }}" 
                                       oninput="hitungHuruf('mental')" 
                                       class="w-full h-11 text-center text-xl font-bold text-yellow-800 rounded-lg border-yellow-200 focus:border-yellow-500 shadow-sm" placeholder="0" required>
                            </div>

                            {{-- Taktik --}}
                            <div>
                                <label class="block text-xs font-bold text-purple-700 uppercase tracking-wider mb-1 flex justify-between">
                                    Taktik
                                    <span id="hasil_taktik" class="text-gray-400 font-extrabold text-sm">-</span>
                                </label>
                                <span class="text-[11px] text-gray-400 block mb-2">(IQ Game, Posisi)</span>
                                <input type="number" id="taktik" name="taktik" min="0" max="100" 
                                       value="{{ old('taktik', $nilai['taktik'] ?? 0) }}" 
                                       oninput="hitungHuruf('taktik')" 
                                       class="w-full h-11 text-center text-xl font-bold text-purple-800 rounded-lg border-purple-200 focus:border-purple-500 shadow-sm" placeholder="0" required>
                            </div>
                        </div>

                        {{-- Catatan --}}
                        <div class="mb-6">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Catatan Evaluasi (Opsional)</label>
                            <textarea name="catatan" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Berikan masukan untuk atlet ini...">{{ old('catatan') }}</textarea>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end items-center gap-3 pt-4 border-t border-gray-100">
                            <a href="{{ route('pelatih.progres.index') }}" 
                               class="h-10 px-5 inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-bold text-xs uppercase tracking-wider transition">
                               Batal
                            </a>
                            <button type="submit" 
                               class="h-10 px-6 inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-xs uppercase tracking-wider shadow-md transition">
                               💾 Simpan Rapor
                            </button>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        function hitungHuruf(idInput) {
            // 1. Ambil nilai
            let inputElement = document.getElementById(idInput);
            if (!inputElement) return; // Jaga-jaga error null

            let angka = inputElement.value;
            let labelHasil = document.getElementById('hasil_' + idInput);

            // 2. Logic Penilaian
            let huruf = '-';
            let warnaClass = 'text-gray-400';

            if (angka !== "") {
                angka = parseInt(angka);
                
                if (angka >= 85) {
                    huruf = 'A';
                    warnaClass = 'text-green-600'; 
                } else if (angka >= 75) {
                    huruf = 'B';
                    warnaClass = 'text-blue-600'; 
                } else if (angka >= 60) {
                    huruf = 'C';
                    warnaClass = 'text-yellow-600'; 
                } else if (angka >= 40) {
                    huruf = 'D';
                    warnaClass = 'text-red-600'; 
                } else {
                    huruf = 'E';
                    warnaClass = 'text-gray-600'; 
                }
            }

            // 3. Tampilkan
            labelHasil.innerText = huruf;
            labelHasil.className = 'font-extrabold text-lg ' + warnaClass;
        }

        // [BARU] Jalankan script saat halaman selesai dimuat agar nilai otomatis langsung dikonversi ke huruf
        document.addEventListener("DOMContentLoaded", function() {
            hitungHuruf('teknik');
            hitungHuruf('fisik');
            hitungHuruf('mental');
            hitungHuruf('taktik');
        });
    </script>
</x-app-layout>