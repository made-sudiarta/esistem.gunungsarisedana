<div class="print-wrapper">

    <style media="print">
        @page {
            size: A4;
            margin: 15mm;
        }

        header, nav, aside, footer,
        .fi-topbar, .fi-sidebar, .fi-header {
            display: none !important;
        }

        body {
            background: white !important;
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 6px;
        }

        th {
            background: #f0f0f0;
        }

        /* HR DOUBLE */
        .double-line {
            border: none;
            border-top: 3px double #000;
            margin: 10px 0 15px 0;
        }

        /* KOP */
        .kop {
            text-align: center;
            margin-bottom: 10px;
        }

        .kop h2 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }

        .kop p {
            margin: 2px 0;
            font-size: 11px;
        }

        /* JUDUL */
        .title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0;
            font-size: 14px;
            text-decoration: underline;
        }

        /* INFO PINJAMAN */
        table.info-pinjaman td {
            padding-top: 2px;
            padding-bottom: 2px;
        }

        /* RIWAYAT */
        table.riwayat th {
            border-bottom: 2px solid #A5a5a5;
            text-align: center;
        }

        table.riwayat td {
            border-bottom: 1px solid #A5a5a5;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        /* TTD */
        .ttd-wrapper {
            margin-top: 50px;
            width: 100%;
        }

        .ttd-table {
            width: 100%;
            text-align: center;
        }

        .ttd-jabatan {
            /* font-weight: bold; */
        }

        .ttd-nama {
            margin-top: 70px;
            /* font-weight: bold; */
            text-decoration: underline;
        }
    </style>

    <script>
        window.onload = function () {
            window.print();
            setTimeout(() => window.close(), 500);
        };
    </script>

    @php
        use Carbon\Carbon;

        $bulanRomawi = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        $tglPengajuan = Carbon::parse($record->tanggal_pengajuan);

        $noPokokFormat =
            str_pad($record->no_pokok, 5, '0', STR_PAD_LEFT)
            . '/KGSH/'
            . $bulanRomawi[$tglPengajuan->month]
            . '/'
            . $tglPengajuan->year;

        $sisaPokok = $record->plafond + ($record->plafond*$record->bunga_persen/100)+ ($record->plafond*$record->admin_persen/100);
    @endphp

    {{-- KOP --}}
    <div class="kop">
        <h2>KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA</h2>
        <p><b>BADAN HUKUM NO : 396/BH/XXVII.9/XII/2015</b></p>
        <p>Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian</p>
        <p>Telp. (0361) 8448574 | Email: gunungsari.sedana@gmail.com</p>
    </div>

    <hr class="double-line">

    {{-- JUDUL --}}
    <div class="title">DETAIL PINJAMAN HARIAN</div>

    {{-- DATA PINJAMAN --}}
    <table class="info-pinjaman">
        <tr>
            <td width="30%">Nomor Pokok</td>
            <td width="5%">:</td>
            <td>{{ $noPokokFormat }}</td>
        </tr>
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td>{{ $record->nama_lengkap }}</td>
        </tr>
        <tr>
            <td>Plafond</td>
            <td>:</td>
            <td>Rp {{ number_format($record->plafond, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Sisa Pokok Kredit</td>
            <td>:</td>
            <td>
                Rp {{
                    number_format(
                        $sisaPokok - $record->transaksis->sum('jumlah'),
                        0, ',', '.'
                    )
                }}
            </td>
        </tr>
    </table>

    <br>

    {{-- RIWAYAT TRANSAKSI --}}
    <h4>Riwayat Transaksi</h4>

    <table class="riwayat">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Tanggal & Waktu</th>
                <th width="30%">Jumlah Bayar</th>
                <th width="30%">Sisa Pokok</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->transaksis->sortBy('tanggal_transaksi') as $trx)
                @php $sisaPokok -= $trx->jumlah; @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ Carbon::parse($trx->tanggal_transaksi)->format('d-m-Y H:i') }}</td>
                    <td class="text-right">
                        Rp {{ number_format($trx->jumlah, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format(max(0, $sisaPokok), 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
<!-- 
    {{-- TANGGAL --}}
    <div style="text-align:right; margin-top:30px; font-size:11px;">
        Denpasar, {{ now()->format('d F Y') }}
    </div> -->

    {{-- TANDA TANGAN --}}
    <div class="ttd-wrapper">
        <table class="ttd-table">
            <tr>
                <td width="50%">
                    <div class="ttd-jabatan"><br>Ketua</div>
                    <br>
                    <div class="ttd-nama">I Made Sudiarta, S.Kom</div>
                </td>
                <td width="50%">
                    <div class="ttd-jabatan">
                        Denpasar,  {{ now()->format('d F Y') }}<br>
                        Bagian Kredit</div>
                        <br>
                    <div class="ttd-nama">I Wayan Wartawan</div>
                </td>
            </tr>
        </table>
    </div>

</div>
