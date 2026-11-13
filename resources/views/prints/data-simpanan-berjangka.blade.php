
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print Data Simpanan Berjangka</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 9mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            font-size: 8pt;
            color: #222;
            margin: 0;
        }

        /* =====================================
           HEADER KOPERASI
        ===================================== */

        .report-header {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .header-table td {
            border: none;
            padding: 2px;
            vertical-align: middle;
        }

        .report-logo {
            width: 65px;
            text-align: center;
        }

        .report-logo img {
            width: 55px;
            height: 55px;
        }

        .report-identity {
            text-align: center;
        }

        .report-identity h2 {
            font-size: 11pt;
            font-weight: bold;
            margin: 0 0 4px 0;
        }

        .report-identity p {
            font-size: 7pt;
            margin: 2px 0;
        }

        /* =====================================
           JUDUL LAPORAN
        ===================================== */

        .page-title {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            margin-top: 5px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .page-subtitle {
            text-align: center;
            font-size: 8pt;
            margin-top: 0;
            margin-bottom: 15px;
        }

        /* =====================================
           TABEL DATA SIMPANAN
        ===================================== */

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.5pt;
            table-layout: fixed;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table thead th {
            background-color: #e9ecef;
            border: 1px solid #999;
            padding: 6px 2px;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }

        .data-table tbody td {
            border-bottom: 1px solid #ccc;
            padding: 6px 2px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .grand-total td {
            background-color: #e9ecef;
            border-top: 2px solid #555 !important;
            border-bottom: 1px solid #555 !important;
            font-weight: bold;
            padding: 8px 3px !important;
        }

        /* =====================================
           TANDA TANGAN PENERIMA BUNGA
        ===================================== */

        .signature-cell {
            height: 55px;
            vertical-align: bottom !important;
            text-align: center;
            font-size: 6pt;
        }

        .signature-line {
            border-bottom: 1px solid #777;
            width: 90%;
            margin: 0 auto 4px auto;
        }

        /* =====================================
           TANDA TANGAN KETUA DAN KOLEKTOR
           HALAMAN DATA SIMPANAN
        ===================================== */

        .data-signature {
            width: 100%;
            margin-top: 18px;
            margin-bottom: 10px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .data-signature td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 50%;
            font-size: 8pt;
        }

        .signature-space {
            height: 65px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .signature-position {
            font-size: 7pt;
            margin-top: 3px;
        }

        /* =====================================
           HALAMAN REKAPITULASI
        ===================================== */

        .rekap-page {
            page-break-before: always;
        }

        .rekap-header {
            text-align: center;
            margin-top: 5px;
            margin-bottom: 20px;
        }

        .rekap-header h2 {
            font-size: 12pt;
            margin: 0 0 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .rekap-header h3 {
            font-size: 10pt;
            margin: 0 0 7px 0;
            font-weight: bold;
        }

        .rekap-header p {
            font-size: 8pt;
            margin: 0;
            color: #555;
        }

        /* =====================================
           INFORMASI REKAP
        ===================================== */

        .rekap-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .rekap-info td {
            border: none;
            padding: 5px 3px;
        }

        /* =====================================
           TABEL REKAP PECAHAN UANG
        ===================================== */

        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .rekap-table thead {
            display: table-header-group;
        }

        .rekap-table thead th {
            background-color: #e9ecef;
            border: 1px solid #999;
            padding: 9px 6px;
            text-align: center;
            font-weight: bold;
        }

        .rekap-table tbody td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        .rekap-table .total-row td {
            background-color: #e9ecef;
            border-top: 2px solid #555;
            border-bottom: 1px solid #555;
            font-weight: bold;
            padding: 10px 8px;
        }

        /* =====================================
           TOTAL KEBUTUHAN UANG
        ===================================== */

        .rekap-total-box {
            margin-top: 15px;
            padding: 12px 15px;
            border: 1px solid #999;
            background-color: #f5f6f7;
            page-break-inside: avoid;
        }

        .rekap-total-label {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .rekap-total-value {
            font-size: 17pt;
            font-weight: bold;
        }

        .rekap-note {
            font-size: 7pt;
            color: #666;
            margin-top: 10px;
            line-height: 1.5;
        }

    </style>
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | PENGATURAN LAPORAN
    |--------------------------------------------------------------------------
    */

    $tanggalCetak = \Carbon\Carbon::now()->locale('id');

    $bulanSekarang = $tanggalCetak->format('m-Y');

    $alamat = 'Jl. Gunung Guntur Gg.XIX No. 9 Padangsambian';

    $ketua = 'I Made Sudiarta, S.Kom';

    $petugas = $namaPetugas ?? '................................';


    /*
    |--------------------------------------------------------------------------
    | DAFTAR PECAHAN UANG
    |--------------------------------------------------------------------------
    */

    $pecahanUang = [
        100000,
        50000,
        20000,
        10000,
        5000,
        2000,
        1000,
        500,
        200,
        100,
    ];


    /*
    |--------------------------------------------------------------------------
    | INISIALISASI TOTAL
    |--------------------------------------------------------------------------
    */

    $totalLembar = array_fill_keys($pecahanUang, 0);

    $totalNominal = 0;

    $totalBunga = 0;

    $totalSisaKecil = 0;


    /*
    |--------------------------------------------------------------------------
    | HITUNG BUNGA DAN AKUMULASI PECAHAN UANG
    |--------------------------------------------------------------------------
    */

    foreach ($records as $r) {

        $bulanMasuk = \Carbon\Carbon::parse(
            $r->tanggal_masuk
        )->format('m-Y');

        if ($bulanMasuk == $bulanSekarang) {

            $bungaRupiah = 0;

        } else {

            $bungaRupiah = $r->nominal *
                ($r->bunga_persen / 12 / 100);

        }

        // Pembulatan ke bawah rupiah penuh
        $bungaRupiah = (int) floor($bungaRupiah);

        // Total simpanan dan bunga
        $totalNominal += $r->nominal;

        $totalBunga += $bungaRupiah;


        /*
        |--------------------------------------------------------------------------
        | PECAHAN UANG PER NASABAH
        |--------------------------------------------------------------------------
        */

        $sisaBunga = $bungaRupiah;

        foreach ($pecahanUang as $pecahan) {

            $jumlahLembar = intdiv(
                $sisaBunga,
                $pecahan
            );

            $totalLembar[$pecahan] += $jumlahLembar;

            $sisaBunga %= $pecahan;

        }

        // Sisa nominal di bawah Rp100
        $totalSisaKecil += $sisaBunga;

    }


    /*
    |--------------------------------------------------------------------------
    | TOTAL KEBUTUHAN UANG TUNAI
    |--------------------------------------------------------------------------
    */

    $totalUangTunai = 0;

    foreach ($totalLembar as $pecahan => $jumlah) {

        $totalUangTunai += $pecahan * $jumlah;

    }

    $totalLembarSemua = array_sum($totalLembar);

    $logoPath = public_path('images/logo-koperasi.png');

    $logoBase64 = null;

    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(
            file_get_contents($logoPath)
        );
    }
@endphp


<!-- =====================================================
     HALAMAN 1
     DATA SIMPANAN BERJANGKA
====================================================== -->


<!-- HEADER KOPERASI -->

<div class="report-header">

    <table class="header-table">
        <tr>

            <td class="report-logo">
                <img src="{{ public_path('images/logo-koperasi.png') }}" style="width: 55px; height: 55px;">
            </td>

            <td class="report-identity">

                <h2>
                    KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA
                </h2>

                <p>
                    {{ $alamat }}
                </p>

                <p>
                    DENPASAR - BALI
                </p>

            </td>

            <td width="65"></td>

        </tr>
    </table>

</div>


<!-- JUDUL LAPORAN -->

<div class="page-title">
    {{ $title }}
</div>

<div class="page-subtitle">
    Periode {{ $tanggalCetak->translatedFormat('F Y') }}
</div>


<!-- TABEL DATA SIMPANAN -->

<table class="data-table">

    <thead>
        <tr>

            <th width="4%">No</th>

            <th width="15%">Kode Bilyet</th>

            <th width="22%">Nama Lengkap</th>

            <th width="20%">Alamat Nasabah</th>

            <th width="11%">Jatuh Tempo</th>

            <th width="7%">Bunga</th>

            <th width="15%">Nominal Simpanan</th>

            <th width="13%">Bunga (Rp)</th>

            <th width="13%">Tanda Tangan Penerima</th>

        </tr>
    </thead>

    <tbody>

        @foreach($records as $index => $r)

            @php

                $bulanMasuk = \Carbon\Carbon::parse(
                    $r->tanggal_masuk
                )->format('m-Y');

                if ($bulanMasuk == $bulanSekarang) {

                    $bungaRupiah = 0;

                } else {

                    $bungaRupiah = (int) floor(
                        $r->nominal *
                        ($r->bunga_persen / 12 / 100)
                    );

                }

            @endphp

            <tr>

                <td class="text-center">
                    {{ $index + 1 }}
                </td>

                <td class="text-center" style="font-size:6pt;">

                    {{ $r->kode_bilyet }}/{{ $r->group->group ?? '-' }}/KGS/{{ \Carbon\Carbon::parse($r->tanggal_masuk)->format('Y') }}

                </td>

                <td class="text-left">
                    {{ $r->nama_lengkap }}
                </td>

                <td class="text-left">
                    {{ $r->member->alamat ?? '-' }}
                </td>

                <td class="text-center">

                    {{ \Carbon\Carbon::parse($r->tanggal_masuk)
                        ->addMonths($r->jangka_waktu)
                        ->format('d-m-Y') }}

                </td>

                <td class="text-center">

                    {{ number_format($r->bunga_persen, 2, ',', '.') }}%

                </td>

                <td class="text-right">

                    {{ number_format($r->nominal, 0, ',', '.') }}

                </td>

                <td class="text-right">

                    {{ number_format($bungaRupiah, 0, ',', '.') }}

                </td>

                <!-- TANDA TANGAN SETIAP PENERIMA BUNGA -->

                <td class="signature-cell">

                    <div class="signature-line"></div>

                </td>

            </tr>

        @endforeach


        <!-- GRAND TOTAL -->

        <tr class="grand-total">

            <td colspan="6" class="text-center">
                GRAND TOTAL
            </td>

            <td class="text-right">

                {{ number_format($totalNominal, 0, ',', '.') }}

            </td>

            <td class="text-right">

                {{ number_format($totalBunga, 0, ',', '.') }}

            </td>

            <td></td>

        </tr>

    </tbody>

</table>


<!-- =====================================================
     TANDA TANGAN LAPORAN SIMPANAN BERJANGKA
     BERADA DI HALAMAN PERTAMA
====================================================== -->

<table class="data-signature">

    <tr>

        <td>

            Mengetahui,<br>

            Ketua Koperasi

            <div class="signature-space"></div>

            <div class="signature-name">
                {{ $ketua }}
            </div>

            <div class="signature-position">
                Ketua KSP Gunung Sari Sedana
            </div>

        </td>

        <td>

            Denpasar,
            {{ $tanggalCetak->translatedFormat('d F Y') }}<br>

            Kolektor

            <div class="signature-space"></div>

            <div class="signature-name">
                {{ $petugas }}
            </div>

            <div class="signature-position">
                Kolektor
            </div>

        </td>

    </tr>

</table>



<!-- =====================================================
     HALAMAN 2
     REKAPITULASI KEBUTUHAN UANG
     TANPA TANDA TANGAN
====================================================== -->

<div class="rekap-page">


    <!-- HEADER KOPERASI -->

    <div class="report-header">

        <table class="header-table">
            <tr>

                <td class="report-logo">

                    <img src="{{ public_path('images/logo-koperasi.png') }}">

                </td>

                <td class="report-identity">

                    <h2>
                        KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA
                    </h2>

                    <p>
                        {{ $alamat }}
                    </p>

                    <p>
                        DENPASAR - BALI
                    </p>

                </td>

                <td width="65"></td>

            </tr>
        </table>

    </div>


    <!-- JUDUL REKAPITULASI -->

    <div class="rekap-header">

        <h2>
            REKAPITULASI KEBUTUHAN UANG
        </h2>

        <h3>
            BUNGA SIMPANAN BERJANGKA
        </h3>

        <p>
            Periode {{ $tanggalCetak->translatedFormat('F Y') }}
        </p>

    </div>


    <!-- INFORMASI REKAP -->

    <table class="rekap-info">

        <tr>

            <td width="25%">
                Jumlah Simpanan
            </td>

            <td width="25%">
                : {{ $records->count() }} Nasabah
            </td>

            <td width="20%">
                Total Bunga
            </td>

            <td width="30%">
                : Rp {{ number_format($totalBunga, 0, ',', '.') }}
            </td>

        </tr>

        <tr>

            <td>
                Total Nominal Simpanan
            </td>

            <td>
                : Rp {{ number_format($totalNominal, 0, ',', '.') }}
            </td>

            <td>
                Total Lembar
            </td>

            <td>
                : {{ number_format($totalLembarSemua, 0, ',', '.') }} Lembar
            </td>

        </tr>

    </table>


    <!-- TABEL REKAP PECAHAN UANG -->

    <table class="rekap-table">

        <thead>

            <tr>

                <th width="10%">No</th>

                <th width="30%">Pecahan Uang</th>

                <th width="25%">Jumlah Lembar</th>

                <th width="35%">Total Nominal</th>

            </tr>

        </thead>

        <tbody>

            @foreach($pecahanUang as $index => $pecahan)

                <tr>

                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>

                    <td class="text-right">

                        Rp {{ number_format($pecahan, 0, ',', '.') }}

                    </td>

                    <td class="text-center">

                        {{ number_format($totalLembar[$pecahan], 0, ',', '.') }}

                        Lembar

                    </td>

                    <td class="text-right">

                        Rp {{ number_format(
                            $pecahan * $totalLembar[$pecahan],
                            0,
                            ',',
                            '.'
                        ) }}

                    </td>

                </tr>

            @endforeach


            <!-- TOTAL KESELURUHAN -->

            <tr class="total-row">

                <td colspan="2" class="text-center">

                    TOTAL KESELURUHAN

                </td>

                <td class="text-center">

                    {{ number_format($totalLembarSemua, 0, ',', '.') }}

                    Lembar

                </td>

                <td class="text-right">

                    Rp {{ number_format($totalUangTunai, 0, ',', '.') }}

                </td>

            </tr>

        </tbody>

    </table>


    <!-- TOTAL UANG YANG HARUS DISIAPKAN -->

    <div class="rekap-total-box">

        <div class="rekap-total-label">

            TOTAL KEBUTUHAN UANG TUNAI

        </div>

        <div class="rekap-total-value">

            Rp {{ number_format($totalUangTunai, 0, ',', '.') }}

        </div>

    </div>


    <!-- KETERANGAN -->

    <div class="rekap-note">

        <strong>Keterangan:</strong>

        <br>

        1. Perhitungan pecahan uang dilakukan berdasarkan bunga
        masing-masing nasabah Simpanan Berjangka.

        <br>

        2. Penghitungan pecahan uang dimulai dari nominal terbesar
        hingga nominal terkecil.

        <br>

        3. Sisa nominal di bawah Rp100 yang belum dapat diakomodasi
        oleh pecahan uang sebesar
        Rp {{ number_format($totalSisaKecil, 0, ',', '.') }}.

        <br>

        4. Total kebutuhan uang tunai merupakan akumulasi seluruh
        pecahan uang yang harus disiapkan oleh koperasi.

    </div>


</div>

</body>
</html>
