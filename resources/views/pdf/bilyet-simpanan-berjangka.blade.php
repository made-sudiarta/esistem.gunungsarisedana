
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bilyet Simpanan Berjangka</title>

    <style>
        @page {
            size: 212mm 110mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            width: 212mm;
            height: 110mm;
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #18364d;
        }

        body {
            font-size: 9px;
        }

        /* AREA BILYET */
        .bilyet {
            position: absolute;
            top: 20mm;
            left: 12mm;
            width: 190mm;
            height: 73mm;
            overflow: hidden;
            background: #fff;
            page-break-inside: avoid;
        }

        .frame {
            position: absolute;
            inset: 0;
            width: 189mm;
            height: 73mm;
            /* border: 1px solid #214e70; */
        }

        .inner {
            position: relative;
            width: 100%;
            height: 100%;
        }

        /* JUDUL */
        .title {
            position: absolute;
            top: 3mm;
            left: 5mm;
            right: 5mm;
            text-align: center;
            padding-bottom: 2.5mm;
            /* border-bottom: 1px solid #214e70; */
        }

        .title h2 {
            margin: 0;
            font-size: 16px;
            color: #173f5f;
            letter-spacing: 2px;
        }

        .subtitle {
            font-size: 12px;
            font-weight: bold;
            margin-top: 1mm;
            text-decoration:underline;
        }

        .number {
            font-size: 8px;
            font-weight: bold;
            margin-top: 1mm;
        }

        /* WATERMARK */
        .watermark {
            position: absolute;
            top: 26mm;
            left: 50%;
            width: 65mm;
            margin-left: -32.5mm;
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            color: #dce5eb;
            opacity: 0.12;
            z-index: 0;
        }

        .watermark img {
            display: block;
            width: 18mm;
            height: 18mm;
            margin: 0 auto 1mm auto;
        }

        .watermark div {
            letter-spacing: 1px;
        }

        /* LABEL DAN NILAI */
        .label {
            font-size: 7px;
            color: #647887;
            margin-bottom: 1.5mm;
            font-weight: normal;
        }

        .value {
            font-size: 10px;
            font-weight: bold;
            color: #173f5f;
            line-height: 1.4;
        }

        /* DATA UTAMA */
        .main-data {
            position: absolute;
            top: 20mm;
            left: 5mm;
            right: 5mm;
            height: 17mm;
            z-index: 1;
        }

        .column {
            position: absolute;
            top: 0;
            height: 17mm;
            padding-right: 3mm;
            /* border-right: 1px dotted #a9b8c3; */
        }

        .column:last-child {
            border-right: none;
        }

        .col-member {
            left: 0;
            width: 70mm;
        }

        .col-nominal {
            left: 73mm;
            width: 45mm;
        }

        .col-bunga {
            left: 121mm;
            width: 31mm;
        }

        .col-jangka {
            left: 155mm;
            width: 32mm;
        }

        .member-name {
            font-size: 10px;
            font-weight: bold;
            color: #173f5f;
            text-transform: uppercase;
            line-height: 1.4;
        }

        .member-address {
            font-size: 7px;
            color: #647887;
            margin-top: 1.5mm;
            line-height: 1.3;
        }

        .amount {
            font-size: 13px;
            color: #0d4268;
            font-weight: bold;
        }

        /* TERBILANG */
        .terbilang-data {
            position: absolute;
            top: 35mm;
            left: 5mm;
            right: 5mm;
            height: 5mm;
            padding: 2mm 3mm;
            border-top: 1px dotted #a9b8c3;
            border-bottom: 1px dotted #a9b8c3;
            z-index: 1;
        }

        .terbilang-label {
            font-size: 7px;
            color: #647887;
            font-weight: bold;
            margin-bottom: 1mm;
            text-align:center;
        }

        .terbilang-value {
            font-size: 12px;
            color: #173f5f;
            font-style: italic;
            font-weight: bold;
            line-height: 1.3;
            text-transform: capitalize;
            text-align:center;
        }

        /* TANGGAL */
        .date-data {
            position: absolute;
            top: 54mm;
            left: 5mm;
            width: 105mm;
            z-index: 1;
        }

        .date-item {
            position: absolute;
            top: 0;
            width: 48mm;
        }

        .date-placement {
            left: 0;
        }

        .date-maturity {
            left: 53mm;
        }

        .date-item .value {
            font-size: 9px;
        }

        /* KETERANGAN */
        .note {
            position: absolute;
            left: 5mm;
            bottom: 3mm;
            width: 105mm;
            font-size: 6.5px;
            font-style: italic;
            line-height: 1.4;
            color: #647887;
            z-index: 1;
        }

        /* TANDA TANGAN */
        .signature {
            position: absolute;
            right: 5mm;
            bottom: 2mm;
            width: 65mm;
            text-align: center;
            font-size: 7px;
            z-index: 1;
        }

        .signature-space {
            height: 7mm;
        }

        .signature-name {
            padding-top: 0.5mm;
            font-weight: bold;
            text-decoration: underline;
            font-size: 8px;
        }
    </style>
</head>

<body>

@php
    $tanggalMasuk = \Carbon\Carbon::parse(
        $simpanan->tanggal_masuk
    );

    $tanggalJatuhTempo = $tanggalMasuk->copy()->addMonths(
        (int) $simpanan->jangka_waktu
    );

    $logoPath = public_path('images/logo-koperasi.png');

    $logoBase64 = null;

    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(
            file_get_contents($logoPath)
        );
    }
@endphp

<div class="bilyet">
    <div class="frame">
        <div class="inner">

            <!-- {{-- WATERMARK --}}
            @if($logoBase64)
                <div class="watermark">
                    <img src="{{ $logoBase64 }}" alt="Logo">
                    <div>KSP. GUNUNG SARI SEDANA</div>
                </div>
            @endif -->

            {{-- JUDUL --}}
            <div class="title">
                <!-- <h2>BILYET</h2> -->

                <div class="nomor">
                    No. Bilyet :
                </div>
                @php
                    $bulanRomawi = [
                        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
                        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
                        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
                    ];
                @endphp
                <div class="subtitle">
                    {{ $simpanan->kode_bilyet }}/ KGS / {{ $bulanRomawi[$simpanan->created_at->month] }} / {{ $simpanan->created_at->format('Y') }}
                </div>
            </div>

            {{-- DATA UTAMA --}}
            <div class="main-data">

                {{-- NAMA ANGGOTA --}}
                <div class="column col-member">
                    <div class="label">NAMA ANGGOTA</div>

                    <div class="member-name">
                        {{ strtoupper($simpanan->nama_lengkap) }}
                    </div>

                    <div class="member-address">
                        {{ $simpanan->member->alamat ?? '-' }}
                    </div>
                </div>

                {{-- NOMINAL --}}
                <div class="column col-nominal">
                    <div class="label">NOMINAL SIMPANAN</div>

                    <div class="value amount">
                        Rp {{ number_format(
                            $simpanan->nominal,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>
                </div>

                {{-- BUNGA --}}
                <div class="column col-bunga">
                    <div class="label">BUNGA PER TAHUN</div>

                    <div class="value">
                        {{ number_format(
                            $simpanan->bunga_persen,
                            2,
                            ',',
                            '.'
                        ) }}% p.a.
                    </div>
                </div>

                {{-- JANGKA WAKTU --}}
                <div class="column col-jangka">
                    <div class="label">JANGKA WAKTU</div>

                    <div class="value">
                        {{ $simpanan->jangka_waktu }} Bulan
                    </div>
                </div>

            </div>

            {{-- TERBILANG --}}
            <div class="terbilang-data">
                <!-- <div class="terbilang-label">
                    TERBILANG
                </div> -->

                <div class="terbilang-value">
                    {{ $terbilangNominal }}
                </div>
            </div>

            {{-- TANGGAL --}}
            <div class="date-data">

                <div class="date-item date-placement">
                    <div class="label">
                        TANGGAL PENEMPATAN
                    </div>

                    <div class="value">
                        {{ $tanggalMasuk->locale('id')->translatedFormat('d F Y') }}
                    </div>
                </div>

                <div class="date-item date-maturity">
                    <div class="label">
                        TANGGAL JATUH TEMPO
                    </div>

                    <div class="value">
                        {{ $tanggalJatuhTempo->locale('id')->translatedFormat('d F Y') }}
                    </div>
                </div>

            </div>

            {{-- KETERANGAN --}}
            <div class="note">
                Bilyet ini merupakan bukti kepemilikan simpanan
                berjangka pada Koperasi Simpan Pinjam
                Gunung Sari Sedana.
            </div>

            {{-- TANDA TANGAN --}}
            <div class="signature">
                Denpasar,
                {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}

                <br>

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
