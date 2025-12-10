<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ID Card Anggota</title>
    <style>
        @page {
            size: 90mm 55mm;
            margin: 10px;
            /* margin-left: 15px; */
        }

        body {
            font-family: sans-serif;
            font-size: 7pt;
            margin: 0;
        }
        .container {
            display: flex;
            gap: 2mm; /* jarak antar depan dan belakang */
            justify-content: center; /* rata tengah container */
            padding: 10px;
        }
        .page {
            box-sizing: border-box;
            width: 90mm;
            height: 55mm;
            border: 1px solid #eee;
            /* hapus margin agar berdampingan rapat */
            margin: 0;
        }

        /* Halaman Depan */
        .card-front {
            background: url('{{ asset('images/cover-koperasi.png') }}') no-repeat center center;
            background-size: cover;
        }

        /* Halaman Belakang */
        .card-back {
            padding: 4mm;
            position: relative;
            background-color: white;
            overflow: hidden;
        }

        .card-back::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 50%;
            height: 50%;
            background: url('{{ asset('images/logo-koperasi.png') }}') no-repeat center center;
            background-size: 80%;
            opacity: 0.05;
            transform: translate(-50%, -50%);
            z-index: 0;
        }

        .card-back > * {
            position: relative;
            z-index: 1;
        }

        .row {
            display: flex;
            margin-bottom: 2mm;
            gap: 2mm;
        }

        .left {
            width: 20mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        img.foto {
            width: 20mm;
            height: 20mm;
            object-fit: cover;
            border-radius: 4px;
        }

        .right {
            flex: 1;
        }

        .right table {
            width: 100%;
            font-size: 6.5pt;
        }

        .right th {
            text-align: left;
            vertical-align: top;
            white-space: nowrap;
            font-weight: bold;
        }

        .signature {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 2mm;
        }

        .notes {
            width: 48%;
            font-size: 5pt;
        }

        .signature-right {
            width: 48%;
            text-align: center;
            font-size: 5pt;
        }

        .bold {
            font-weight: bold;
        }

        ol {
            padding-left: 14px;
            margin: 2mm 0 0 0;
        }

        ol li {
            margin-bottom: 0mm;
        }

    </style>
</head>
<body onload="window.print()">

    <div class="container">
        <!-- Halaman Depan -->
        <div class="page card-front"></div>

        <!-- Halaman Belakang -->
        <div class="page card-back">
            <div class="row">
                <div class="left">
                    <img src="{{ $record->photo ? asset('storage/' . $record->photo) : asset('images/user-placeholder.jpg') }}" class="foto">
                </div>
                <div class="right">
                    <table>
                        <tr>
                            <th>NO. INDUK</th>
                            <td>:</td>
                            <td>{{ str_pad($record->nia, 5, '0', STR_PAD_LEFT) }}/{{ $record->jenis->keterangan }}</td>
                        </tr>
                        <tr>
                            <th>IDENTITAS</th>
                            <td>:</td>
                            <td>{{ $record->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>NAMA</th>
                            <td>:</td>
                            <td>{{ strtoupper($record->nama_lengkap) }}</td>
                        </tr>
                        <tr>
                            <th>ALAMAT</th>
                            <td>:</td>
                            <td>{{ strtoupper($record->alamat) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="signature">
                <div class="notes">
                    <p class="bold">Catatan:</p>
                    <ol>
                        <li>Simpanan Pokok : Rp. 100.000,-</li>
                        <li>Memakai Jasa/Layanan Koperasi</li>
                        <li>Taat dan patuh kepada Anggaran Dasar, Anggaran Rumah Tangga & Peraturan Khusus</li>
                    </ol>
                </div>
                <div class="signature-right">
                    <p>Denpasar, {{ now()->format('d F Y') }}<br>Disahkan oleh,<br>Ketua</p>
                    <img src="{{ asset('images/ttd-ketua.png') }}" alt="Tanda Tangan Ketua" style="height: 30px;"><br>
                    <p class="bold">I Made Sudiarta, S.Kom</p>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>
