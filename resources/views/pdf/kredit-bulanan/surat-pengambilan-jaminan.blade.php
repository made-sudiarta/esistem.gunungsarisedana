
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pengambilan Jaminan</title>

    <style>
        @page {
            size: A4 portrait;
            margin: 35px 45px 40px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
            line-height: 1.2;
        }

        .header {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 3px double #263b32;
            padding-bottom: 10px;
            margin-bottom: 25px;
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
            width: 70px;
            height: 70px;
        }

        .company {
            text-align: center;
        }

        .company h3 {
            margin: 0;
            font-size: 11px;
            font-weight: normal;
        }

        .company h1 {
            margin: 2px 0;
            font-size: 20px;
            color: #234b39;
        }

        .company p {
            margin: 2px 0;
            font-size: 8px;
        }

        .title {
            text-align: center;
            margin: 25px 0;
        }

        .title h2 {
            font-size: 15px;
            text-decoration: underline;
            margin: 0 0 5px;
        }

        .title p {
            margin: 0;
        }

        .content {
            text-align: justify;
        }

        .identity {
            width: 100%;
            margin: 15px 0;
            border-collapse: collapse;
        }

        .identity td {
            padding: 3px 5px;
            vertical-align: top;
        }

        .identity .label {
            width: 145px;
        }

        .vehicle {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }

        .vehicle th,
        .vehicle td {
            border: 1px solid #999;
            padding: 6px 8px;
        }

        .vehicle th {
            background: #edf2ee;
            text-align: left;
            width: 38%;
        }

        .signatures {
            width: 100%;
            margin-top: 35px;
            border-collapse: collapse;
        }

        .signatures td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: none;
        }

        .sign-space {
            height: 50px;
        }

        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .footer {
            margin-top: 25px;
            font-size: 9px;
            color: #777;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 7px;
        }
    </style>
</head>

<body>

    {{-- KOP SURAT --}}
    <table class="header" style="margin-bottom: 10px; padding-bottom: 5px;">
        <tr>
            <td class="logo" style="width: 75px; padding: 0;">
                <img
                    src="{{ public_path('images/logo-koperasi.png') }}"
                    style="width: 65px; height: 65px;"
                >
            </td>

            <td class="company" style="padding: 0; text-align: center;">
                <h3 style="margin: 0; font-size: 10px; line-height: 1.1;">
                    KOPERASI SIMPAN PINJAM
                </h3>

                <h1 style="margin: 1px 0; font-size: 19px; line-height: 1.1;">
                    GUNUNG SARI SEDANA
                </h1>

                <p style="margin: 1px 0; font-size: 8px; line-height: 1.1;">
                    <b>Badan Hukum No. 396/BH/XXVII.9/XII/2015</b>
                </p>

                <p style="margin: 1px 0 0; font-size: 7px; line-height: 1.2;">
                    Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian
                    <br>
                    E-Mail: gunungsari.sedana@gmail.com
                    | Telp. (0361) 8448574
                </p>
            </td>

            <td style="width: 75px; padding: 0;"></td>
        </tr>
    </table>

    {{-- JUDUL SURAT --}}
    <div class="title" style="margin: 12px 0 18px; padding: 0;">
        <h2 style="
            font-size: 14px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
            text-decoration: underline;
        ">
            SURAT PENGAMBILAN JAMINAN
        </h2>

        <p style="
            font-size: 10px;
            margin: 2px 0 0;
            padding: 0;
            line-height: 1.2;
        ">
            Nomor: {{ $surat->nomor_surat }}
        </p>
    </div>

    <div class="content">

        <p>
            Yang bertanda tangan di bawah ini, Pengurus
            <b>Koperasi Simpan Pinjam Gunung Sari Sedana</b>,
            menerangkan bahwa anggota berikut:
        </p>

        {{-- IDENTITAS ANGGOTA --}}
        <table class="identity">
            <tr>
                <td class="label">Nama Anggota</td>
                <td>: {{ $kredit->member->nama_lengkap ?? '-' }}</td>
            </tr>

            <tr>
                <td class="label">Nomor Pokok</td>
                <td>: {{ $kredit->no_pokok }}</td>
            </tr>

            <tr>
                <td class="label">Plafond Pinjaman</td>
                <td>
                    : Rp {{ number_format($kredit->plafond, 0, ',', '.') }}
                </td>
            </tr>

            <tr>
                <td class="label">Status Pinjaman</td>
                <td>: LUNAS</td>
            </tr>
        </table>

        <p>
            Berdasarkan catatan administrasi koperasi, pinjaman anggota
            tersebut telah diselesaikan atau dinyatakan <b>LUNAS</b>.
            Dengan demikian, koperasi menyatakan bahwa jaminan yang
            sebelumnya disimpan sebagai agunan pinjaman dapat
            dikembalikan kepada pemilik atau pihak yang berhak menerima.
        </p>

        <p><b>Adapun identitas jaminan adalah sebagai berikut:</b></p>

        {{-- IDENTITAS JAMINAN --}}
        <table class="vehicle">
            <tr>
                <th>Jenis Kendaraan</th>
                <td>{{ $surat->jenis_kendaraan ?? '-' }}</td>
            </tr>

            <tr>
                <th>Merek / Tipe</th>
                <td>{{ $surat->merek_tipe ?? '-' }}</td>
            </tr>

            <tr>
                <th>Nomor Polisi</th>
                <td>{{ $surat->nomor_polisi ?? '-' }}</td>
            </tr>

            <tr>
                <th>Nomor Rangka</th>
                <td>{{ $surat->nomor_rangka ?? '-' }}</td>
            </tr>

            <tr>
                <th>Nomor Mesin</th>
                <td>{{ $surat->nomor_mesin ?? '-' }}</td>
            </tr>

            <tr>
                <th>Nomor BPKB</th>
                <td>{{ $surat->nomor_bpkb ?? '-' }}</td>
            </tr>

            <tr>
                <th>Nomor STNK</th>
                <td>{{ $surat->nomor_stnk ?? '-' }}</td>
            </tr>

            <tr>
                <th>Atas Nama Dokumen</th>
                <td>{{ $surat->atas_nama_dokumen ?? '-' }}</td>
            </tr>
        </table>

        <!-- <p><b>Kondisi dan Kelengkapan Jaminan:</b></p>

        <table class="vehicle">
            <tr>
                <th>Kondisi Kendaraan</th>
                <td>{{ $surat->kondisi_kendaraan ?? '-' }}</td>
            </tr>

            <tr>
                <th>Kelengkapan Diserahkan</th>
                <td>{{ $surat->kelengkapan_diserahkan ?? '-' }}</td>
            </tr>

            <tr>
                <th>Catatan</th>
                <td>{{ $surat->catatan ?? '-' }}</td>
            </tr>
        </table> -->

        <p><b>BUKTI PENERIMAAN JAMINAN</b></p>

        <p>
            Jaminan tersebut telah diterima oleh:
        </p>

        <table class="identity">
            <tr>
                <td class="label">Nama Penerima</td>
                <td>: ........................................................</td>
            </tr>

            <tr>
                <td class="label">No. Identitas (KTP)</td>
                <td>: ........................................................</td>
            </tr>

            <!-- <tr>
                <td class="label">Hubungan dengan Peminjam</td>
                <td>: ........................................................</td>
            </tr> -->

            <tr>
                <td class="label">Tanggal Penyerahan</td>
                <td>: ........................................................</td>
            </tr>

            <!-- <tr>
                <td class="label">Waktu Penyerahan</td>
                <td>: ........................................................</td>
            </tr>

            <tr>
                <td class="label">Tempat Penyerahan</td>
                <td>: ........................................................</td>
            </tr> -->
        </table>

        <p>
            Dengan menandatangani surat ini, penerima menyatakan
            telah menerima kembali dokumen jaminan sebagaimana
            tercantum dalam surat ini.
        </p>

    </div>

    {{-- TANDA TANGAN --}}
    <table class="signatures" style="margin-top:0px;">
        <tr>
            <td>
                <br>
                <p>Penerima Jaminan</p>
                <div class="sign-space"></div>
                <div class="sign-name">
                    {{ $surat->nama_penerima ?? '(........................................)' }}
                </div>
            </td>

            <td>
                <p>
                    Denpasar,
                    {{ $surat->tanggal_surat?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
                    <br>
                    Pengurus Koperasi
                </p>

                <div class="sign-space"></div>

                <div class="sign-name">
                    (........................................)
                </div>
                <p>Koperasi Gunung Sari Sedana</p>
            </td>
        </tr>
    </table>

    <div class="footer" style="margin-top:-5px;">
        Koperasi Simpan Pinjam Gunung Sari Sedana
        <br>
        Dokumen administrasi pengembalian jaminan
    </div>

</body>
</html>
