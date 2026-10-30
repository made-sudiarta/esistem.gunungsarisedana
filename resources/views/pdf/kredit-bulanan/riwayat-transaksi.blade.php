
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi Kredit Bulanan</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 30px 35px 40px 35px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #263238;
            line-height: 1.45;
        }

        /* =========================
           KOP KOPERASI
        ========================= */

        .header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #263b32;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header td {
            border: none;
            vertical-align: middle;
        }

        .logo {
            width: 85px;
            text-align: left;
        }

        .logo img {
            width: 72px;
            height: 72px;
        }

        .company {
            text-align: center;
        }

        .company h3 {
            margin: 0;
            font-size: 12px;
            font-weight: normal;
            letter-spacing: 1px;
        }

        .company h1 {
            margin: 2px 0 4px;
            font-size: 21px;
            color: #234b39;
            letter-spacing: 1px;
        }

        .company .legal {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .company .address {
            font-size: 8px;
            color: #555;
        }

        /* =========================
           JUDUL LAPORAN
        ========================= */

        .report-title-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 10px 0 15px 0;
        }

        .report-title-table td {
            width: 100%;
            text-align: center !important;
            vertical-align: middle;
            padding: 0;
            border: none;
        }

        .report-title-table h2 {
            font-size: 15px;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            text-align: center;
        }

        .report-title-table p {
            font-size: 9px;
            margin: 2px 0 0;
            padding: 0;
            text-align: center;
        }

        /* =========================
           INFORMASI KREDIT
        ========================= */

        .info-wrapper {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin: 0 -8px 15px;
        }

        .info-card {
            width: 50%;
            border: 1px solid #dce5df;
            background-color: #f7faf8;
            padding: 10px 12px;
            vertical-align: top;
        }

        .info-title {
            font-size: 10px;
            font-weight: bold;
            color: #234b39;
            border-bottom: 1px solid #dce5df;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 0;
            border: none;
            vertical-align: top;
        }

        .info-table .label {
            width: 37%;
            color: #65736b;
        }

        .info-table .value {
            font-weight: bold;
            color: #263238;
        }

        /* =========================
           STATUS
        ========================= */

        .status {
            font-weight: bold;
            color: #234b39;
        }

        /* =========================
           TABEL TRANSAKSI
        ========================= */

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #234b39;
            margin: 15px 0 8px;
        }

        .transaksi {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 8px;
        }

        .transaksi thead {
            display: table-header-group;
        }

        .transaksi th {
            background: #294d3c;
            color: #fff;
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #294d3c;
        }

        .transaksi td {
            padding: 7px 5px;
            border: 1px solid #dce3de;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .transaksi tbody tr:nth-child(even) {
            background: #f5f8f6;
        }

        .transaksi .number {
            text-align: right;
            white-space: nowrap;
        }

        .transaksi .center {
            text-align: center;
        }

        .transaksi tfoot td {
            background: #eaf1ec;
            font-weight: bold;
            border: 1px solid #cbd8cf;
            padding: 8px 5px;
        }

        .empty {
            text-align: center;
            padding: 18px !important;
            color: #777;
        }

        /* =========================
           RINGKASAN
        ========================= */

        .summary {
            width: 100%;
            margin-top: 13px;
            border-collapse: separate;
            border-spacing: 5px 0;
        }

        .summary td {
            border: 1px solid #dce5df;
            background: #f7faf8;
            padding: 8px;
            width: 25%;
            vertical-align: middle;
        }

        .summary-label {
            font-size: 8px;
            color: #65736b;
        }

        .summary-value {
            font-size: 11px;
            font-weight: bold;
            color: #234b39;
            margin-top: 3px;
        }

        /* =========================
           TANDA TANGAN
        ========================= */

        .footer {
            width: 100%;
            margin-top: 25px;
        }

        .footer td {
            border: none;
            vertical-align: top;
        }

        .signature {
            width: 220px;
            text-align: center;
        }

        .signature .date {
            margin-bottom: 55px;
        }

        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }

        .print-info {
            color: #777;
            font-size: 8px;
        }

        /* =========================
           CETAK / HALAMAN
        ========================= */

        tr {
            page-break-inside: avoid;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .page-footer {
            position: fixed;
            bottom: -23px;
            left: 0;
            right: 0;
            border-top: 1px solid #dce3de;
            padding-top: 5px;
            font-size: 8px;
            color: #888;
        }
    </style>
</head>

<body>

    {{-- KOP KOPERASI --}}
    <table class="header">
        <tr>
            <td class="logo">
                <img src="{{ public_path('images/logo-koperasi.png') }}">
            </td>

            <td class="company">
                <h3 style="margin: 0; font-size: 11px; line-height: 1.1;">
                    KOPERASI SIMPAN PINJAM
                </h3>

                <h1 style="margin: 1px 0; font-size: 21px; line-height: 1.1;">
                    GUNUNG SARI SEDANA
                </h1>

                <div class="legal" style="margin: 1px 0; line-height: 1.1;">
                    Badan Hukum No. 396/BH/XXVII.9/XII/2015
                </div>

                <div class="address" style="font-size: 8px; line-height: 1.2; margin: 0;">
                    Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian
                    <br>
                    E-Mail: gunungsari.sedana@gmail.com
                    | Telp. (0361) 8448574
                </div>
            </td>

        </tr>
    </table>

    {{-- JUDUL --}}
    <table class="report-title-table" width="100%" style="margin-left:50px;">
        <tr>
            <td align="center">
                <h2>RIWAYAT TRANSAKSI PINJAMAN BULANAN</h2>
                <p>Laporan Riwayat Pembayaran Anggota Koperasi</p>
            </td>
        </tr>
    </table>

    {{-- INFORMASI KREDIT --}}
    <table class="info-wrapper">
        <tr>
            <td class="info-card">
                <div class="info-title">INFORMASI PEMINJAM</div>

                <table class="info-table">
                    <tr>
                        <td class="label">No. Pokok</td>
                        <td class="value">: {{ $kredit->no_pokok }}</td>
                    </tr>

                    <tr>
                        <td class="label">Nama Peminjam</td>
                        <td class="value">: {{ $kredit->member->nama_lengkap ?? '-' }}</td>
                    </tr>

                    <tr>
                        <td class="label">Status Kredit</td>
                        <td class="value">
                            : <span class="status">{{ strtoupper($kredit->status) }}</span>
                        </td>
                    </tr>
                </table>
            </td>

            <td class="info-card">
                <div class="info-title">INFORMASI PINJAMAN</div>

                <table class="info-table">
                    <tr>
                        <td class="label">Plafond</td>
                        <td class="value">
                            : Rp {{ number_format($kredit->plafond, 0, ',', '.') }}
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Tanggal Pengajuan</td>
                        <td class="value">
                            : {{ $kredit->tanggal_pengajuan?->format('d-m-Y') ?? '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td class="label">Jatuh Tempo</td>
                        <td class="value">
                            : {{ $kredit->tanggal_jatuh_tempo?->format('d-m-Y') ?? '-' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- TABEL TRANSAKSI --}}
    <div class="section-title">
        DETAIL RIWAYAT PEMBAYARAN
    </div>

    <table class="transaksi">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="10%">Tanggal</th>
                <th width="12%">Saldo Awal</th>
                <th width="11%">Pokok</th>
                <th width="9%">Bunga</th>
                <th width="13%">Nominal Bayar</th>
                <th width="9%">Denda</th>
                <th width="12%">Sisa Saldo</th>
                <th width="20%">Keterangan</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($transaksi as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>

                    <td class="center">
                        {{ $item->tanggal_transaksi?->format('d-m-Y') ?? '-' }}
                    </td>

                    <td class="number">
                        {{ number_format($item->saldo_awal, 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($item->pokok, 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($item->bunga, 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($item->nominal_bayar, 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($item->denda, 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($item->sisa_saldo, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ $item->keterangan ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty">
                        Belum ada riwayat transaksi pembayaran.
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if ($transaksi->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="3" class="center">TOTAL</td>

                    <td class="number">
                        {{ number_format($transaksi->sum('pokok'), 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($transaksi->sum('bunga'), 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($transaksi->sum('nominal_bayar'), 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($transaksi->sum('denda'), 0, ',', '.') }}
                    </td>

                    <td colspan="2"></td>
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- RINGKASAN --}}
    @if ($transaksi->isNotEmpty())
        <table class="summary">
            <tr>
                <td>
                    <div class="summary-label">TOTAL PEMBAYARAN</div>
                    <div class="summary-value">
                        Rp {{ number_format($transaksi->sum('nominal_bayar'), 0, ',', '.') }}
                    </div>
                </td>

                <td>
                    <div class="summary-label">TOTAL POKOK DIBAYAR</div>
                    <div class="summary-value">
                        Rp {{ number_format($transaksi->sum('pokok'), 0, ',', '.') }}
                    </div>
                </td>

                <td>
                    <div class="summary-label">TOTAL BUNGA</div>
                    <div class="summary-value">
                        Rp {{ number_format($transaksi->sum('bunga'), 0, ',', '.') }}
                    </div>
                </td>

                <td>
                    <div class="summary-label">SISA SALDO TERAKHIR</div>
                    <div class="summary-value">
                        Rp {{ number_format($transaksi->last()->sisa_saldo, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    @endif

    {{-- TANDA TANGAN --}}
    <table class="footer">
        <tr>
            <td>
                <div class="print-info">
                    Jumlah transaksi: {{ $transaksi->count() }} kali
                    <br>
                    Dokumen ini dicetak melalui sistem informasi koperasi.
                </div>
            </td>

            <td class="signature">
                <div class="date">
                    Denpasar, {{ now()->translatedFormat('d F Y') }}
                    <br>
                    Mengetahui,
                </div>

                <div class="name">
                    (........................................)
                </div>
                <div>Pengurus Koperasi</div>
            </td>
        </tr>
    </table>

    {{-- FOOTER HALAMAN --}}
    <div class="page-footer">
        KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA
        <span style="float: right;">
            Laporan Riwayat Kredit Bulanan
        </span>
    </div>

</body>
</html>
