<!DOCTYPE html>
<html>
<head>
    <title>Laporan Perkembangan Atlet</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; color: #166534;}
        .header p { margin: 5px 0; font-size: 13px; color: #555; }
        
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; background-color: #eee; padding: 5px; margin-bottom: 10px; border-left: 4px solid #166534; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table th, table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        table th { background-color: #f9f9f9; font-weight: bold; text-align: center; }
        
        .biodata-table td { border: none; padding: 4px; }
        .text-center { text-align: center; }
        .badge-lunas { color: green; font-weight: bold; }
        .badge-belum { color: red; font-weight: bold; }
    </style>
</head>
<body>

    {{-- KOP SURAT --}}
    <div class="header">
        <h1>J3 BASKETBALL ACADEMY</h1>
        <p><strong>Laporan Perkembangan & Evaluasi Atlet</strong></p>
        <p>Periode: <strong>Semester {{ $namaSemester }} (Tahun Ajaran {{ $tahunAjaran }})</strong></p>
    </div>

    {{-- BIODATA --}}
    <div class="section">
        <div class="section-title">A. Biodata Atlet</div>
        <table class="biodata-table">
            <tr>
                <td width="150">Nama Lengkap</td>
                <td width="10">:</td>
                <td><strong>{{ $atlet->nama_lengkap }}</strong></td>
                <td width="150">Kategori (KU)</td>
                <td width="10">:</td>
                <td>{{ $atlet->kategori }}</td>
            </tr>
            <tr>
                <td>Posisi</td>
                <td>:</td>
                <td>{{ $atlet->posisi ?? '-' }}</td>
                <td>Jenis Kelamin</td>
                <td>:</td>
                <td>{{ $atlet->jenis_kelamin }}</td>
            </tr>
            <tr>
                <td>Tempat, Tgl Lahir</td>
                <td>:</td>
                <td>{{ $atlet->tempat_lahir }}, {{ \Carbon\Carbon::parse($atlet->tanggal_lahir)->format('d M Y') }}</td>
                <td>Asal Sekolah</td>
                <td>:</td>
                <td>{{ $atlet->nama_sekolah ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- STATISTIK KEHADIRAN --}}
    <div class="section">
        <div class="section-title">B. Statistik Kehadiran Semester Ini</div>
        <table>
            <tr>
                <th style="background-color: #dbeafe;">Hadir (H)</th>
                <th style="background-color: #fef08a;">Sakit (S)</th>
                <th style="background-color: #ffedd5;">Izin (I)</th>
                <th style="background-color: #fee2e2;">Alpha (A)</th>
                <th style="background-color: #e5e7eb;">Total Sesi</th>
            </tr>
            <tr>
                <td class="text-center font-bold text-blue-700">{{ $hadir }}</td>
                <td class="text-center font-bold text-yellow-700">{{ $sakit }}</td>
                <td class="text-center font-bold text-orange-700">{{ $izin }}</td>
                <td class="text-center font-bold text-red-700">{{ $alpha }}</td>
                <td class="text-center font-bold">{{ $hadir + $sakit + $izin + $alpha }}</td>
            </tr>
        </table>
    </div>

    {{-- RIWAYAT LATIHAN & NILAI --}}
    <div class="section">
        <div class="section-title">C. Riwayat Latihan & Evaluasi Harian</div>
        <table>
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="80">Tanggal</th>
                    <th width="50">Status</th>
                    <th>Dribble</th>
                    <th>Pass</th>
                    <th>Shoot</th>
                    <th>IQ</th>
                    <th>Catatan Coach</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat_latihan as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_latihan)->format('d/m/Y') }}</td>
                        <td class="text-center font-bold">{{ $row->status }}</td>
                        <td class="text-center">{{ $row->nilai_dribble ?? '-' }}</td>
                        <td class="text-center">{{ $row->nilai_pass ?? '-' }}</td>
                        <td class="text-center">{{ $row->nilai_shoot ?? '-' }}</td>
                        <td class="text-center">{{ $row->nilai_iq ?? '-' }}</td>
                        <td><i>{{ $row->catatan ?? '-' }}</i></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center" style="padding: 20px;">Belum ada data latihan di semester ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- RIWAYAT KEUANGAN --}}
    <div class="section">
        <div class="section-title">D. Status Keuangan & SPP</div>
        @if($total_tunggakan > 0)
            <p style="color: red; font-weight: bold; margin-bottom: 5px;"></p>
        @else
            <p style="color: green; font-weight: bold; margin-bottom: 5px;"></p>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Bulan / Keterangan</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tgl Bayar</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tagihans as $tagihan)
                    <tr>
                        <td>
                            {{ $tagihan->jenis_tagihan ?? 'SPP' }} Bulan {{ \Carbon\Carbon::create()->month($tagihan->bulan)->translatedFormat('F') }} {{ $tagihan->tahun }}
                        </td>
                        <td>Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($tagihan->status == 'Lunas')
                                <span class="badge-lunas">LUNAS</span>
                            @else
                                <span class="badge-belum">BELUM LUNAS</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $tagihan->tanggal_lunas ? \Carbon\Carbon::parse($tagihan->tanggal_lunas)->format('d/m/Y') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center">Tidak ada riwayat tagihan di semester ini.</td></tr>
                @endforelse
            </tbody>
            
        </table>
    </div>

    {{-- EVALUASI PELATIH / RAPOR BULANAN --}}
    <div class="section">
        <div class="section-title">E. Evaluasi & Catatan Akhir Pelatih (Terbaru)</div>
        
        @if(isset($last_progres) && $last_progres)
            <table style="width: 100%; border: none;">
                <tr>
                    <td width="50%" style="vertical-align: top; border: none; padding: 0;">
                        <table style="width: 95%;">
                            <tr>
                                <th style="background: #eee;">Aspek</th>
                                <th style="background: #eee; text-align: center;">Skor</th>
                            </tr>
                            <tr>
                                <td><strong>Teknik</strong> (Dribble, Shoot, Pass)</td>
                                <td style="text-align: center; font-weight: bold; font-size: 14px;">{{ number_format($last_progres->teknik, 0) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fisik</strong> (Speed, Power, Stamina)</td>
                                <td style="text-align: center; font-weight: bold; font-size: 14px;">{{ number_format($last_progres->fisik, 0) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Mental</strong> (Disiplin, Fokus, Spirit)</td>
                                <td style="text-align: center; font-weight: bold; font-size: 14px;">{{ number_format($last_progres->mental, 0) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Taktik</strong> (IQ Game, Positioning)</td>
                                <td style="text-align: center; font-weight: bold; font-size: 14px;">{{ number_format($last_progres->taktik, 0) }}</td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%" style="vertical-align: top; border: none; padding: 0;">
                        <div style="border: 1px solid #ddd; padding: 10px; height: 115px; background-color: #fcfcfc;">
                            <strong style="display: block; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 5px;">💬 Catatan Khusus Pelatih:</strong>
                            <p style="font-style: italic; margin: 0; color: #444;">"{{ $last_progres->catatan ?? 'Tidak ada catatan khusus.' }}"</p>
                            <div style="margin-top: 10px; font-size: 10px; color: #888; text-align: right;">
                                Tgl Evaluasi: {{ \Carbon\Carbon::parse($last_progres->tanggal)->format('d M Y') }}
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        @else
            <div style="padding: 15px; border: 1px dashed #ccc; text-align: center; color: #777;">
                Belum ada evaluasi akhir semester yang diinput oleh pelatih.
            </div>
        @endif
    </div>

    {{-- FOOTER TTD --}}
    <div style="margin-top: 30px; text-align: right; margin-right: 30px;">
        <p>Kediri, {{ date('d F Y') }}</p>
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>Admin / Manajemen J3 Academy</strong></p>
    </div>

</body>
</html>