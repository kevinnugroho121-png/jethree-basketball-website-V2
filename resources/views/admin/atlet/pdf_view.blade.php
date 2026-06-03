<!DOCTYPE html>
<html>
<head>
    <title>Biodata - {{ $atlet->nama_lengkap }}</title>
    <style>
        /* CSS Khusus PDF (Sederhana agar rapi saat dicetak) */
        body { font-family: sans-serif; font-size: 11pt; line-height: 1.3; color: #000; }
        .container { width: 100%; margin: 0 auto; }
        
        /* KOP SURAT */
        .header { text-align: center; border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header p { margin: 2px 0 0; font-size: 10pt; color: #555; }

        /* LAYOUT UTAMA (FOTO KIRI, DATA KANAN) */
        .layout-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .col-photo { width: 160px; vertical-align: top; padding-right: 15px; }
        .col-data { vertical-align: top; }

        /* FOTO */
        .photo-frame { width: 150px; height: 200px; border: 1px solid #000; padding: 2px; }
        .photo-frame img { width: 100%; height: 100%; object-fit: cover; }
        .status-box { margin-top: 5px; text-align: center; border: 1px solid #000; padding: 5px; font-weight: bold; font-size: 10pt; }

        /* TABEL DATA */
        .section-title { font-size: 12pt; font-weight: bold; background-color: #eee; padding: 5px; border-bottom: 1px solid #000; margin-top: 15px; margin-bottom: 5px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 11pt; }
        .data-table td { padding: 3px 0; vertical-align: top; }
        .label { width: 35%; font-weight: bold; }
        .colon { width: 2%; }
        .value { width: 63%; }

        /* TANDA TANGAN */
        .signature-table { width: 100%; margin-top: 40px; text-align: center; }
        .sign-space { height: 70px; }
    </style>
</head>
<body>
    <div class="container">
        
        {{-- KOP --}}
        <div class="header">
            <h1>Formulir Biodata Atlet</h1>
            <p>Sistem Informasi Manajemen Jethree Basketball Academy</p>
        </div>

        <table class="layout-table">
            <tr>
                {{-- KOLOM FOTO --}}
                <td class="col-photo">
                    <div class="photo-frame">
                        @if($atlet->foto_profil)
                            {{-- public_path() wajib untuk DOMPDF --}}
                            <img src="{{ public_path('storage/' . $atlet->foto_profil) }}">
                        @else
                            <div style="text-align: center; padding-top: 80px; color: #999;">FOTO</div>
                        @endif
                    </div>
                    <div class="status-box">STATUS: {{ strtoupper($atlet->status) }}</div>
                </td>

                {{-- KOLOM DATA --}}
                <td class="col-data">
                    <div class="section-title" style="margin-top: 0;">A. DATA DIRI</div>
                    <table class="data-table">
                        <tr><td class="label">Nama Lengkap</td><td class="colon">:</td><td class="value"><strong>{{ $atlet->nama_lengkap }}</strong></td></tr>
                        
                        {{-- MENAMPILKAN EMAIL LOGIN DISINI --}}
                        <tr>
                            <td class="label">Email Login</td>
                            <td class="colon">:</td>
                            <td class="value">{{ $atlet->user->email ?? '-' }}</td>
                        </tr>
                        {{-- --------------------------------- --}}

                        <tr><td class="label">Nama Panggilan</td><td class="colon">:</td><td class="value">{{ $atlet->nama_panggilan }}</td></tr>
                        <tr><td class="label">TTL</td><td class="colon">:</td><td class="value">{{ $atlet->tempat_lahir }}, {{ \Carbon\Carbon::parse($atlet->tanggal_lahir)->format('d-m-Y') }}</td></tr>
                        <tr><td class="label">Usia</td><td class="colon">:</td><td class="value">{{ \Carbon\Carbon::parse($atlet->tanggal_lahir)->age }} Tahun</td></tr>
                        <tr><td class="label">Jenis Kelamin</td><td class="colon">:</td><td class="value">{{ $atlet->jenis_kelamin }}</td></tr>
                        <tr><td class="label">Alamat</td><td class="colon">:</td><td class="value">{{ $atlet->alamat }}</td></tr>
                        <tr><td class="label">No. HP Atlet</td><td class="colon">:</td><td class="value">{{ $atlet->no_hp_atlet ?? '-' }}</td></tr>
                    </table>

                    <div class="section-title">B. AKADEMI & SEKOLAH</div>
                    <table class="data-table">
                        <tr><td class="label">Kategori Umur</td><td class="colon">:</td><td class="value">{{ $atlet->kategori }}</td></tr>
                        <tr><td class="label">Posisi Bermain</td><td class="colon">:</td><td class="value">{{ $atlet->posisi }}</td></tr>
                        <tr><td class="label">Sekolah</td><td class="colon">:</td><td class="value">{{ $atlet->jenjang_sekolah }} - {{ $atlet->nama_sekolah }}</td></tr>
                    </table>

                    <div class="section-title">C. ORANG TUA / WALI</div>
                    <table class="data-table">
                        <tr><td class="label">Nama Orang Tua</td><td class="colon">:</td><td class="value">{{ $atlet->nama_orang_tua }}</td></tr>
                        <tr><td class="label">No. HP (WA)</td><td class="colon">:</td><td class="value">{{ $atlet->no_hp_orang_tua }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- AREA TANDA TANGAN --}}
        <table class="signature-table">
            <tr>
                <td width="50%">
                    Mengetahui,<br>Orang Tua / Wali
                    <div class="sign-space"></div>
                    ( ..................................... )
                </td>
                <td width="50%">
                    Kediri, {{ date('d F Y') }}<br>Atlet
                    <div class="sign-space"></div>
                    ( <strong>{{ $atlet->nama_lengkap }}</strong> )
                </td>
            </tr>
        </table>

    </div>
</body>
</html>