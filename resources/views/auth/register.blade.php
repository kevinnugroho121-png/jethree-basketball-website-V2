<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Baru - Jethree Academy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #15803d; border-radius: 10px; } /* Hijau */
        
        .valid-pass { color: #16a34a; font-weight: bold; }
        .invalid-pass { color: #9ca3af; }
    </style>
</head>
<body class="h-screen bg-gray-50 font-sans text-gray-900 overflow-hidden">

    <div class="flex h-full w-full">
        
        {{-- KIRI: POSTER VISUAL --}}
        <div class="hidden lg:flex w-5/12 bg-green-900 text-white flex-col justify-between p-12 relative">
            <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/basketball.png')]"></div>
            <div class="relative z-10 mt-10">
                <img src="{{ asset('images/logo_j3.PNG') }}" alt="logo_j3.PNG" class="w-32 h-auto mb-6 drop-shadow-lg object-contain">
                <h1 class="text-4xl font-extrabold tracking-tight leading-tight mb-4">JETHREE BASKETBALL<br> <span class="text-orange-400">ACADEMY</span></h1>
                <p class="text-green-100 text-lg leading-relaxed opacity-90 border-l-4 border-orange-500 pl-4">"Talent wins games, but teamwork and intelligence win championships."</p>
            </div>
            <div class="relative z-10 text-sm text-green-400/80 font-mono">&copy; {{ date('Y') }} Jethree System.</div>
        </div>

        {{-- KANAN: FORMULIR --}}
        <div class="w-full lg:w-7/12 h-full bg-white flex flex-col relative">
            
            {{-- HEADER: Tombol Kembali & Login --}}
            <div class="px-6 py-4 border-b border-gray-100 bg-white z-20 flex justify-between items-center shadow-sm shrink-0">
                <div class="flex items-center gap-4">
                    {{-- TOMBOL KEMBALI KE LANDING PAGE --}}
                    <a href="{{ url('/') }}" class="text-gray-500 hover:text-green-700 transition flex items-center gap-1 text-sm font-medium">
                        &larr; Beranda
                    </a>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">Daftar Baru</h2>
                </div>
                <a href="{{ route('login') }}" class="text-sm font-bold text-green-700 hover:text-green-900 transition flex items-center gap-1">
                    Masuk <span aria-hidden="true">&rarr;</span>
                </a>
            </div>

            {{-- AREA SCROLL FORMULIR --}}
            <div class="flex-1 overflow-y-auto custom-scroll p-6 lg:p-10 bg-gray-50">
                
                {{-- PESAN ERROR DARI SERVER --}}
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r shadow-sm">
                        <div class="flex">
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">Gagal Menyimpan Data!</h3>
                                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-8" id="regForm">
                    @csrf

                    {{-- 1. DATA AKUN --}}
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs">1</span> Akun Login
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Email Aktif <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" required placeholder="contoh: atlet@gmail.com" 
                                    class="w-full h-11 rounded-lg border-gray-300 bg-gray-50 px-4 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password" required placeholder="Min. 8 Karakter" 
                                            class="w-full h-11 rounded-lg border-gray-300 bg-gray-50 px-4 pr-10 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition">
                                        <button type="button" onclick="togglePass('password')" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                    </div>
                                    <div class="mt-2 text-[10px] space-y-1 p-2 bg-gray-50 rounded border border-gray-100" id="pass_criteria">
                                        <p id="rule_length" class="invalid-pass flex items-center gap-1"><span class="icon">○</span> Min. 8 Karakter</p>
                                        <p id="rule_upper" class="invalid-pass flex items-center gap-1"><span class="icon">○</span> Huruf Besar</p>
                                        <p id="rule_number" class="invalid-pass flex items-center gap-1"><span class="icon">○</span> Angka</p>
                                        <p id="rule_symbol" class="invalid-pass flex items-center gap-1"><span class="icon">○</span> Simbol (@#$)</p>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Ulangi Password <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ketik ulang password" 
                                            class="w-full h-11 rounded-lg border-gray-300 bg-gray-50 px-4 pr-10 text-sm focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-200 transition">
                                        <button type="button" onclick="togglePass('password_confirmation')" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. BIODATA ATLET --}}
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-green-500"></div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs">2</span> Biodata
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap (Sesuai Akta) <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama Lengkap Atlet" class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Panggilan</label>
                                <input type="text" name="nama_panggilan" value="{{ old('nama_panggilan') }}" placeholder="Nama Panggilan Atlet" class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" required placeholder="Kota Kelahiran" class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                            </div>

                            {{-- TANGGAL LAHIR (PERBAIKAN NAME) --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                                {{-- PERHATIKAN NAME DI SINI: tgl_lahir --}}
                                <input type="date" name="tgl_lahir" id="tgl_lahir" value="{{ old('tgl_lahir') }}" required 
                                    class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition cursor-pointer">
                                
                                <div id="age_feedback" class="hidden mt-2 p-2 bg-blue-50 border border-blue-100 rounded text-xs text-blue-800">
                                    🎂 Umur: <span id="age_val" class="font-bold">0</span> Tahun
                                </div>
                                <p id="tgl_error" class="text-xs text-red-600 font-bold mt-1 hidden">🚫 Tanggal tidak valid!</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="jenis_kelamin" required class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm bg-white focus:border-green-500 focus:ring-2 focus:ring-green-200">
                                    <option value="">-- Pilih --</option>
                                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">No. WhatsApp <span class="text-red-500">*</span></label>
                                <input type="number" name="no_hp" value="{{ old('no_hp') }}" required placeholder="08xxxxxxxxxx" class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Domisili</label>
                                <textarea name="alamat" rows="2" placeholder="Alamat lengkap..." class="w-full rounded-lg border-gray-300 px-4 py-3 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">{{ old('alamat') }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-700 mb-1">Foto Profil</label>
                                <input type="file" name="foto_profil" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 border border-gray-300 rounded-lg p-1">
                            </div>
                        </div>
                    </div>

                    {{-- 3. SEKOLAH & AKADEMI --}}
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-orange-500"></div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs">3</span> Sekolah & Kategori
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Kategori (Otomatis) 🔒</label>
                                <input type="text" id="kategori_display" readonly class="w-full h-11 rounded-lg border-gray-200 bg-gray-100 px-4 text-sm font-bold text-orange-600 cursor-not-allowed" placeholder="Isi Tgl Lahir dulu...">
                                <input type="hidden" name="kategori" id="kategori_input">
                                
                                <div id="school_rec" class="hidden mt-2 p-2 bg-orange-50 border border-orange-100 rounded text-xs text-orange-800">
                                    🎓 Rekomendasi: <span id="rec_val" class="font-bold">-</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Jenjang Sekolah <span class="text-red-500">*</span></label>
                                <select name="jenjang_sekolah" required class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 bg-white">
                                    <option value="">-- Pilih --</option>
                                    <option value="SD" {{ old('jenjang_sekolah') == 'SD' ? 'selected' : '' }}>SD / MI</option>
                                    <option value="SMP" {{ old('jenjang_sekolah') == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                                    <option value="SMA" {{ old('jenjang_sekolah') == 'SMA' ? 'selected' : '' }}>SMA / SMK</option>
                                    <option value="Umum" {{ old('jenjang_sekolah') == 'Umum' ? 'selected' : '' }}>Umum</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Sekolah <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah') }}" required placeholder="Contoh: SMAN 2 Kediri" class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Posisi (Opsional)</label>
                                <select name="posisi" class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 bg-white">
                                    <option value="">-- Belum Tahu --</option>
                                    <option value="Point Guard">Point Guard</option>
                                    <option value="Shooting Guard">Shooting Guard</option>
                                    <option value="Center">Center</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- 4. ORANG TUA --}}
                    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-1 h-full bg-purple-500"></div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-5 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center text-xs">4</span> Data Ortu
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Orang Tua <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_ortu" value="{{ old('nama_ortu') }}" required placeholder="Nama Lengkap Ortu" class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">HP Ortu (Darurat) <span class="text-red-500">*</span></label>
                                <input type="number" name="no_hp_ortu" value="{{ old('no_hp_ortu') }}" required class="w-full h-11 rounded-lg border-gray-300 px-4 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 transition">
                            </div>
                        </div>
                    </div>

                    {{-- TOMBOL SUBMIT BESAR --}}
                    <div class="pt-6 pb-10">
                        <button type="submit" id="submitBtn" class="w-full flex justify-center items-center gap-2 py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-extrabold text-white bg-gray-400 cursor-not-allowed transition-all" disabled>
                            DATA BELUM LENGKAP / PASSWORD LEMAH
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- LOGIKA JAVASCRIPT --}}
    <script>
        function togglePass(id) {
            const input = document.getElementById(id);
            input.type = input.type === "password" ? "text" : "password";
        }

        document.addEventListener('DOMContentLoaded', function() {
            const tglInput = document.getElementById('tgl_lahir'); // ID sesuai input
            const ageFeedback = document.getElementById('age_feedback');
            const ageVal = document.getElementById('age_val');
            const schoolRec = document.getElementById('school_rec');
            const recVal = document.getElementById('rec_val');
            const katDisplay = document.getElementById('kategori_display');
            const katInput = document.getElementById('kategori_input');
            const tglError = document.getElementById('tgl_error');

            const passInput = document.getElementById('password');
            const submitBtn = document.getElementById('submitBtn');



            // --- REVISI GABUNGAN: LOGIKA TANGGAL LAHIR & GENDER ATLET ---
            const genderSelect = document.querySelector('select[name="jenis_kelamin"]');

            function hitungKategoriOtomatis() {
                if (!tglInput.value) return; // Lewati jika tanggal belum diisi

                const birthDate = new Date(tglInput.value);
                const today = new Date();
                
                if (birthDate >= today) {
                    tglError.classList.remove('hidden');
                    tglInput.value = '';
                    ageFeedback.classList.add('hidden');
                    schoolRec.classList.add('hidden');
                    katDisplay.value = 'Tanggal Invalid';
                    return;
                }
                tglError.classList.add('hidden');

                // Hitung Umur Riil
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }

                ageFeedback.classList.remove('hidden');
                ageVal.innerText = age;

                // 1. Tentukan Base Kategori Usia
                let baseKategori = '';
                if (age <= 10) baseKategori = 'KU-10';
                else if (age <= 12) baseKategori = 'KU-12';
                else if (age <= 14) baseKategori = 'KU-14';
                else if (age <= 16) baseKategori = 'KU-16';
                else if (age <= 18) baseKategori = 'KU-18';
                else baseKategori = 'Senior';

                // 2. Suntik Logika Gender Suffix (Laki-laki -> Putra, Perempuan -> Putri)
                let genderSuffix = '';
                if (baseKategori !== 'Senior' && genderSelect.value !== '') {
                    genderSuffix = (genderSelect.value === 'Laki-laki') ? ' Putra' : ' Putri';
                }

                // 3. Gabungkan Hasil Akhir ke Input Hidden (Untuk dikirim ke Laravel Database)
                let kategoriFinal = baseKategori + genderSuffix;
                katDisplay.value = kategoriFinal;
                katInput.value = kategoriFinal;

                // Rekomendasi Jenjang Sekolah
                let jenjang = '-';
                if(age >= 5 && age <= 10) jenjang = 'SD / MI';
                else if(age >= 11 && age <= 14) jenjang = 'SMP / MTs';
                else if(age >= 15 && age <= 18) jenjang = 'SMA / SMK / MA';
                else if(age > 18) jenjang = 'Kuliah';
                
                schoolRec.classList.remove('hidden');
                recVal.innerText = jenjang;
            }

            // Daftarkan fungsi ke 2 trigger sekaligus (Anti-Miss)
            tglInput.addEventListener('change', hitungKategoriOtomatis);
            genderSelect.addEventListener('change', hitungKategoriOtomatis);


            

            // --- LOGIKA PASSWORD VALIDASI (REALTIME) ---
            passInput.addEventListener('input', function() {
                const val = this.value;
                const rules = {
                    length: val.length >= 8,
                    upper: /[A-Z]/.test(val),
                    number: /[0-9]/.test(val),
                    symbol: /[!@#$%^&*(),.?":{}|<>]/.test(val)
                };

                updateRule('rule_length', rules.length);
                updateRule('rule_upper', rules.upper);
                updateRule('rule_number', rules.number);
                updateRule('rule_symbol', rules.symbol);

                // Cek Validasi Total
                const isValid = Object.values(rules).every(Boolean);
                if (isValid) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.add('bg-green-700', 'hover:bg-green-800', 'transform', 'hover:scale-[1.01]');
                    submitBtn.innerHTML = '<span>🚀</span> DAFTAR SEKARANG';
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.remove('bg-green-700', 'hover:bg-green-800', 'transform', 'hover:scale-[1.01]');
                    submitBtn.innerHTML = '<span>🔒</span> PASSWORD BELUM KUAT';
                }
            });

            function updateRule(id, valid) {
                const el = document.getElementById(id);
                const icon = el.querySelector('.icon');
                if (valid) {
                    el.classList.remove('invalid-pass');
                    el.classList.add('valid-pass');
                    icon.innerText = '✓';
                } else {
                    el.classList.remove('valid-pass');
                    el.classList.add('invalid-pass');
                    icon.innerText = '○';
                }
            }
        });
    </script>
    {{-- TAMBAHKAN SCRIPT AJAX WA DI SINI (SEBELUM BODY TERTUTUP) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Kita bidik input 'no_hp_ortu' sesuai dengan form di register
            $('input[name="no_hp_ortu"]').on('blur', function() {
                var wa_diketik = $(this).val();

                if(wa_diketik.length > 8) { 
                    $.ajax({          
                        url: "{{ route('public.checkWa') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            no_hp: wa_diketik
                        },
                        success: function(response) {
                            if(response.status == 'ditemukan') {
                                // Target ke input 'nama_ortu' sesuai form register
                                $('input[name="nama_ortu"]').val(response.nama_orang_tua);
                                $('input[name="nama_ortu"]').prop('readonly', true);
                                $('input[name="nama_ortu"]').addClass('bg-gray-200 cursor-not-allowed opacity-70');

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Halo, Kakak!',
                                    text: 'Nomor WA ini terdeteksi sebagai wali dari saudara yang sudah bergabung. Nama (' + response.nama_orang_tua + ') otomatis diisi.',
                                    confirmButtonColor: '#15803d', // Hijau senada landing page
                                    confirmButtonText: 'Lanjutkan Pendaftaran',
                                });
                            } else {
                                $('input[name="nama_ortu"]').prop('readonly', false);
                                $('input[name="nama_ortu"]').removeClass('bg-gray-200 cursor-not-allowed opacity-70');
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>