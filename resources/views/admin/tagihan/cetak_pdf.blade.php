<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 2px 0; }
        
        .meta { margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #000; }
        th { background-color: #f2f2f2; padding: 8px; text-align: center; }
        td { padding: 6px; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .total-row td { background-color: #e6e6e6; font-weight: bold; }
        
        .footer { margin-top: 40px; text-align: right; }
        .ttd-area { margin-top: 60px; margin-right: 30px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Jethree Basketball Academy</h1>
        <p>Laporan Pemasukan Keuangan</p>
        <p>Periode: {{ $bulan ? DateTime::createFromFormat('!m', $bulan)->format('F') : 'Semua Bulan' }} {{ $tahun }}</p>
    </div>

    <div class="meta">
        <strong>Dicetak Oleh:</strong> {{ Auth::user()->name }} <br>
        <strong>Tanggal Cetak:</strong> {{ date('d F Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal Bayar</th>
                <th>Nama Atlet</th>
                <th width="15%">Kategori</th>
                <th width="20%">Keterangan (Bulan)</th>
                <th width="20%">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->updated_at->format('d/m/Y') }}</td>
                    <td>{{ $item->atlet->user->name ?? '-' }}</td>
                    <td class="text-center">{{ $item->atlet->kategori_umur ?? '-' }}</td>
                    <td>SPP {{ $item->bulan }} {{ $item->tahun }}</td>
                    <td class="text-right">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pemasukan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">TOTAL PEMASUKAN</td>
                <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Kediri, {{ date('d F Y') }}</p>
        <p>Mengetahui,</p>
        <div class="ttd-area">
            ( Admin Keuangan )
        </div>
    </div>

</body>
</html>