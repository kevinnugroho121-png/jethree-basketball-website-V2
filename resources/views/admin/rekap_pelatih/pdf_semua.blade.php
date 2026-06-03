<!DOCTYPE html>
<html>
<head>
    <title>Rekap Kinerja Semua Pelatih</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #22c55e; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #166534; font-size: 20px; }
        .header p { margin: 5px 0 0 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #f0fdf4; color: #166534; font-weight: bold; }
        .text-left { text-align: left; }
        .signature { margin-top: 50px; text-align: right; width: 100%; }
        .signature p { margin-right: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>JE-THREE BASKETBALL ACADEMY</h2>
        <p>Laporan Rekapitulasi Kinerja Pelatih Keseluruhan</p>
        <p>Periode: <strong>{{ $namaBln }} {{ $tahun }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th class="text-left">Nama Pelatih</th>
                <th>Total Jadwal</th>
                <th>Hadir Ngajar</th>
                <th>Hangus/Alpa</th>
                <th>Tingkat Kinerja</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapData as $index => $data)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="text-left"><strong>{{ $data['nama'] }}</strong></td>
                <td>{{ $data['total_jadwal'] }}</td>
                <td>{{ $data['hadir'] }}</td>
                <td>{{ $data['hangus'] }}</td>
                <td><strong>{{ $data['persentase'] }}%</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature">
        <p>Kediri, {{ date('d F Y') }}</p>
        <br><br><br>
        <p><strong>( Admin / Manajemen )</strong></p>
    </div>
</body>
</html>