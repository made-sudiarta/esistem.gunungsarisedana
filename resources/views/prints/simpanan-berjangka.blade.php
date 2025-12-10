<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Print Simpanan Berjangka</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #999; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Simpanan Berjangka</h2>
    <table>
        <tr><th>No</th><td>{{ $data->id }}</td></tr>
        <tr><th>Kode Bilyet</th><td>{{ $data->kode_bilyet }}</td></tr>
        <tr><th>Group</th><td>{{ $data->group->group }}</td></tr>
        <tr><th>Anggota</th><td>{{ $data->member->nama_lengkap }}</td></tr>
        <tr><th>Nama Lengkap</th><td>{{ $data->nama_lengkap }}</td></tr>
        <tr><th>Tanggal Masuk</th><td>{{ $data->tanggal_masuk }}</td></tr>
        <tr><th>Jangka Waktu</th><td>{{ $data->jangka_waktu }} bulan</td></tr>
        <tr><th>Bunga</th><td>{{ $data->bunga_persen }}%</td></tr>
        <tr><th>Nominal</th><td>Rp {{ number_format($data->nominal, 0, ',', '.') }}</td></tr>
    </table>
</body>
</html>
