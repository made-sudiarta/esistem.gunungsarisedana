
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kuitansi Pembayaran Kredit</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        html, body {
            margin: 0;
            padding: 30px;
            padding-bottom:0px;
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #222;
        }

        /*
        |--------------------------------------------------------------------------
        | UKURAN AREA CETAK
        |--------------------------------------------------------------------------
        | A4             : 210 x 297 mm
        | Margin         : 8 mm
        | Area cetak     : 194 x 281 mm
        | Kuitansi       : 194 x 140 mm
        | Garis potong   : 1 mm
        |--------------------------------------------------------------------------
        */

        .receipt-wrapper {
            width: 194mm;
            height: 281mm;
            margin: 0;
            padding: 0;
        }

        .receipt-item {
            width: 194mm;
            height: 140mm;
            margin: 0;
            padding: 0;
            overflow: hidden;
            page-break-inside: avoid;
        }

        /*
        |--------------------------------------------------------------------------
        | CONTAINER
        |--------------------------------------------------------------------------
        | Lebar total:
        | 179 + 7 + 7 + 0.5 + 0.5 = 194 mm
        |
        | Tinggi total:
        | 123 + 6 + 10 + 0.5 + 0.5 = 140 mm
        |--------------------------------------------------------------------------
        */

        .container {
            width: 179mm;
            height: 123mm;
            margin: 0;
            padding: 6mm 7mm 10mm 7mm;
            /* border: 0.5mm solid #333; */
            position: relative;
            overflow: hidden;
        }

        /*
        |--------------------------------------------------------------------------
        | GARIS POTONG
        |--------------------------------------------------------------------------
        */

        .cut-line {
            width: 194mm;
            height: 1mm;
            position: relative;
            margin: 0;
            padding: 0;
            border-top: 0.2mm dashed #777;
            text-align: center;
            font-size: 7px;
            line-height: 0;
            color: #777;
        }

        .cut-line span {
            position: relative;
            top: -2.5mm;
            padding: 0 8px;
            background: white;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 1.5px solid #333;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        .header td {
            vertical-align: middle;
        }

        .logo {
            width: 65px;
        }

        .logo img {
            width: 52px;
            height: auto;
        }

        .kop {
            text-align: center;
            width: 100%;
        }

        .kop h2 {
            font-size: 15px;
            margin: 0 0 3px;
            text-transform: uppercase;
        }

        .kop p {
            margin: 2px 0;
            font-size: 9px;
        }

        /*
        |--------------------------------------------------------------------------
        | JUDUL
        |--------------------------------------------------------------------------
        */

        .title {
            text-align: center;
            margin: 7px 0 8px;
        }

        .title h3 {
            font-size: 15px;
            margin: 0 0 3px;
            text-decoration: underline;
        }

        .title p {
            margin: 0;
            font-size: 9px;
        }

        /*
        |--------------------------------------------------------------------------
        | INFORMASI ANGGOTA
        |--------------------------------------------------------------------------
        */

        .info {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 5px;
        }

        .info td {
            padding: 3px;
            vertical-align: top;
            font-size: 9px;
        }

        /*
        |--------------------------------------------------------------------------
        | TABEL PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        .detail {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 4px;
        }

        .detail th,
        .detail td {
            border: 1px solid #444;
            padding: 5px;
            font-size: 9px;
        }

        .detail th {
            background: #eeeeee;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total {
            font-weight: bold;
            font-size: 10px;
            background: #eeeeee;
        }

        /*
        |--------------------------------------------------------------------------
        | TERBILANG
        |--------------------------------------------------------------------------
        */

        .terbilang {
            margin-top: 6px;
            padding: 6px;
            border: 1px solid #999;
            font-size: 9px;
            min-height: 25px;
        }

        .terbilang strong {
            margin-right: 5px;
        }

        /*
        |--------------------------------------------------------------------------
        | SALDO
        |--------------------------------------------------------------------------
        */

        .saldo {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 6px;
        }

        .saldo td {
            padding: 2px 3px;
            font-size: 9px;
        }

        /*
        |--------------------------------------------------------------------------
        | KETERANGAN
        |--------------------------------------------------------------------------
        */

        .catatan {
            margin-top: 4px;
            font-size: 8px;
            font-style: italic;
        }

        /*
        |--------------------------------------------------------------------------
        | TANDA TANGAN
        |--------------------------------------------------------------------------
        */

        .footer {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
        }

        .footer td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
            font-size: 9px;
        }

        .footer .label {
            height: 17px;
            margin: 0;
            padding: 0;
        }

        .footer .tanggal {
            height: 17px;
            margin: 0;
            padding: 0;
        }

        .footer .ttd {
            height: 10mm;
        }

        .footer .nama {
            display: block;
            border-top: 0;
            padding-top: 2px;
            font-weight: bold;
            min-height: 13px;
        }

    </style>
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | FORMAT RUPIAH
    |--------------------------------------------------------------------------
    */

    $formatRupiah = function ($nominal) {
        return 'Rp ' . number_format(
            (float) $nominal,
            0,
            ',',
            '.'
        );
    };


    /*
    |--------------------------------------------------------------------------
    | FUNGSI TERBILANG
    |--------------------------------------------------------------------------
    */

    $terbilang = function ($angka) use (&$terbilang) {

        $angka = abs((int) $angka);

        $huruf = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas'
        ];

        if ($angka < 12) {
            return $huruf[$angka];
        }

        if ($angka < 20) {
            return $terbilang($angka - 10) . ' belas';
        }

        if ($angka < 100) {
            return $terbilang(intdiv($angka, 10))
                . ' puluh '
                . $terbilang($angka % 10);
        }

        if ($angka < 200) {
            return 'seratus ' . $terbilang($angka - 100);
        }

        if ($angka < 1000) {
            return $terbilang(intdiv($angka, 100))
                . ' ratus '
                . $terbilang($angka % 100);
        }

        if ($angka < 2000) {
            return 'seribu ' . $terbilang($angka - 1000);
        }

        if ($angka < 1000000) {
            return $terbilang(intdiv($angka, 1000))
                . ' ribu '
                . $terbilang($angka % 1000);
        }

        if ($angka < 1000000000) {
            return $terbilang(intdiv($angka, 1000000))
                . ' juta '
                . $terbilang($angka % 1000000);
        }

        if ($angka < 1000000000000) {
            return $terbilang(intdiv($angka, 1000000000))
                . ' miliar '
                . $terbilang($angka % 1000000000);
        }

        return 'Angka terlalu besar';
    };


    /*
    |--------------------------------------------------------------------------
    | DATA TRANSAKSI
    |--------------------------------------------------------------------------
    */

    $tanggal = $transaksi->tanggal_transaksi
        ? \Carbon\Carbon::parse($transaksi->tanggal_transaksi)
            ->translatedFormat('d F Y')
        : '-';

    $namaAnggota = data_get(
        $transaksi,
        'kreditBulanan.member.nama_lengkap',
        '-'
    );

    $kredit = $transaksi->kreditBulanan;

    $tanggalPengajuan = $kredit?->tanggal_pengajuan
        ? \Carbon\Carbon::parse($kredit->tanggal_pengajuan)
        : null;

    $nomor = str_pad(
        data_get($kredit, 'no_pokok', '0'),
        5,
        '0',
        STR_PAD_LEFT
    );

    $romawi = [
        1 => 'I',
        2 => 'II',
        3 => 'III',
        4 => 'IV',
        5 => 'V',
        6 => 'VI',
        7 => 'VII',
        8 => 'VIII',
        9 => 'IX',
        10 => 'X',
        11 => 'XI',
        12 => 'XII',
    ];

    $nomorKredit = $tanggalPengajuan
        ? $nomor . '/KGS/' . $romawi[$tanggalPengajuan->month] . '/' . $tanggalPengajuan->format('Y')
        : $nomor . '/KGS';

    $pokok = (float) ($transaksi->pokok ?? 0);
    $bunga = (float) ($transaksi->bunga ?? 0);
    $denda = (float) ($transaksi->denda ?? 0);

    $total = (float) ($transaksi->nominal_bayar ?? 0);

    $namaPetugas = data_get($transaksi, 'user.name', '-');

    $nomorKuitansi = 'KWT/KB/' .
        str_pad($transaksi->id, 6, '0', STR_PAD_LEFT);

    $nilaiTerbilang = $total > 0
        ? ucfirst(trim($terbilang($total))) . ' rupiah'
        : 'Nol rupiah';

@endphp


<div class="receipt-wrapper">

    {{-- KUITANSI PERTAMA DAN KEDUA --}}

    @for($i = 1; $i <= 2; $i++)

        <div class="receipt-item">

            <div class="container">

                {{-- HEADER --}}

                <table class="header">
                    <tr>
                        <td class="logo">
                            <img src="{{ public_path('images/logo-koperasi.png') }}">
                        </td>

                        <td class="kop">
                            <h2>KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA</h2>
                            <p><b>Nomor Badan Hukum : 396 / BH / XXVII.9 / XII / 2015</b></p>
                            <p>Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian, Denpasar Barat | Telp. (0361) 8448574 | E-Mail : gunungsari.sedana@gmail.com</p>
                        </td>
                    </tr>
                </table>


                {{-- JUDUL --}}

                <div class="title">
                    <h3>KUITANSI PEMBAYARAN</h3>

                    <p>
                        Nomor: {{ $nomorKuitansi }}
                    </p>
                </div>


                {{-- INFORMASI ANGGOTA --}}

                <table class="info">

                    <tr>
                        <td width="18%">Telah diterima dari</td>
                        <td width="2%">:</td>

                        <td width="45%">
                            <strong>{{ $namaAnggota }}</strong>
                        </td>

                        <td width="15%">Tanggal</td>
                        <td width="20%">: {{ $tanggal }}</td>
                    </tr>

                    <tr>
                        <td>Nomor Kredit</td>
                        <td>:</td>
                        <td>{{ $nomorKredit }}</td>

                        <td>Petugas</td>
                        <td>: {{ $namaPetugas }}</td>
                    </tr>

                </table>


                {{-- RINCIAN PEMBAYARAN --}}

                <table class="detail">

                    <thead>
                        <tr>
                            <th width="8%">No</th>
                            <th width="57%">Uraian Pembayaran</th>
                            <th width="35%">Jumlah</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td align="center">1</td>

                            <td>Pembayaran Pokok Kredit</td>

                            <td class="text-right">
                                {{ $formatRupiah($pokok) }}
                            </td>
                        </tr>

                        <tr>
                            <td align="center">2</td>

                            <td>Pembayaran Bunga Kredit</td>

                            <td class="text-right">
                                {{ $formatRupiah($bunga) }}
                            </td>
                        </tr>

                        <tr>
                            <td align="center">3</td>

                            <td>Pembayaran Denda</td>

                            <td class="text-right">
                                {{ $formatRupiah($denda) }}
                            </td>
                        </tr>

                        <tr class="total">
                            <td colspan="2" class="text-right">
                                TOTAL PEMBAYARAN
                            </td>

                            <td class="text-right">
                                {{ $formatRupiah($total) }}
                            </td>
                        </tr>

                    </tbody>

                </table>


                {{-- TERBILANG --}}

                <div class="terbilang">
                    <strong>Terbilang:</strong>
                    {{ $nilaiTerbilang }}
                </div>


                {{-- SALDO KREDIT --}}

                <table class="saldo">

                    <tr>
                        <td width="18%">Saldo sebelum bayar</td>
                        <td width="2%">:</td>
                        <td>
                            {{ $formatRupiah($transaksi->saldo_awal) }}
                        </td>
                    </tr>

                    <tr>
                        <td>Saldo setelah bayar</td>
                        <td>:</td>
                        <td>
                            {{ $formatRupiah($transaksi->sisa_saldo) }}
                        </td>
                    </tr>

                </table>


                {{-- KETERANGAN --}}

                @if($transaksi->keterangan)

                    <div class="catatan">
                        Keterangan: {{ $transaksi->keterangan }}
                    </div>

                @endif


                {{-- TANDA TANGAN --}}

                <table class="footer">
                    <tr>
                        <td></td>
                        <td>
                            <p class="tanggal">
                                Denpasar, {{ $tanggal }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        {{-- PENYETOR --}}
                        <td>
                            <p class="label">Penyetor,</p>
                        </td>

                        {{-- PETUGAS --}}
                        <td>
                            
                            <p class="label">
                                Kasir / Petugas,
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="ttd"></div>
                        </td>

                        <td>
                            <div class="ttd"></div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <strong class="nama">{{ $namaAnggota }}</strong>
                        </td>

                        <td>
                            <strong class="nama">{{ $namaPetugas }}</strong>
                        </td>
                    </tr>
                </table>


                {{-- CATATAN --}}

                <div class="catatan">
                    * Kuitansi ini merupakan bukti pembayaran kredit
                    setelah pembayaran diterima dan dicatat oleh koperasi.
                </div>

            </div>

        </div>

        {{-- GARIS POTONG DI ANTARA KUITANSI --}}

        @if($i === 1)

            <div class="cut-line">
                <span>POTONG DI SINI</span>
            </div>

        @endif

    @endfor

</div>

</body>
</html>
