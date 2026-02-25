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
    </style>
</head>
<body>
    <h2>Rekap Absensi Karyawan</h2>
    <div class="meta">
        <div><b>Nama:</b> {{ $userName }}</div>
        <div><b>Periode:</b> {{ $mulai->format('d-m-Y') }} s/d {{ $selesai->format('d-m-Y') }}</div>
    </div>

    <h3>Ringkasan</h3>
    <table style="margin-bottom: 16px;">
        <tr>
            <th>Total Jam Kerja</th>
            <td class="right">{{ number_format($totalJam, 2) }} jam</td>
        </tr>
        <tr>
            <th>Total Setoran</th>
            <td class="right">Rp {{ number_format($totalSetoran, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Penarikan</th>
            <td class="right">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Bersih (Setoran - Penarikan)</th>
            <td class="right"><b>Rp {{ number_format($totalBersih, 0, ',', '.') }}</b></td>
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
            @forelse($rows as $r)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $r->jam_masuk }}</td>
                    <td>{{ $r->jam_keluar }}</td>
                    <td class="right">{{ number_format((float)$r->jumlah_jam, 2) }}</td>
                    <td class="right">Rp {{ number_format((float)$r->jumlah_setoran, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format((float)$r->penarikan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada data pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>