<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota Koperasi</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        h2 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Daftar Anggota Koperasi</h2>
    <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>NIA</th>
                <th>Nama Lengkap</th>
                <th>Jenis</th>
                <th>Tanggal Bergabung</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $member)
                <tr>
                    <td>{{ str_pad($member->nia, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $member->nama_lengkap }}</td>
                    <td>{{ $member->jenis->jenis ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($member->tanggal_bergabung)->format('d M Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;">Tidak ada data ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
