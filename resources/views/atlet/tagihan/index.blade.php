<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Keuangan & SPP') }}
        </h2>
    </x-slot>

    {{-- JAVASCRIPT: HITUNG TOTAL --}}
    <script>
        function hitungTotal() {
            let checkboxes = document.querySelectorAll('.tagihan-checkbox:checked');
            let total = 0;
            let count = 0;

            checkboxes.forEach((checkbox) => {
                total += parseInt(checkbox.getAttribute('data-nominal'));
                count++;
            });

            // Update Teks
            document.getElementById('total-bayar').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
            document.getElementById('info-item').innerText = count + ' Bulan Dipilih';

            // Tampilkan/Sembunyikan Panel Bawah
            let paySection = document.getElementById('payment-section');
            if (count > 0) {
                paySection.classList.remove('hidden');
            } else {
                paySection.classList.add('hidden');
            }
        }
    </script>

    <div class="w-full h-[calc(100vh-80px)] bg-gray-50 p-6 flex flex-col gap-6 overflow-hidden">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-full min-h-0">

            {{-- ==================================================== --}}
            {{-- KOLOM KIRI: FORM TAGIHAN (DESAIN ASLI + FOOTER RAPI) --}}
            {{-- ==================================================== --}}
            <form action="{{ route('atlet.pembayaran.bulk') }}" method="POST" class="flex flex-col h-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @csrf

                {{-- 1. HEADER --}}
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        📋 Daftar Tagihan
                    </h3>
                    @if($tagihan_belum->count() > 0)
                        <span class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full border border-red-100">
                            {{ $tagihan_belum->count() }} Belum Lunas
                        </span>
                    @endif
                </div>

                {{-- 2. LIST TAGIHAN (SCROLLABLE) --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-3 bg-gray-50/30">
                    @forelse($semua_tagihan as $tagihan)
                        @php
                            $isLunas = $tagihan->status == 'Lunas';
                            $isPending = $tagihan->status == 'Menunggu Verifikasi';
                            $isBelum = $tagihan->status == 'Belum Lunas';
                        @endphp

                        {{-- CARD ITEM (DESAIN ASLI YANG MAS SUKA) --}}
                        <div class="relative bg-white p-4 rounded-xl border transition-all flex items-center gap-4 select-none
                            {{ $isLunas ? 'border-green-200 bg-green-50/30' : '' }}
                            {{ $isPending ? 'border-yellow-200 bg-yellow-50/30' : '' }}
                            {{ $isBelum ? 'border-gray-200 hover:border-blue-400 cursor-pointer shadow-sm' : '' }}"
                            
                            {{-- Klik Card otomatis centang --}}
                            @if($isBelum) onclick="document.getElementById('chk-{{ $tagihan->id }}').click()" @endif
                        >
                            {{-- Checkbox / Ikon Status --}}
                            <div class="flex-shrink-0">
                                @if($isLunas)
                                    <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @elseif($isPending)
                                    <div class="w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center text-white shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                @else
                                    <input type="checkbox" name="tagihan_ids[]" value="{{ $tagihan->id }}" 
                                           id="chk-{{ $tagihan->id }}"
                                           data-nominal="{{ $tagihan->nominal }}"
                                           onclick="event.stopPropagation(); hitungTotal()"
                                           class="tagihan-checkbox w-6 h-6 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                                @endif
                            </div>

                            {{-- Info Bulan --}}
                            <div class="flex-1">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-500">
                                    SPP {{ $tagihan->tahun }}
                                </p>
                                <h4 class="text-base font-bold text-gray-800">
                                    {{ \Carbon\Carbon::create()->month($tagihan->bulan)->translatedFormat('F') }}
                                </h4>
                            </div>

                            {{-- Nominal --}}
                            <div class="text-right">
                                <p class="font-bold text-gray-800">
                                    Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                </p>
                                @if($isLunas)
                                    <span class="text-[10px] text-green-600 font-bold bg-green-100 px-2 py-0.5 rounded">LUNAS</span>
                                @elseif($isPending)
                                    <span class="text-[10px] text-yellow-600 font-bold bg-yellow-100 px-2 py-0.5 rounded">DIPROSES</span>
                                @else
                                    <span class="text-[10px] text-gray-400 bg-gray-100 px-2 py-0.5 rounded">Belum Bayar</span>
                                @endif
                            </div>
                        </div>

                    @empty
                        <div class="h-40 flex flex-col items-center justify-center text-gray-400 border-2 border-dashed border-gray-200 rounded-xl">
                            <p class="text-sm">Data tagihan belum tersedia.</p>
                        </div>
                    @endforelse
                </div>

                {{-- 3. PANEL PEMBAYARAN (FOOTER STATIS - MUNCUL JIKA ADA YG DICENTANG) --}}
                {{-- Ini posisinya di bawah LIST, jadi tidak akan menutupi teks apapun --}}
                <div id="payment-section" class="hidden border-t border-gray-200 bg-white p-4 animate-fade-in-up">
                    <div class="flex items-center justify-between bg-gray-900 text-white p-4 rounded-xl shadow-lg">
                        <div>
                            <p class="text-xs text-gray-400" id="info-item">0 Bulan Dipilih</p>
                            <h4 class="text-xl font-bold text-green-400" id="total-bayar">Rp 0</h4>
                        </div>
                        <button type="submit" class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-6 rounded-lg transition shadow-md flex items-center gap-2">
                            <span>Bayar Sekarang</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </div>

            </form>


            {{-- ==================================================== --}}
            {{-- KOLOM KANAN: RIWAYAT (TETAP SAMA) --}}
            {{-- ==================================================== --}}
            <div class="flex flex-col h-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-white">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2">
                        📜 Riwayat Pembayaran
                    </h3>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-6 py-3">Bulan</th>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3 text-right">Nominal</th>
                                <th class="px-6 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($history_bayar as $history)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ \Carbon\Carbon::create()->month($history->bulan)->translatedFormat('F') }} {{ $history->tahun }}
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        {{ $history->updated_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-700">
                                        Rp {{ number_format($history->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full border border-green-200">
                                            LUNAS
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                        Belum ada riwayat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Script Animasi CSS Sederhana --}}
    <style>
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.3s ease-out forwards;
        }
    </style>
</x-app-layout>