<!DOCTYPE html>
<html>
<head>
    <title>Data Anggota Koperasi</title>
    <style>
        @page { size: A4; margin: 18mm 14mm; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 0;
        }

        /* KOP */
        .kop {
            display: table;
            width: 100%;
            padding-bottom: 10px;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
        }

        .kop-logo {
            display: table-cell;
            width: 90px;
            vertical-align: middle;
        }
        .kop-logo img {
            width: 75px;
            height: auto;
        }

        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding-right: 90px; /* biar judul bener-bener center walau ada logo */
        }

        .kop-text h2 {
            margin: 0;
            font-size: 14px;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .kop-text .line {
            margin: 2px 0;
            font-size: 10px;
            line-height: 1.25;
        }

        /* Header dokumen */
        .doc-title {
            text-align: center;
            margin: 10px 0 6px 0;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .meta {
            display: table;
            width: 100%;
            margin-bottom: 8px;
            font-size: 10px;
        }
        .meta .left { display: table-cell; text-align: left; }
        .meta .right { display: table-cell; text-align: right; }

        /* TABEL */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* biar proporsional */
        }

        thead { display: table-header-group; } /* header ulang tiap halaman */
        tfoot { display: table-footer-group; }

        th, td {
            border: 1px solid #444;
            padding: 6px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #f1f1f1;
            font-weight: 700;
            text-align: center;
            font-size: 10px;
        }

        tbody tr:nth-child(even) { background: #fafafa; }

        .nowrap { white-space: nowrap; }
        .center { text-align: center; }
        .right { text-align: right; }

        /* Lebar kolom (silakan sesuaikan) */
        .col-nia { width: 9%; }
        .col-nama { width: 25%; }
        .col-jenis { width: 10%; }
        .col-nik { width: 18%; }
        .col-saldo { width: 11%; }
        .col-pinjaman { width: 11%; }
        .col-ket { width: 8%; }

        /* Hindari row kepotong aneh saat print */
        tr, td, th { page-break-inside: avoid; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="kop">
        <div class="kop-logo">
            <img src="{{ asset('images/logo-koperasi.png') }}" alt="Logo Koperasi">
            <!-- untuk dompdf: <img src="{{ public_path('images/logo-koperasi.png') }}" ...> -->
        </div>
        <div class="kop-text">
            <h2>KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA</h2>
            <div class="line">Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian · Telp (0361) 8448574</div>
            <div class="line">E-Mail: gunungsari.sedana@gmail.com · www.gunungsarisedana.com</div>
        </div>
    </div>

    <div class="doc-title">Daftar Anggota Koperasi</div>

    <div class="meta">
        <div class="left">Total Anggota: {{ $members->count() }}</div>
        <div class="right">Dicetak: {{ now()->format('d M Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-nia">NIA</th>
                <th class="col-nik">NIK</th>
                <th class="col-nama">Nama Lengkap</th>
                <th class="col-jenis">Jenis</th>
                <th class="col-saldo">Saldo</th>
                <th class="col-pinjaman">Pinjaman</th>
                <th class="col-ket">Ket.</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($members as $member)
                <tr>
                    <td class="center nowrap">{{ str_pad($member->nia, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $member->nik }}</td>
                    <td>{{ $member->nama_lengkap }}</td>
                    <td class="center">{{ $member->jenis->keterangan ?? '-' }}</td>
                    <td class="right"></td>
                    <td class="right"></td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Tidak ada data ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>