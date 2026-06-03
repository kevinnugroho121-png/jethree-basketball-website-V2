<!DOCTYPE html>
<html>
<head>
    <title>Rekap Kinerja Pelatih</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #22c55e; padding-bottom: 10px; margin-bottom: 30px; }
        .header h2 { margin: 0; color: #166534; font-size: 22px; }
        .header p { margin: 5px 0 0 0; color: #555; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .info-box table { width: 100%; border: none; }
        .info-box td { padding: 8px; text-align: left; border: none; font-size: 16px;}
        .highlight { font-size: 24px; font-weight: bold; color: #166534; text-align: center; margin-top: 20px;}
        .signature { margin-top: 60px; text-align: right; width: 100%; }
        .signature p { margin-right: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>JE-THREE BASKETBALL ACADEMY</h2>
        <p>Laporan Evaluasi Kinerja Pelatih Individu</p>
        <p>Periode: <strong>{{ $namaBln }} {{ $tahun }}</strong></p>
    </div>

    <div class="info-box">
        <table>
            <tr>
                <td width="30%">Nama Pelatih</td>
                <td width="5%">:</td>
                <td><strong>{{ $dataPelatih['nama'] }}</strong></td>
            </tr>
            <tr>
                <td>Total Jadwal Mengajar</td>
                <td>:</td>
                <td>{{ $dataPelatih['total_jadwal'] }} Pertemuan</td>
            </tr>
            <tr>
                <td>Kehadiran (Selesai Absen)</td>
                <td>:</td>
                <td><strong>{{ $dataPelatih['hadir'] }} Pertemuan</strong></td>
            </tr>
            <tr>
                <td>Jadwal Hangus / Alpa</td>
                <td>:</td>
                <td>{{ $dataPelatih['hangus'] }} Pertemuan</td>
            </tr>
        </table>
    </div>

    <div class="highlight">
        TINGKAT KINERJA BULAN INI: {{ $dataPelatih['persentase'] }}%
    </div>

    <div class="signature">
        <p>Kediri, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>( Admin / Manajemen )</strong></p>
    </div>
</body>
</html>