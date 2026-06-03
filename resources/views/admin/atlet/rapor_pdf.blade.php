<!DOCTYPE html>
<html>
<head>
    <title>Rapor Evaluasi - {{ $atlet->nama_lengkap }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 13px; color: #333; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px solid #16a34a; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #166534; font-size: 24px; letter-spacing: 1px;}
        .header p { margin: 5px 0 0 0; color: #555; font-size: 14px;}
        
        .biodata-table { width: 100%; margin-bottom: 20px; }
        .biodata-table td { padding: 4px 0; }
        .biodata-label { width: 150px; font-weight: bold; color: #555;}
        
        .section-title { font-size: 16px; font-weight: bold; background-color: #f0fdf4; color: #166534; padding: 8px; border-left: 4px solid #16a34a; margin-top: 20px; margin-bottom: 10px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 12px;}
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; }
        .data-table th { background-color: #16a34a; color: white; text-align: center; }
        
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .footer { margin-top: 50px; width: 100%; }
        .signature-box { float: right; text-align: center; width: 200px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>JE-THREE BASKETBALL ACADEMY</h2>
        <p>LAPORAN EVALUASI PERKEMBANGAN ATLET</p>
        <p class="font-bold">Periode: {{ $bulanIni }}</p>
    </div>

    <table class="biodata-table">
        <tr>
            <td class="biodata-label">Nama Lengkap</td>
            <td>: <strong>{{ $atlet->nama_lengkap }}</strong></td>
            <td class="biodata-label" style="width: 100px;">Kategori / KU</td>
            <td>: {{ $atlet->kategori }}</td>
        </tr>
        <tr>
            <td class="biodata-label">Asal Sekolah</td>
            <td>: {{ $atlet->nama_sekolah }} ({{ $atlet->jenjang_sekolah }})</td>
            <td class="biodata-label">Posisi</td>
            <td>: {{ $atlet->posisi ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">A. REKAPITULASI KEHADIRAN BULAN INI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Total Jadwal</th>
                <th style="width: 25%; background-color: #3b82f6;">Hadir</th>
                <th style="width: 25%; background-color: #eab308;">Izin / Sakit</th>
                <th style="width: 25%; background-color: #ef4444;">Alpa (Tanpa Keterangan)</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center font-bold" style="font-size: 16px;">
                <td>{{ $totalJadwal }} Kali</td>
                <td style="color: #2563eb;">{{ $totalHadir }} Kali</td>
                <td style="color: #ca8a04;">{{ $totalIzinSakit }} Kali</td>
                <td style="color: #dc2626;">{{ $totalAlpa }} Kali</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">B. RIWAYAT NILAI LATIHAN BULAN INI</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 16%;">Tanggal Latihan</th>
                <th style="width: 12%;">Dribble</th>
                <th style="width: 12%;">Passing</th>
                <th style="width: 12%;">Shooting</th>
                <th style="width: 12%;">IQ/Fisik</th>
                <th style="width: 36%; text-align: left;">Catatan Pelatih</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataNilai as $item)
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_latihan)->format('d M Y') }}</td>
                <td class="text-center font-bold">{{ $item->nilai_dribble ?? '-' }}</td>
                <td class="text-center font-bold">{{ $item->nilai_pass ?? '-' }}</td>
                <td class="text-center font-bold">{{ $item->nilai_shoot ?? '-' }}</td>
                <td class="text-center font-bold">{{ $item->nilai_iq ?? '-' }}</td>
                <td>{{ $item->catatan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px;">Belum ada data nilai latihan untuk bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-box">
            <p>Kediri, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p style="margin-bottom: 70px;">Head Coach / Manajemen</p>
            <p><strong>( ______________________ )</strong></p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>