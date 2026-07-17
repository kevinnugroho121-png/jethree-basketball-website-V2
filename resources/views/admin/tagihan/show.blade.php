<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-2">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                📂 Detail SPP: <span class="text-green-600">{{ $atlet->nama_lengkap }}</span>
            </h2>
            <a href="{{ route('tagihan.index') }}" class="inline-flex items-center gap-1 h-9 px-4 bg-gray-600 hover:bg-gray-700 text-white text-sm font-bold rounded-lg shadow-md transition">
                &laquo; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-3">
        <div class="w-full px-2 max-w-7xl mx-auto">

            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="mb-3 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 text-sm shadow-sm rounded flex justify-between items-center">
                    <span>✅ {{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 font-bold px-2 hover:text-green-900">×</button>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-3 bg-red-100 border-l-4 border-red-500 text-red-700 p-3 text-sm shadow-sm rounded flex justify-between items-center">
                    <span>❌ {{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-700 font-bold px-2 hover:text-red-900">×</button>
                </div>
            @endif

            {{-- ========================================== --}}
            {{-- KOTAK 1: FORM BUAT TAGIHAN MASAL (CENTANG) --}}
            {{-- ========================================== --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden mb-5 border border-blue-100">
                <div class="bg-blue-50 px-4 py-3 border-b border-blue-100 flex items-center justify-between">
                    <h3 class="font-bold text-blue-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Buat Tagihan Cepat (Masal)
                    </h3>
                    <span class="text-xs text-blue-600 bg-blue-100 px-2 py-1 rounded font-semibold border border-blue-200">
                        Otomatis Kirim Notif WA 📱
                    </span>
                </div>
                
                <form action="{{ route('tagihan.storeBulk') }}" method="POST" class="p-4" onsubmit="return confirm('Yakin ingin membuat tagihan untuk bulan-bulan yang dicentang? Notifikasi WA akan otomatis terkirim ke Orang Tua.');">
                    @csrf
                    <input type="hidden" name="atlet_id" value="{{ $atlet->id }}">
                    
                    <div class="flex flex-wrap gap-4 mb-4">
                        <div class="w-full sm:w-1/3">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Tahun Tagihan <span class="text-red-500">*</span></label>
                            <select name="tahun" id="pilih_tahun" required class="w-full h-9 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                @for($y = date('Y') - 1; $y <= 2030; $y++)
                                    <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="w-full sm:w-1/3">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Nominal per Bulan (Rp) <span class="text-red-500">*</span></label>
                            <input type="number" name="nominal" value="100000" min="0" required class="w-full h-9 rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    {{-- [REVISI] JUDUL & TOMBOL CENTANG SEMUA --}}
                    <div class="flex justify-between items-end mb-2">
                        <label class="block text-xs font-bold text-gray-700">Pilih Bulan <span class="text-red-500">*</span></label>
                        <button type="button" id="btn_centang_semua" class="text-[11px] font-bold text-blue-700 bg-blue-100 border border-blue-300 px-3 py-1.5 rounded-lg hover:bg-blue-200 transition shadow-sm flex items-center gap-1">
                            ☑️ Centang Semua
                        </button>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
                        @php
                            $namaBulan = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                        @endphp
                        @foreach($namaBulan as $angka => $nama)
                            <label id="label_bulan_{{ $angka }}" class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg transition label-bulan-centang">
                                <input type="checkbox" id="checkbox_bulan_{{ $angka }}" name="bulan[]" value="{{ $angka }}" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 checkbox-input">
                                <span id="text_bulan_{{ $angka }}" class="text-sm text-gray-700 font-medium">{{ $nama }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" id="btn_simpan_masal" class="h-10 px-5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow-md transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Simpan & Kirim WA
                        </button>
                    </div>
                </form>
            </div>

            {{-- ========================================== --}}
            {{-- KOTAK 2: TABEL RIWAYAT TAGIHAN LAMA --}}
            {{-- ========================================== --}}
            <div class="bg-white shadow-md rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">Riwayat Tagihan & Pembayaran</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-green-600 text-white">
                                <th class="px-3 py-3 text-center font-semibold w-10">No</th>
                                <th class="px-3 py-3 font-semibold text-center">Bulan/Tahun</th>
                                <th class="px-4 py-3 font-semibold text-right">Nominal</th>
                                <th class="px-3 py-3 font-semibold text-center">Tgl Bayar</th>
                                <th class="px-3 py-3 font-semibold text-center w-28">Status</th>
                                <th class="px-3 py-3 font-semibold text-center w-32">Metode</th> <!-- 💡 Tambahan Kolom Baru -->
                                <th class="px-3 py-3 font-semibold text-center w-44">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tagihans as $index => $tagihan)
                                @php
                                    $no_hp = preg_replace('/^0/', '62', $atlet->no_hp ?? '0');
                                    $pesanTagih = "Halo Orang Tua dari *" . ($atlet->nama_lengkap ?? 'Atlet') . "*,\n\nKami mengingatkan tagihan *SPP Bulan " . $tagihan->bulan . "/" . $tagihan->tahun . "* sebesar *Rp " . number_format($tagihan->nominal, 0, ',', '.') . "* statusnya BELUM LUNAS.\nMohon segera melakukan pembayaran ya. Terima kasih! 🙏🏀";
                                    $pesanLunas = "Halo Orang Tua dari *" . ($atlet->nama_lengkap ?? 'Atlet') . "*,\n\nTerima kasih! Pembayaran *SPP Bulan " . $tagihan->bulan . "/" . $tagihan->tahun . "* sebesar *Rp " . number_format($tagihan->nominal, 0, ',', '.') . "* telah kami terima (LUNAS).\nSemangat latihannya! 💪🔥";
                                @endphp
                                <tr class="hover:bg-green-50 transition-colors duration-150 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">

                                    <td class="px-3 py-2 text-center text-gray-500 font-medium">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="px-3 py-2 font-bold text-gray-800 text-center">
                                        {{ $namaBulan[$tagihan->bulan] }} {{ $tagihan->tahun }}
                                    </td>

                                    <td class="px-4 py-2 text-right font-mono font-bold text-gray-900">
                                        Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}
                                    </td>
                                    
                                    <td class="px-3 py-2 text-center text-xs text-gray-600">
                                        {{ $tagihan->tanggal_lunas ? \Carbon\Carbon::parse($tagihan->tanggal_lunas)->format('d/m/Y') : ($tagihan->status == 'Lunas' ? \Carbon\Carbon::parse($tagihan->updated_at)->format('d/m/Y') : '-') }}
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        @if($tagihan->status == 'Lunas')
                                            <span class="inline-block text-[10px] font-bold px-2 py-1 rounded bg-green-100 text-green-800 border border-green-200">LUNAS</span>
                                        @elseif($tagihan->status == 'Menunggu Verifikasi')
                                            <span class="inline-block text-[10px] font-bold px-2 py-1 rounded bg-yellow-100 text-yellow-800 border border-yellow-200">DIPROSES</span>
                                        @else
                                            <span class="inline-block text-[10px] font-bold px-2 py-1 rounded bg-red-100 text-red-800 border border-red-200">BELUM</span>
                                        @endif
                                    </td>

                                    <!-- 💡 Kolom Metode Pembayaran Baru (Deteksi Lebih Akurat) -->
                                    <td class="px-3 py-2 text-center text-xs">
                                        @if($tagihan->status == 'Lunas')
                                            {{-- SINKRONISASI: Jika ada file bukti gambar ATAU metodenya klop, maka itu pasti Manual --}}
                                            @if($tagihan->bukti_pembayaran || in_array($tagihan->metode_pembayaran, ['Tunai (Cash)', 'Transfer Bank', 'Transfer (Via Mobile)']))
                                                <span class="inline-block font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-700 border border-gray-200">Manual</span>
                                            @else
                                                <span class="inline-block font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-700 border border-blue-200">Midtrans</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td class="px-3 py-2 text-center">
                                        <div class="flex justify-center items-center gap-1.5 flex-wrap">

                                            {{-- Tombol WA --}}
                                            @if($tagihan->status == 'Belum Lunas')
                                                <a href="https://wa.me/{{ $no_hp }}?text={{ urlencode($pesanTagih) }}" target="_blank"
                                                   class="flex items-center justify-center w-7 h-7 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-sm" title="Kirim Tagihan via WA"></a>
                                            @else
                                                <a href="https://wa.me/{{ $no_hp }}?text={{ urlencode($pesanLunas) }}" target="_blank"
                                                   class="flex items-center justify-center w-7 h-7 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-sm" title="Kirim Bukti Lunas via WA"></a>
                                            @endif

                                            {{-- Tombol Cek Bukti / Edit --}}
                                            @if($tagihan->bukti_pembayaran)
                                                <a href="{{ route('tagihan.bukti', $tagihan->id) }}"
                                                   class="flex items-center px-2 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 text-[10px] font-bold transition">📄 Cek</a>

                                                @if($tagihan->status != 'Lunas')
                                                    <form action="{{ route('tagihan.verifikasi_lunas', $tagihan->id) }}" method="POST"
                                                          onsubmit="return confirm('Yakin ingin memverifikasi tagihan ini menjadi LUNAS?');" style="display:inline-block;">
                                                        @csrf @method('PUT')
                                                        <button type="submit"
                                                            class="flex items-center px-2 py-1 bg-green-50 text-green-700 border border-green-200 rounded-lg hover:bg-green-100 text-[10px] font-bold transition">✅ OK</button>
                                                    </form>
                                                @endif
                                            @else
                                                <a href="{{ route('tagihan.edit', $tagihan->id) }}"
                                                   class="flex items-center justify-center w-7 h-7 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-lg border border-yellow-300 " title="Edit / Bayar Manual">✏️ Edit</a>
                                            @endif

                                            {{-- Hapus --}}
                                            <form action="{{ route('tagihan.destroy', $tagihan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus tagihan ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="flex items-center justify-center w-7 h-7 bg-red-100 text-red-600 hover:bg-red-200 rounded-lg border border-red-300" title="Hapus">✕</button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-400"> <!-- 💡 Ubah 6 menjadi 7 -->
                                        <div class="flex flex-col items-center gap-2">
                                            <span class="text-4xl">📭</span>
                                            <p class="font-medium">Atlet ini belum memiliki riwayat tagihan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{{-- ========================================== --}}
    {{-- SCRIPT JAVASCRIPT UNTUK KUNCI KOTAK BULAN --}}
    {{-- ========================================== --}}
    
    {{-- Kotak sembunyi untuk mengirim data ke JS tanpa bikin editor eror --}}
    <div id="bridge-data-atlet" 
         data-existing='@json($tagihans->map->only(["bulan", "tahun"]))'
         data-tahun="{{ \Carbon\Carbon::parse($atlet->created_at)->year }}"
         data-bulan="{{ \Carbon\Carbon::parse($atlet->created_at)->month }}"
         style="display: none;">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Ambil data dari element HTML jembatan di atas
            const bridgeData = document.getElementById('bridge-data-atlet');
            
            const existingTagihans = JSON.parse(bridgeData.dataset.existing);
            const namaBulanList = {1:'Januari', 2:'Februari', 3:'Maret', 4:'April', 5:'Mei', 6:'Juni', 7:'Juli', 8:'Agustus', 9:'September', 10:'Oktober', 11:'November', 12:'Desember'};
            
            // Ambil Tahun & Bulan Atlet Didaftarkan dengan aman
            const tahunDaftar = parseInt(bridgeData.dataset.tahun);
            const bulanDaftar = parseInt(bridgeData.dataset.bulan);
            
            const selectTahun = document.getElementById('pilih_tahun');
            const btnCentangSemua = document.getElementById('btn_centang_semua');
            let isSemuaDicentang = false;

            function perbaruiKotakBulan() {
                const tahunDipilih = parseInt(selectTahun.value);
                
                // Cari bulan apa saja yang sudah ada di tahun yang dipilih
                const bulanSudahAda = existingTagihans
                                        .filter(tagihan => parseInt(tagihan.tahun) === tahunDipilih)
                                        .map(tagihan => parseInt(tagihan.bulan));

                for(let i = 1; i <= 12; i++) {
                    const checkbox = document.getElementById('checkbox_bulan_' + i);
                    const label = document.getElementById('label_bulan_' + i);
                    const textSpan = document.getElementById('text_bulan_' + i);

                    // LOGIKA KUNCI BARU:
                    // 1. Cek apakah bulan/tahun ini SEBELUM tanggal atlet didaftarkan
                    const isSebelumDaftar = (tahunDipilih < tahunDaftar) || (tahunDipilih === tahunDaftar && i < bulanDaftar);
                    
                    // 2. Cek apakah tagihan sudah dibuat
                    const isSudahAda = bulanSudahAda.includes(i);

                    if(isSudahAda) {
                        // KUNCI: Karena tagihan sudah ada
                        checkbox.disabled = true;
                        checkbox.checked = false;
                        label.className = 'flex items-center gap-2 p-2 border border-gray-300 rounded-lg transition cursor-not-allowed bg-gray-100 opacity-60';
                        textSpan.innerHTML = `${namaBulanList[i]} <span class="text-[10px] text-red-500 font-bold italic ml-1">(Sudah Ada)</span>`;
                    
                    } else if(isSebelumDaftar) {
                        // KUNCI: Karena sebelum tanggal gabung/daftar
                        checkbox.disabled = true;
                        checkbox.checked = false;
                        label.className = 'flex items-center gap-2 p-2 border border-gray-200 rounded-lg transition cursor-not-allowed bg-gray-50 opacity-50';
                        textSpan.innerHTML = `${namaBulanList[i]} <span class="text-[10px] text-gray-500 font-bold italic ml-1">(Belum Gabung)</span>`;
                    
                    } else {
                        // BUKA KUNCI: Siap ditagih
                        checkbox.disabled = false;
                        label.className = 'flex items-center gap-2 p-2 border border-gray-200 rounded-lg transition cursor-pointer hover:bg-blue-50 label-bulan-centang';
                        textSpan.innerHTML = namaBulanList[i];
                    }
                }
            }

            // [REVISI] Logika Tombol "Centang Semua"
            btnCentangSemua.addEventListener('click', function() {
                isSemuaDicentang = !isSemuaDicentang; // Toggle nyala/mati
                
                for(let i = 1; i <= 12; i++) {
                    const checkbox = document.getElementById('checkbox_bulan_' + i);
                    // Hanya centang kotak yang TIDAK disabled (Terkunci)
                    if(!checkbox.disabled) {
                        checkbox.checked = isSemuaDicentang;
                    }
                }

                // Ubah teks & warna tombol biar admin gak bingung
                if(isSemuaDicentang) {
                    this.innerHTML = '◻️ Batal Centang';
                    this.classList.replace('text-blue-700', 'text-red-700');
                    this.classList.replace('bg-blue-100', 'bg-red-100');
                    this.classList.replace('border-blue-300', 'border-red-300');
                    this.classList.replace('hover:bg-blue-200', 'hover:bg-red-200');
                } else {
                    this.innerHTML = '☑️ Centang Semua';
                    this.classList.replace('text-red-700', 'text-blue-700');
                    this.classList.replace('bg-red-100', 'bg-blue-100');
                    this.classList.replace('border-red-300', 'border-blue-300');
                    this.classList.replace('hover:bg-red-200', 'hover:bg-blue-200');
                }
            });

            // Jika tahun diganti, reset state Centang Semua & perbarui kotak
            selectTahun.addEventListener('change', function() {
                isSemuaDicentang = false;
                btnCentangSemua.innerHTML = '☑️ Centang Semua';
                btnCentangSemua.className = 'text-[11px] font-bold text-blue-700 bg-blue-100 border border-blue-300 px-3 py-1.5 rounded-lg hover:bg-blue-200 transition shadow-sm flex items-center gap-1';
                perbaruiKotakBulan();
            });
            
            // Jalankan fungsi satu kali saat halaman baru dimuat
            perbaruiKotakBulan();
        });
    </script>
</x-app-layout>