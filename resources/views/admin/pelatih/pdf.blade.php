<!DOCTYPE html>
<html>
<head>
    <title>Biodata Coach - {{ $pelatih->nama_lengkap }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12pt; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; color: #2d3748; }
        .header p { margin: 5px 0; font-size: 10pt; color: #718096; }
        
        .photo-container { text-align: center; margin-bottom: 20px; }
        .photo { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #cbd5e0; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { width: 35%; background-color: #f7fafc; color: #4a5568; }
        
        .footer { margin-top: 50px; text-align: right; font-size: 10pt; }
        .ttd-box { display: inline-block; text-align: center; width: 200px; }
        .ttd-line { margin-top: 60px; border-top: 1px solid #000; }
        
        .status-badge {
            background-color: #c6f6d5; color: #22543d; 
            padding: 4px 8px; border-radius: 4px; font-size: 10pt; font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="header">
        <h1>JETHREE BASKETBALL ACADEMY</h1>
        <p>Data Profil Pelatih Resmi</p>
    </div>

    {{-- FOTO PROFIL --}}
    <div class="photo-container">
        @if($pelatih->foto_profil)
            {{-- Menggunakan path absolute agar terbaca oleh DomPDF --}}
            <img src="{{ public_path('storage/' . $pelatih->foto_profil) }}" class="photo">
        @else
            <div style="width:120px; height:120px; background:#ddd; border-radius:50%; margin:0 auto; line-height:120px; text-align:center;">No Foto</div>
        @endif
        <h2 style="margin-top: 10px; margin-bottom: 5px;">{{ $pelatih->nama_lengkap }}</h2>
        <span class="status-badge">{{ strtoupper($pelatih->status) }}</span>
    </div>

    {{-- TABEL BIODATA --}}
    <table>
        <tr>
            <th>Email Login</th>
            <td>{{ $pelatih->user->email ?? '-' }}</td>
        </tr>
        <tr>
            <th>Nomor WhatsApp</th>
            <td>{{ $pelatih->no_hp }}</td>
        </tr>
        <tr>
            <th>Tempat, Tanggal Lahir</th>
            <td>
                {{ $pelatih->tempat_lahir ?? '-' }}, 
                {{ \Carbon\Carbon::parse($pelatih->tanggal_lahir)->translatedFormat('d F Y') }}
                <br>
                <small style="color: #777">({{ \Carbon\Carbon::parse($pelatih->tanggal_lahir)->age }} Tahun)</small>
            </td>
        </tr>
        <tr>
            <th>Lisensi Melatih</th>
            <td>{{ $pelatih->lisensi ?? 'Belum ada lisensi' }}</td>
        </tr>
        {{-- ⚡ BARU: DATA FOKUS KELAS DAN GENDER PADA CETAKAN PDF --}}
        <tr>
            <th>Kategori Umur Latihan</th>
            <td>{{ $pelatih->kategori_fokus ?? '-' }}</td>
        </tr>
        <tr>
            <th>Gender Fokus Latihan</th>
            <td>{{ $pelatih->gender_fokus ?? '-' }}</td>
        </tr>
        <tr>
            <th>Alamat Domisili</th>
            <td>{{ $pelatih->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <th>Terdaftar Sejak</th>
            <td>{{ $pelatih->created_at->translatedFormat('d F Y, H:i') }}</td>
        </tr>
    </table>

    {{-- TANDA TANGAN (Opsional) --}}
    <div class="footer">
        <div class="ttd-box">
            <p>Dicetak pada: {{ date('d-m-Y') }}</p>
            <br>
            <p>Mengetahui,</p>
            <div class="ttd-line"></div>
            <p>Admin Jethree</p>
        </div>
    </div>

</body>
</html>