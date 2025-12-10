<!DOCTYPE html>
<html>
<head>
    <title>Daftar Anggota</title>
    <!-- <style>
        body {
            font-family: sans-serif;
        }
        table {
            width: 100%;
            /* border-collapse: collapse; */
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px;
            /* text-align: left; */
        }
        tbody td{
            padding-top:5px;
            padding-bottom:5px;
            font-size:7pt;
        }
        thead tr{
            font-size:8pt;
        }
        th {
            background-color: #f4f4f4;
        }

        @media print {
            @page {
                size: A4 landscape; /* ✅ Kertas A4 dan orientasi landscape */
                /* margin: 1cm;         ✅ Atur margin agar cetakan rapi */
            }

            body {
                margin: 0;
            }

            button {
                display: none; /* ✅ Sembunyikan tombol saat dicetak */
            }
        }
    </style> -->
    <style>
    body {
        font-family: sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    th {
        background-color: #f4f4f4;
        -webkit-print-color-adjust: exact;
    }
    thead {
        display: table-header-group;
    }
    tr {
        page-break-inside: avoid;
    }




    th, td {
        border: 1px solid #ccc;
        padding: 4px 6px;
        text-align: left;
        font-size: 7pt;
        line-height: 1.2;
        height: 15px; /* Tinggi baris tetap */
        vertical-align: middle;
    }

    thead th {
        background-color: #f4f4f4;
        font-size: 8pt;
        height: 24px;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 1cm;
        }

        body {
            margin: 0;
        }

        button {
            display: none;
        }
    }
</style>


</head>
<body>
    <h4>Daftar Anggota Koperasi</h4>
    <button onclick="window.print()">🖨️ Print Sekarang</button>

    <table>
        <thead>
            <tr>
                <th width="1" style="text-align:center">#</th>
                <th width="5">NIA</th>
                <th width="80">Terdaftar</th>
                <th width="200">Nama Lengkap</th>
                <th width="100">NIK</th>
                <th width="100">Jenis</th>
                <th width="80">No HP</th>
                <th width="80">Pokok</th>
                <th width="80">Penyerta</th>
                <th width="80">Wajib</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            @foreach($members as $member)
                <tr>
                    <td style="text-align:center">{{ $no }}</td>
                    <td>{{ str_pad($member->nia, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ \Carbon\Carbon::parse($member->tanggal_bergabung)->format('d M Y') }}</td>
                    <td>{{ $member->nama_lengkap }}</td>
                    <td>{{ $member->nik }}</td>
                    <td>{{ $member->jenis->jenis ?? '-' }}</td>
                    <td>{{ $member->no_hp }}</td>
                    <td>
                        @php
                            $debit = $member->trxSimpananPokoks->sum('debit');
                            $kredit = $member->trxSimpananPokoks->sum('kredit');
                            $saldo = $kredit - $debit;
                        @endphp
                        Rp. {{ number_format($saldo, 0, ',', '.') }}
                    </td>
                    <td>
                        @php
                            $debit = $member->trxSimpananPenyertas->sum('debit');
                            $kredit = $member->trxSimpananPenyertas->sum('kredit');
                            $saldo = $kredit - $debit;
                        @endphp
                        Rp. {{ number_format($saldo, 0, ',', '.') }}
                    </td>
                    <td>
                        @php
                            $debit = $member->trxSimpananWajibs->sum('debit');
                            $kredit = $member->trxSimpananWajibs->sum('kredit');
                            $saldo = $kredit - $debit;
                        @endphp
                        Rp. {{ number_format($saldo, 0, ',', '.') }}
                    </td>

                </tr>
                <?php $no++; ?>
            @endforeach
        </tbody>
    </table>
</body>
</html>
