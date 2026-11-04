
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            size: 210mm 90mm;
            margin: 0;
        }

        html, body {
            width: 210mm;
            height: 90mm;
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #18364d;
        }

        .bilyet {
            position: relative;
            width: 210mm;
            height: 90mm;
            overflow: hidden;
            page-break-inside: avoid;
            background: #fff;
        }

        /* Bingkai */
        .frame {
            position: absolute;
            top: 2.5mm;
            left: 2.5mm;
            width: 205mm;
            height: 85mm;
            border: 1px solid #214e70;
            padding: 0;
        }

        .inner {
            position: relative;
            width: 100%;
            height: 100%;
        }

        /* Header */
        .header {
            position: absolute;
            top: 2mm;
            left: 3mm;
            right: 3mm;
            height: 14mm;
            padding-bottom:20px;
            border-bottom: 1px solid #214e70;
        }

        .logo {
            position: absolute;
            left: 0;
            top: 0;
            width: 16mm;
            height: 13mm;
        }

        .logo img {
            width: 17mm;
            height: 17mm;
        }

        .identity {
            position: absolute;
            left: 25mm;
            top: 1mm;
            width: 125mm;
        }

        .koperasi {
            font-size: 12px;
            font-weight: bold;
            color: #173f5f;
            letter-spacing: .3px;
        }

        .alamat {
            font-size: 8px;
            line-height: 1.2;
            margin-top: 1mm;
        }

        .title {
            position: absolute;
            right: 0;
            top: 0;
            width: 49mm;
            text-align: right;
        }

        .title h2 {
            margin: 0;
            font-size: 16px;
            color: #173f5f;
            letter-spacing: 1px;
        }

        .title .subtitle {
            font-size: 8px;
            margin-top: 1mm;
        }

        .title .number {
            font-size: 12px;
            font-weight: bold;
            margin-top: 1mm;
        }

        /* Data utama */
        .main-data {
            position: absolute;
            top: 25mm;
            left: 4mm;
            right: 4mm;
            height: 12mm;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table td {
            padding: 0 2mm;
            vertical-align: top;
            overflow: hidden;
        }

        .data-table td:first-child {
            padding-left: 0;
        }

        .label {
            font-size: 8px;
            color: #647887;
            margin-bottom: 1.2mm;
            white-space: nowrap;
        }

        .value {
            font-size: 12px;
            font-weight: bold;
            color: #173f5f;
            white-space: nowrap;
        }

        .amount {
            font-size: 12px;
            color: #0d4268;
        }

        /* Data tanggal */
        .date-data {
            position: absolute;
            top: 55mm;
            left: 4mm;
            right: 4mm;
            /* border-top: 1px dotted #a9b8c3; */
            padding-top: 2mm;
        }

        .date-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .date-table td {
            vertical-align: top;
            padding-right: 3mm;
        }

        /* Footer */
        .note {
            position: absolute;
            left: 4mm;
            bottom: 5mm;
            width: 112mm;
            font-size: 7px;
            font-style: italic;
            line-height: 1.5;
            color: #647887;
        }

        .signature {
            position: absolute;
            right: 4mm;
            bottom: 5mm;
            width: 48mm;
            text-align: center;
            font-size: 8px;
        }

        .signature-space {
            height: 10mm;
        }

        .signature-name {
            /* border-top: 1px solid #526575; */
            padding-top: 1mm;
            font-weight: bold;
        }

        .watermark {
            position: absolute;
            top: 25mm;
            left: 50%;
            width: 100mm;
            transform: translateX(-50%) rotate(-15deg);
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            color: #dce5eb;
            opacity: 0.20;
            z-index: 0;
        }

        .watermark img {
            display: block;
            width: 25mm;
            height: 25mm;
            object-fit: contain;
            margin: 0 auto 2mm auto;
        }

        .watermark div {
            letter-spacing: 1px;
        }
        .member-address {
            font-size: 6px;
            font-weight: normal;
            color: #647887;
            margin-top: 1.5mm;
            line-height: 1.3;
            white-space: normal;
        }
        .terbilang-data {
            position: absolute;
            top: 41mm;
            left: 4mm;
            right: 4mm;
            width: auto;
            padding: 2mm 0;
            border-top: 1px dotted #a9b8c3;
            border-bottom: 1px dotted #a9b8c3;
        }

        .terbilang-label {
            font-size: 8px;
            color: #647887;
            margin-bottom: 1mm;
            font-weight: bold;
        }

        .terbilang-value {
            font-size: 12px;
            color: #173f5f;
            font-style: italic;
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="bilyet">
    <div class="frame">
        <div class="inner">

            {{-- WATERMARK --}}
            <div class="watermark">
                <img src="{{ public_path('images/logo-koperasi.png') }}" alt="Logo Koperasi">
                <div>KSP. GUNUNG SARI SEDANA</div>
            </div>

            {{-- HEADER --}}
            <div class="header">
                <div class="logo">
                    <img src="{{ public_path('images/logo-koperasi.png') }}">
                </div>

                <div class="identity">
                    <div class="koperasi">
                        KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA
                    </div>

                    <div class="alamat">
                        Nomor Badan Hukum: 396 / BH / XXVII.9 / XII / 2015<br>
                        Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian,
                        Denpasar Barat | Telp. (0361) 8448574<br>
                        E-Mail: gunungsari.sedana@gmail.com
                    </div>
                </div>

                <div class="title">
                    <h2>BILYET</h2>
                    <div class="subtitle">SIMPANAN BERJANGKA</div>
                    <div class="number">
                        No. {{ $simpanan->kode_bilyet }}
                    </div>
                </div>
            </div>

            {{-- DATA UTAMA --}}
            <div class="main-data">
                <table class="data-table">
                    <tr>
                        <td width="29%">
                            <div class="label">NAMA ANGGOTA</div>

                            <div class="value">
                                {{ strtoupper($simpanan->nama_lengkap) }}
                            </div>

                            <div class="member-address">
                                {{ $simpanan->member->alamat ?? '-' }}
                            </div>
                        </td>

                        <td width="25%">
                            <div class="label">NOMINAL SIMPANAN</div>

                            <div class="value amount">
                                Rp {{ number_format($simpanan->nominal, 0, ',', '.') }}
                            </div>
                        </td>

                        <td width="17%">
                            <div class="label">BUNGA PER TAHUN</div>
                            <div class="value">
                                {{ number_format($simpanan->bunga_persen, 2, ',', '.') }}% p.a.
                            </div>
                        </td>

                        <td width="15%">
                            <div class="label">JANGKA WAKTU</div>
                            <div class="value">
                                {{ $simpanan->jangka_waktu }} Bulan
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            
            {{-- TERBILANG --}}
            <div class="terbilang-data">
                <div class="terbilang-label">TERBILANG</div>
                <div class="terbilang-value">
                    {{ $terbilangNominal }}
                </div>
            </div>


            {{-- TANGGAL --}}
            <div class="date-data">
                <table class="date-table">
                    <tr>
                        <td width="33%">
                            <div class="label">TANGGAL PENEMPATAN</div>
                            <div class="value">
                                {{ $tanggalMasuk->locale('id')->translatedFormat('d F Y') }}
                            </div>
                        </td>

                        <td width="33%">
                            <div class="label">TANGGAL JATUH TEMPO</div>
                            <div class="value">
                                {{ $tanggalJatuhTempo->locale('id')->translatedFormat('d F Y') }}
                            </div>
                        </td>

                        <td width="34%">
                            <!-- <div class="label">STATUS SIMPANAN</div>
                            <div class="value">AKTIF</div> -->
                        </td>
                    </tr>
                </table>
            </div>

            {{-- KETERANGAN --}}
            <div class="note">
                Bilyet ini merupakan bukti kepemilikan simpanan berjangka
                pada Koperasi Simpan Pinjam Gunung Sari Sedana. 
            </div>

            {{-- TANDA TANGAN --}}
            <div class="signature">
                Denpasar, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
                Ketua KSP. Gunung Sari Sedana

                <div class="signature-space"></div>

                <div class="signature-name">
                    ( I Made Sudiarta, S.Kom )
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
