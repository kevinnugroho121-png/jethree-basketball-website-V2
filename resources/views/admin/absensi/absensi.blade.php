<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Input Penilaian & Absensi
        </h2>
    </x-slot>

    {{-- CSS TAMBAHAN --}}
    <style>
        .radio-hidden { position: absolute; opacity: 0; width: 0; height: 0; }
        .radio-label {
            width: 2rem; height: 2rem; 
            display: flex; align-items: center; justify-content: center;
            border-radius: 0.25rem; border: 1px solid #d1d5db;
            color: #6b7280; font-weight: bold; cursor: pointer; transition: all 0.2s;
        }
        .radio-label:not(.disabled):hover { background-color: #f3f4f6; }

        input[value="H"]:checked + .radio-label { background-color: #16a34a; color: white; border-color: #16a34a; }
        input[value="S"]:checked + .radio-label { background-color: #facc15; color: white; border-color: #facc15; }
        input[value="I"]:checked + .radio-label { background-color: #3b82f6; color: white; border-color: #3b82f6; }
        input[value="A"]:checked + .radio-label { background-color: #dc2626; color: white; border-color: #dc2626; }
        
        .disabled { opacity: 0.6; cursor: not-allowed !important; pointer-events: none; }
        .bg-disabled { background-color: #f3f4f6; color: #9ca3af; }
    </style>

    @php
        $userRole = Auth::user()->role;
        $isPelatih = ($userRole === 'pelatih');
        $isAdmin = ($userRole === 'admin' || $userRole === 'owner');

        $actionUrl = $isPelatih ? route('pelatih.absensi.store', $jadwal->id) : route('absensi.store', $jadwal->id);
        $cancelUrl = $isPelatih ? route('pelatih.absensi.index') : route('absensi.index');
        
        $isEditMode = request('edit') == 'true';
        
        // LOGIKA WAKTU TENGGANG 7 HARI
        $tglLatihan = \Carbon\Carbon::parse($jadwal->tanggal)->startOfDay();
        $hariIni = \Carbon\Carbon::now()->startOfDay();
        $selisihHari = $tglLatihan->diffInDays($hariIni, false);
        $sisaHari = 7 - $selisihHari;
        $isExpired = $selisihHari > 7;

        // LOGIKA PENGUNCIAN
        if ($isPelatih && $isExpired) {
            $isLocked = true; // Mutlak Terkunci untuk pelatih jika lewat 7 hari
        } else {
            $isLocked = $sudahDiabsen && !$isEditMode; // Logika normal
        }
    @endphp

    <div class="py-6 px-4">
        <div class="max-w-7xl mx-auto">
            
            {{-- INFORMASI JADWAL --}}
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border-l-4 border-blue-600 flex flex-col md:flex-row items-start gap-4 justify-between">
                <div class="flex gap-4">
                    <div class="bg-blue-600 text-white font-bold p-3 rounded text-center min-w-[80px]">
                        <span class="text-xs block uppercase">Kategori</span>
                        <span class="text-xl">{{ $jadwal->kategori }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($jadwal->tanggal)->isoFormat('dddd, D MMMM Y') }}
                        </h3>
                        <div class="text-gray-600 text-sm mt-1 flex flex-wrap gap-4">
                            <span class="flex items-center gap-1">⏰ {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</span>
                            <span class="flex items-center gap-1">📍 {{ $jadwal->lokasi }}</span>
                            <span class="flex items-center gap-1">👤 Coach: {{ $jadwal->pelatih->nama_lengkap ?? 'Tanpa Pelatih' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    {{-- Indikator Status Absensi --}}
                    @if($isLocked)
                        <div class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg border border-gray-300 text-sm font-bold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            DATA TERKUNCI
                        </div>
                    @else
                        <div class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg border border-yellow-200 text-sm font-bold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            MODE PENGISIAN
                        </div>
                    @endif
                </div>
            </div>

            {{-- NOTIFIKASI PERINGATAN MASA TENGGANG --}}
            @if($isPelatih)
                @if($isExpired)
                    <div class="mb-6 px-5 py-4 bg-red-100 text-red-800 rounded-lg border-l-4 border-red-600 text-sm flex items-center gap-3 shadow-sm">
                        <svg class="w-8 h-8 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <strong class="block text-base">WAKTU HABIS!</strong> 
                            Anda telah melewati batas 7 hari masa pengisian. Akses ini telah dikunci oleh sistem. Silakan hubungi Admin jika ingin melakukan pengisian susulan.
                        </div>
                    </div>
                @elseif(!$sudahDiabsen && $selisihHari >= 0)
                    <div class="mb-6 px-5 py-4 bg-orange-50 text-orange-800 rounded-lg border-l-4 border-orange-500 text-sm flex items-center gap-3 shadow-sm">
                        <svg class="w-8 h-8 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <strong class="block text-base">Peringatan Masa Tenggang!</strong> 
                            Batas waktu pengisian sisa <b>{{ $sisaHari }} hari</b> lagi. Pastikan Anda menyimpan data absensi sebelum sistem menguncinya otomatis.
                        </div>
                    </div>
                @endif
            @endif

            @if($isAdmin && $isExpired)
                <div class="mb-6 px-5 py-3 bg-blue-50 text-blue-800 rounded-lg border border-blue-200 text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <strong>God Mode:</strong> Jadwal ini sudah expired untuk pelatih, namun Anda dapat mengubahnya sebagai Admin.
                </div>
            @endif

            <form action="{{ $actionUrl }}" method="POST" id="absensiForm">
                @csrf
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                    
                    {{-- TOOLBAR --}}
                    <div class="p-4 bg-gray-50 border-b border-gray-200 flex flex-col md:flex-row gap-3 justify-between items-center">
                        <input type="text" id="searchInput" placeholder="Cari nama atlet..." class="w-full md:w-1/3 text-sm border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        
                        @if(!$isLocked)
                            <button type="button" onclick="setAllPresent()" class="w-full md:w-auto px-4 py-2 bg-green-600 text-white text-sm font-bold rounded hover:bg-green-700 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Set Semua Hadir
                            </button>
                        @endif
                    </div>

                    {{-- TABLE --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-white uppercase bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 w-10">No</th>
                                    <th class="px-4 py-3 min-w-[200px]">Nama Atlet</th>
                                    <th class="px-4 py-3 text-center min-w-[180px]">Status Kehadiran</th>
                                    <th class="px-2 py-3 text-center bg-indigo-900 w-20 text-white" title="Wajib diisi jika Hadir">Dribble*</th>
                                    <th class="px-2 py-3 text-center bg-indigo-800 w-20 text-white" title="Wajib diisi jika Hadir">Pass*</th>
                                    <th class="px-2 py-3 text-center bg-indigo-900 w-20 text-white" title="Wajib diisi jika Hadir">Shoot*</th>
                                    <th class="px-2 py-3 text-center bg-yellow-600 text-black w-20 text-white" title="Wajib diisi jika Hadir">IQ/ATT*</th>
                                    <th class="px-4 py-3 min-w-[200px]">Catatan Coach</th>
                                </tr>
                            </thead>
                            <tbody id="atletTable">
                                @forelse($atlets as $index => $atlet)
                                    @php
                                        $old = $existingAbsensi[$atlet->id] ?? null;
                                        $status = $old->status ?? ''; 
                                    @endphp
                                    <tr class="border-b hover:bg-gray-50 transition param-row" data-id="{{ $atlet->id }}">
                                        <td class="px-4 py-4 text-center">{{ $index + 1 }}</td>
                                        <td class="px-4 py-4 font-medium text-gray-900 search-name">
                                            {{ $atlet->nama_lengkap }}
                                            <div class="text-xs text-gray-500 font-normal">{{ $atlet->posisi ?? '-' }}</div>
                                            
                                            @if($old)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 mt-1 uppercase border border-green-200">
                                                    ✓ Terabsen
                                                </span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-4 py-4">
                                            <div class="flex justify-center gap-1">
                                                @foreach(['H', 'S', 'I', 'A'] as $s)
                                                    <label class="cursor-pointer relative {{ $isLocked ? 'disabled' : '' }}">
                                                        <input type="radio" 
                                                               name="data[{{ $atlet->id }}][status]" 
                                                               value="{{ $s }}" 
                                                               class="radio-hidden status-radio" 
                                                               {{ $status == $s ? 'checked' : '' }}
                                                               {{ $isLocked ? 'disabled' : '' }}
                                                               onchange="toggleInputs({{ $atlet->id }}, '{{ $s }}')">
                                                        <div class="radio-label {{ $isLocked ? 'disabled' : '' }}">{{ $s }}</div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>

                                        <td class="px-2 py-4">
                                            <input type="number" name="data[{{ $atlet->id }}][dribble]" value="{{ $old->nilai_dribble ?? '' }}" 
                                                   min="0" max="100" class="w-full text-center text-xs border-gray-300 rounded bg-indigo-50 p-1 score-input {{ $isLocked ? 'bg-disabled' : '' }}" 
                                                   placeholder="-" {{ $isLocked ? 'readonly' : '' }}>
                                        </td>
                                        <td class="px-2 py-4">
                                            <input type="number" name="data[{{ $atlet->id }}][pass]" value="{{ $old->nilai_pass ?? '' }}" 
                                                   min="0" max="100" class="w-full text-center text-xs border-gray-300 rounded bg-indigo-50 p-1 score-input {{ $isLocked ? 'bg-disabled' : '' }}" 
                                                   placeholder="-" {{ $isLocked ? 'readonly' : '' }}>
                                        </td>
                                        <td class="px-2 py-4">
                                            <input type="number" name="data[{{ $atlet->id }}][shoot]" value="{{ $old->nilai_shoot ?? '' }}" 
                                                   min="0" max="100" class="w-full text-center text-xs border-gray-300 rounded bg-indigo-50 p-1 score-input {{ $isLocked ? 'bg-disabled' : '' }}" 
                                                   placeholder="-" {{ $isLocked ? 'readonly' : '' }}>
                                        </td>
                                        <td class="px-2 py-4">
                                            <input type="number" name="data[{{ $atlet->id }}][iq]" value="{{ $old->nilai_iq ?? '' }}" 
                                                   min="0" max="100" class="w-full text-center text-xs border-gray-300 rounded bg-yellow-50 p-1 score-input {{ $isLocked ? 'bg-disabled' : '' }}" 
                                                   placeholder="-" {{ $isLocked ? 'readonly' : '' }}>
                                        </td>
                                        
                                        <td class="px-4 py-4">
                                            <input type="text" name="data[{{ $atlet->id }}][catatan]" value="{{ $old->catatan ?? '' }}" 
                                                   class="w-full text-xs border-gray-300 rounded {{ $isLocked ? 'bg-disabled' : '' }}" 
                                                   placeholder="Evaluasi..." {{ $isLocked ? 'readonly' : '' }}>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center py-8 text-gray-500">Tidak ada atlet di kategori ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- FOOTER TOMBOL --}}
                    <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 sticky bottom-0 z-10">
                        <a href="{{ $cancelUrl }}" class="px-5 py-2.5 bg-white text-gray-700 font-medium rounded border border-gray-300 hover:bg-gray-100 transition">
                            Kembali
                        </a>
                        
                        @if($isLocked)
                            @if($isPelatih && $isExpired)
                                {{-- JIKA PELATIH DAN SUDAH EXPIRED, TOMBOL EDIT DIHILANGKAN --}}
                                <span class="px-5 py-2.5 bg-gray-200 text-gray-500 font-bold rounded cursor-not-allowed border border-gray-300">
                                    🔒 AKSES DITUTUP
                                </span>
                            @else
                                {{-- TOMBOL EDIT (Hanya Muncul Jika Terkunci Normal / Admin) --}}
                                <a href="?edit=true" class="px-5 py-2.5 bg-blue-600 text-white font-bold rounded shadow-md hover:bg-blue-700 transition flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    EDIT DATA ABSENSI
                                </a>
                            @endif
                        @else
                            {{-- TOMBOL SIMPAN --}}
                            <button type="submit" class="px-5 py-2.5 bg-indigo-700 text-white font-bold rounded shadow-md hover:bg-indigo-800 transition flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                {{ $isEditMode ? 'SIMPAN PERUBAHAN' : 'SIMPAN DATA' }}
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#atletTable tr');
            rows.forEach(row => {
                let nameCell = row.querySelector('.search-name');
                if (nameCell) {
                    row.style.display = nameCell.textContent.toLowerCase().includes(filter) ? '' : 'none';
                }
            });
        });

        function setAllPresent() {
            document.querySelectorAll('#atletTable tr').forEach(row => {
                if (row.style.display !== 'none') {
                    let hadirRadio = row.querySelector('input[value="H"]');
                    if (hadirRadio && !hadirRadio.disabled) {
                        hadirRadio.checked = true;
                        let atletId = row.getAttribute('data-id');
                        toggleInputs(atletId, 'H');
                    }
                }
            });
        }

        function toggleInputs(atletId, status) {
            let row = document.querySelector(`tr[data-id="${atletId}"]`);
            if(!row) return;

            let inputs = row.querySelectorAll('.score-input');
            
            if (status === 'H') {
                inputs.forEach(input => {
                    input.required = true;
                    input.classList.remove('bg-gray-100');
                    input.classList.add('bg-white'); 
                });
            } else {
                inputs.forEach(input => {
                    input.required = false;
                    input.value = '';
                    input.classList.add('bg-gray-100');
                    input.classList.remove('bg-white');
                });
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            let radios = document.querySelectorAll('.status-radio:checked');
            radios.forEach(radio => {
                let atletId = radio.name.match(/\d+/)[0]; 
                toggleInputs(atletId, radio.value);
            });
        });

        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", showConfirmButton: false, timer: 2000 });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}" });
        @endif
    </script>
</x-app-layout>