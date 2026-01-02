<!DOCTYPE html>
<html>
<head>
    <title>Data Pinjaman Harian</title>
   <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        @page {
            size: A4 landscape;
            margin: 15mm 12mm;
        }

        /* ===== KOP ===== */
        .kop {
            text-align: center;
            margin-bottom: 6px;
        }
        .kop h2 {
            margin: 0;
            font-size: 15px;
            text-transform: uppercase;
        }
        .kop p {
            margin: 2px 0;
        }

        .double-line {
            border: 0;
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            margin: 8px 0 12px;
            height: 3px;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        th, td {
            border-bottom: 1px solid #000;
            padding: 6px 5px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background: #eee;
            text-align: center;
            font-weight: bold;
        }

        /* Lebar kolom stabil */
        .col-no { width: 4%; }
        .col-pokok { width: 13%; }
        .col-nama { width: 18%; }
        .col-group { width: 7%; }
        .col-tgl { width: 12%; }
        .col-uang { width: 13%; }
        .col-status { width: 6%; }

        /* ===== TTD ===== */
        .ttd-wrapper {
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .ttd-table {
            width: 100%;
            text-align: center;
        }

        .ttd-table td {
            border: none;
        }

        .ttd-jabatan {
            font-weight: bold;
        }

        .ttd-nama {
            margin-top: 50px;
            font-weight: bold;
            text-decoration: underline;
        }
    </style>

</head>
<body>

{{-- KOP --}}
<div class="kop">
    <h2>KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA</h2>
    <p><b>BADAN HUKUM NO : 396/BH/XXVII.9/XII/2015</b></p>
    <p>Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian</p>
    <p>Telp. (0361) 8448574 | Email: gunungsari.sedana@gmail.com</p>
</div>

<hr class="double-line">

<h3 style="text-align:center; margin-bottom:15px;">DATA PINJAMAN HARIAN</h3>

<table>
    <thead>
        <tr>
            <th class="col-no">No</th>
            <th class="col-pokok">No Pokok</th>
            <th class="col-nama">Nama</th>
            <th class="col-group">Group</th>
            <th class="col-tgl">Tgl Pengajuan</th>
            <th class="col-tgl">Tgl Jatuh Tempo</th>
            <th class="col-uang">Plafond</th>
            <th class="col-uang">Sisa Pokok</th>
            <th class="col-status">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $row)
        @php
            $tgl = \Carbon\Carbon::parse($row->tanggal_pengajuan);
            $jatuhTempo = $tgl->copy()->addDays($row->jangka_waktu);

            $nomor = str_pad($row->no_pokok, 5, '0', STR_PAD_LEFT);
            $bulanRomawi = [
                '01'=>'I','02'=>'II','03'=>'III','04'=>'IV',
                '05'=>'V','06'=>'VI','07'=>'VII','08'=>'VIII',
                '09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII'
            ][$tgl->format('m')];
        @endphp
        <tr>
            <td align="center">{{ $i + 1 }}</td>
            <td align="center">{{ $nomor }}/KGSH/{{ $bulanRomawi }}/{{ $tgl->year }}</td>
            <td>{{ $row->nama_lengkap }}</td>
            <td align="center">{{ $row->group->group }}</td>
            <td align="center">{{ $tgl->translatedFormat('d F Y') }}</td>
            <td align="center">{{ $jatuhTempo->translatedFormat('d F Y') }}</td>
            <td align="right">Rp {{ number_format($row->plafond, 0, ',', '.') }}</td>
            <td align="right">Rp {{ number_format($row->sisa_pokok, 0, ',', '.') }}</td>
            <td align="center">{{ ucfirst($row->status) }}</td>
        </tr>
        @endforeach
    </tbody>

</table>

{{-- TANDA TANGAN --}}
<div class="ttd-wrapper">
    <table class="ttd-table">
        <tr>
            <td width="50%">
                <div class="ttd-jabatan">Ketua</div>
                <div class="ttd-nama">I Made Sudiarta, S.Kom</div>
            </td>
            <td width="50%">
                <div class="ttd-jabatan">
                    Denpasar, {{ now()->translatedFormat('d F Y') }}<br>
                    Bagian Kredit
                </div>
                <div class="ttd-nama">I Wayan Wartawan</div>
            </td>
        </tr>
    </table>
</div>

<script>
    window.print();
</script>

</body>
</html>
