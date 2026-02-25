<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2,h3 { margin: 0; }
        .meta { margin: 10px 0 16px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #333; padding:6px; }
        th { background:#f2f2f2; }
        .right { text-align:right; }

        /* ini kunci: page break tiap karyawan */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

@foreach($rekap as $i => $r)
    <h2>Rekap Absensi Karyawan</h2>
    <div class="meta">
        <div><b>Nama:</b> {{ $r['userName'] }}</div>
        <div><b>Periode:</b> {{ $mulai->format('d-m-Y') }} s/d {{ $selesai->format('d-m-Y') }}</div>
    </div>

    <h3>Ringkasan</h3>
    <table style="margin-bottom: 16px;">
        <tr>
            <th>Total Jam Kerja</th>
            <td class="right">{{ number_format($r['totalJam'], 2) }} jam</td>
        </tr>
        <tr>
            <th>Total Setoran</th>
            <td class="right">Rp {{ number_format($r['totalSetoran'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Penarikan</th>
            <td class="right">Rp {{ number_format($r['totalPenarikan'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Bersih (Setoran - Penarikan)</th>
            <td class="right"><b>Rp {{ number_format($r['totalBersih'], 0, ',', '.') }}</b></td>
        </tr>
    </table>

    <h3>Detail</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Keluar</th>
                <th class="right">Jumlah Jam</th>
                <th class="right">Setoran</th>
                <th class="right">Penarikan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($r['rows'] as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $row->jam_masuk }}</td>
                    <td>{{ $row->jam_keluar }}</td>
                    <td class="right">{{ number_format((float)$row->jumlah_jam, 2) }}</td>
                    <td class="right">Rp {{ number_format((float)$row->jumlah_setoran, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format((float)$row->penarikan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada data pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Jangan page break setelah karyawan terakhir --}}
    @if($i < count($rekap) - 1)
        <div class="page-break"></div>
    @endif
@endforeach

</body>
</html>