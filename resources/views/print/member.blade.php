<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Data Anggota</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 8px;
            padding: 20px;
            background: #fff;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 16px;
        }
        h2 {
            margin-top: 0;
            font-size: 12px;
            border-bottom: 1px solid #eee;
            padding-bottom: 6px;
        }
        h3 {
            font-size: 10px;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
        }
        .col-6 {
            width: 50%;
            padding: 4px 8px;
            box-sizing: border-box;
        }
        .col-4 {
            width: 33.33%;
            padding: 4px 8px;
            box-sizing: border-box;
        }
        .field {
            margin-bottom: 8px;
        }
        .px-3 {
            padding-right: 5px;
            padding-left: 5px;
        }
        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 4px;
        }
        .value {
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 6px;
            background: #f9f9f9;
        }
        table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            font-size: 8px;
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }
        th {
            background: #f1f1f1;
        }
        /* Rounded corners on table */
        thead tr:first-child th:first-child {
            border-top-left-radius: 10px;
        }
        thead tr:first-child th:last-child {
            border-top-right-radius: 10px;
        }
        tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }
        tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Member / Anggota Koperasi</h2>
    <div class="grid">
        <div class="col-6 field">
            <span class="label">Tanggal Bergabung</span>
            <div class="value">{{ \Carbon\Carbon::parse($member->tanggal_masuk)->format('d/m/Y') }}</div>
        </div>
        <div class="col-6 field">
            <span class="label">NIA</span>
            <div class="value">{{ str_pad($member->nia, 5, '0', STR_PAD_LEFT) }}</div>
        </div>
        <div class="col-6 field">
            <span class="label">NIK</span>
            <div class="value">{{ $member->nik  ?? '-'}}</div>
        </div>
        <div class="col-6 field">
            <span class="label">Nama Lengkap</span>
            <div class="value">{{ $member->nama_lengkap ?? '-'}}</div>
        </div>
        <div class="col-6 field">
            <span class="label">Tempat Lahir</span>
            <div class="value">{{ $member->tempat_lahir ?? '-' }}</div>
        </div>
        <div class="col-6 field">
            <span class="label">Tanggal Lahir</span>
            <div class="value">{{ \Carbon\Carbon::parse($member->tanggal_lahir)->format('d/m/Y') }}</div>
        </div>
        <div class="col-6 field">
            <span class="label">No. Handphone</span>
            <div class="value">{{ $member->no_hp ?? '-'}}</div>
        </div>
        <div class="col-6 field">
            <span class="label">Jenis</span>
            <div class="value">{{ $member->jenis->jenis ?? '-' }}</div>
        </div>
        <div class="col-6 field">
            <span class="label">Alamat</span>
            <div class="value">{{ $member->alamat ?? '-' }}</div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Simpanan Anggota</h2>
    <div class="grid">
        <div class="col-4 field">
            <span class="label">Simpanan Pokok</span>
            <div class="value">Rp {{ number_format($member->trxSimpananPokoks()->sum('kredit') - $member->trxSimpananPokoks()->sum('debit'), 0, ',', '.') }}</div>
        </div>
        <div class="col-4 field px-3">
            <span class="label">Simpanan Penyerta</span>
            <div class="value">Rp {{ number_format($member->trxSimpananPenyertas()->sum('kredit') - $member->trxSimpananPenyertas()->sum('debit'), 0, ',', '.') }}</div>
        </div>
        <div class="col-4 field">
            <span class="label">Simpanan Wajib</span>
            <div class="value">Rp {{ number_format($member->trxSimpananWajibs()->sum('kredit') - $member->trxSimpananWajibs()->sum('debit'), 0, ',', '.') }}</div>
        </div>
    </div>

    <h3>Riwayat Transaksi Simpanan</h3>
    <table style="border:1px;">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pokok</th>
                <th>Penyerta</th>
                <th>Wajib</th>
                <th>Saldo</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $saldo = 0; @endphp
            @foreach ($transactions as $i => $trx)
                @php
                    $subtotal = $trx['pokok'] + $trx['penyerta'] + $trx['wajib'];
                    $saldo += $subtotal;
                @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($trx['tanggal_trx'])->format('d M Y') }}</td>
                    <td>{{ number_format($trx['pokok'], 0, ',', '.') }}</td>
                    <td>{{ number_format($trx['penyerta'], 0, ',', '.') }}</td>
                    <td>{{ number_format($trx['wajib'], 0, ',', '.') }}</td>
                    <td>{{ number_format($saldo, 0, ',', '.') }}</td>
                    <td>{{ $trx['keterangan'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="card">
    <h2>Simpanan Sukarela</h2>

    <table>
        <thead>
            <tr>
                <th>No. Rek.</th>
                <th>Tanggal Terdaftar</th>
                <th>Saldo</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php $saldoSukarela = 0; @endphp
            @foreach ($sukarelas as $i => $sukarela)
                @php
                    
                    $noRek = str_pad($sukarela->no_rek, 5, '0', STR_PAD_LEFT);
                    $groupKode = $sukarela->groups ? $sukarela->groups->kode : '-';
                @endphp
                <tr>
                    <td>{{ $noRek }}/{{ $sukarela->groups->group }}</td>
                    <td>{{ \Carbon\Carbon::parse($sukarela->tanggal_terdaftar)->format('d M Y') }}</td>
                    <td>{{ number_format($sukarela->saldo, 0, ',', '.') }}</td>
                    <td>{{ $sukarela->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


<script>
    window.print();
</script>
</body>
</html>
