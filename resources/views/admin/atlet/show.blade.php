<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Biodata Atlet') }}
            </h2>
            
            <div class="flex space-x-2">
                {{-- TOMBOL DOWNLOAD PDF --}}
                <a href="{{ route('atlet.pdf', $atlet->id) }}" target="_blank" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-bold hover:bg-red-700 transition shadow-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Download PDF A4
                </a>

                {{-- Tombol Edit --}}
                <a href="{{ route('atlet.edit', $atlet->id) }}" class="px-4 py-2 bg-yellow-400 text-gray-900 rounded-md text-sm font-bold hover:bg-yellow-500 transition shadow-sm flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Edit Data
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        {{-- Gunakan max-w-4xl agar proporsi lebih mirip kertas A4 di layar --}}
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8"> 
            
            {{-- KERTAS UTAMA (Wrapper Putih) --}}
            <div class="bg-white shadow-xl sm:rounded-lg overflow-hidden border border-gray-200">
                <div class="p-8 text-gray-900">

                    {{-- JUDUL DOKUMEN (KOP) --}}
                    <div class="text-center mb-8 border-b-2 border-gray-800 pb-4">
                        <h1 class="text-2xl font-extrabold uppercase tracking-wider text-gray-800">Formulir Biodata Atlet</h1>
                        <p class="text-sm text-gray-500 mt-1 font-semibold">Sistem Informasi Manajemen Akademi Basket</p>
                    </div>

                    {{-- ========================================================== --}}
                    {{-- KOTAK PERINGATAN MATERI TERTINGGAL (MISI 2) DISISIPKAN DI SINI --}}
                    {{-- ========================================================== --}}
                    @if(isset($materiTertinggal) && $materiTertinggal->count() > 0)
                        <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-500 p-5 rounded-r shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <span class="text-yellow-500 text-2xl">⚠️</span>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-bold text-yellow-800 uppercase tracking-wider">
                                        Perhatian: Atlet Tertinggal {{ $materiTertinggal->count() }} Materi Latihan!
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-800">
                                        <p class="mb-2">Atlet ini bergabung pada <strong>{{ \Carbon\Carbon::parse($atlet->tanggal_gabung)->translatedFormat('d F Y') }}</strong> dan belum mengikuti silabus materi berikut yang sudah diajarkan sebelumnya:</p>
                                        <ul class="list-disc list-inside space-y-1 bg-yellow-100/60 p-3 rounded border border-yellow-200 font-medium">
                                            @foreach($materiTertinggal as $jadwal)
                                                <li>
                                                    <span class="text-gray-700">{{ \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('d M Y') }}</span> 
                                                    - {{ $jadwal->materi }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- ========================================================== --}}

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

                        {{-- === KOLOM KIRI: FOTO & STATUS (Lebar 4 dari 12 kolom) === --}}
                        <div class="md:col-span-4 flex flex-col items-center">
                            
                            {{-- Frame Foto (Style Pas Foto) --}}
                            <div class="w-48 h-64 bg-gray-200 rounded-sm overflow-hidden shadow-lg border-[4px] border-white outline outline-1 outline-gray-300 mb-4 relative">
                                @if($atlet->foto_profil)
                                    <img src="{{ asset('storage/' . $atlet->foto_profil) }}" 
                                         alt="Foto {{ $atlet->nama_lengkap }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="flex flex-col items-center justify-center h-full text-gray-400 bg-gray-100">
                                        <span class="text-xs text-center px-2 font-semibold">Pas Foto 3x4</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Badge Status --}}
                            <div class="w-48 text-center mt-2">
                                <span class="block w-full py-1.5 font-bold text-sm uppercase border-2 
                                    @if($atlet->status == 'Aktif') border-green-600 text-green-700 bg-green-50
                                    @elseif($atlet->status == 'Non-Aktif') border-yellow-600 text-yellow-700 bg-yellow-50
                                    @else border-red-600 text-red-700 bg-red-50 @endif">
                                    STATUS: {{ $atlet->status }}
                                </span>
                            </div>
                        </div>

                        {{-- === KOLOM KANAN: DETAIL BIODATA (Lebar 8 dari 12 kolom) === --}}
                        <div class="md:col-span-8 space-y-8">
                            
                            {{-- A. DATA DIRI --}}
                            <section>
                                <h3 class="font-bold text-lg text-gray-800 border-b-2 border-gray-300 pb-1 mb-3 uppercase">A. Data Diri Atlet</h3>
                                <table class="w-full text-sm">
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 w-1/3 font-semibold text-gray-600 uppercase text-xs">Nama Lengkap</td>
                                        <td class="py-2 w-2/3 font-bold text-lg text-gray-900">{{ $atlet->nama_lengkap }}</td>
                                    </tr>

                                    {{-- TAMBAHAN BARU: EMAIL LOGIN --}}
                                    <tr class="border-b border-gray-100 bg-yellow-50">
                                        <td class="py-2 font-semibold text-gray-600 uppercase text-xs pl-2">Email Login</td>
                                        <td class="py-2 text-gray-800 font-mono">
                                            {{ $atlet->user->email ?? '-' }}
                                            <span class="text-xs text-gray-500 ml-2 font-sans">(Untuk Login)</span>
                                        </td>
                                    </tr>
                                    {{-- -------------------------- --}}

                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 font-semibold text-gray-600 uppercase text-xs">Nama Panggilan</td>
                                        <td class="py-2 text-gray-800">{{ $atlet->nama_panggilan ?? '-' }}</td>
                                    </tr>
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 font-semibold text-gray-600 uppercase text-xs">Tempat, Tgl Lahir</td>
                                        <td class="py-2 text-gray-800">
                                            {{ $atlet->tempat_lahir }}, {{ \Carbon\Carbon::parse($atlet->tanggal_lahir)->translatedFormat('d F Y') }}
                                            <span class="text-blue-600 font-semibold ml-2">(Usia: {{ \Carbon\Carbon::parse($atlet->tanggal_lahir)->age }} Tahun)</span>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 font-semibold text-gray-600 uppercase text-xs">Jenis Kelamin</td>
                                        <td class="py-2 text-gray-800">{{ $atlet->jenis_kelamin }}</td>
                                    </tr>
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 font-semibold text-gray-600 uppercase text-xs align-top pt-3">Alamat Lengkap</td>
                                        <td class="py-2 text-gray-800 pt-3">{{ $atlet->alamat ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 font-semibold text-gray-600 uppercase text-xs">No. HP Atlet</td>
                                        <td class="py-2 font-mono font-bold text-base text-gray-900">{{ $atlet->no_hp_atlet ? $atlet->no_hp_atlet : '-' }}</td>
                                    </tr>
                                </table>
                            </section>

                            {{-- B. DATA SEKOLAH & C. AKADEMI (Grid 2 Kolom) --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <section>
                                    <h3 class="font-bold text-lg text-gray-800 border-b-2 border-gray-300 pb-1 mb-3 uppercase">B. Data Sekolah</h3>
                                    <table class="w-full text-sm">
                                        <tr class="border-b border-gray-100">
                                            <td class="py-2 w-1/3 font-semibold text-gray-600 uppercase text-xs">Jenjang</td>
                                            <td class="py-2 text-gray-800">{{ $atlet->jenjang_sekolah }}</td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold text-gray-600 uppercase text-xs">Nama Sekolah</td>
                                            <td class="py-2 text-gray-800 font-medium">{{ $atlet->nama_sekolah }}</td>
                                        </tr>
                                    </table>
                                </section>

                                <section>
                                    <h3 class="font-bold text-lg text-gray-800 border-b-2 border-gray-300 pb-1 mb-3 uppercase">C. Data Akademi</h3>
                                    <table class="w-full text-sm">
                                        <tr class="border-b border-gray-100">
                                            <td class="py-2 w-1/3 font-semibold text-gray-600 uppercase text-xs">Kategori</td>
                                            <td class="py-2">
                                                <span class="bg-blue-800 text-white py-0.5 px-3 text-sm font-bold rounded-sm">
                                                    {{ $atlet->kategori }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 font-semibold text-gray-600 uppercase text-xs">Posisi</td>
                                            <td class="py-2 text-gray-800">{{ $atlet->posisi ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </section>
                            </div>

                            {{-- D. DATA ORANG TUA --}}
                            <section>
                                <h3 class="font-bold text-lg text-gray-800 border-b-2 border-gray-300 pb-1 mb-3 uppercase">D. Data Orang Tua / Wali</h3>
                                <table class="w-full text-sm">
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 w-1/3 font-semibold text-gray-600 uppercase text-xs">Nama Orang Tua</td>
                                        <td class="py-2 text-gray-800 font-medium">{{ $atlet->nama_orang_tua }}</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 font-semibold text-gray-600 uppercase text-xs">No. HP (WA)</td>
                                        <td class="py-2 font-mono font-bold text-base text-gray-900">
                                            {{ $atlet->no_hp_orang_tua }}
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $atlet->no_hp_orang_tua) }}" target="_blank" class="ml-2 text-green-600 hover:text-green-800 no-underline text-xs font-bold">
                                                (Chat WA 💬)
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </section>
                            
                            {{-- AREA TANDA TANGAN (Biar resmi) --}}
                            <div class="pt-8 mt-8 border-t-2 border-gray-800 grid grid-cols-2 gap-8 text-center text-sm font-semibold text-gray-700">
                                <div>
                                    <p>Mengetahui,<br>Orang Tua / Wali</p>
                                    <div class="h-20"></div> {{-- Spasi tanda tangan --}}
                                    <p class="border-t border-gray-400 w-2/3 mx-auto pt-1">( ..................................... )</p>
                                </div>
                                <div>
                                    <p>Kediri, {{ date('d F Y') }}<br>Atlet Yang Bersangkutan</p>
                                    <div class="h-20"></div> {{-- Spasi tanda tangan --}}
                                    <p class="border-t border-gray-400 w-2/3 mx-auto pt-1 font-bold">( {{ $atlet->nama_lengkap }} )</p>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Tombol Kembali (Di luar area kertas) --}}
                    <div class="flex justify-start pt-8 no-print">
                        <a href="{{ route('atlet.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center font-semibold">
                            &laquo; Kembali ke Daftar Atlet
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>