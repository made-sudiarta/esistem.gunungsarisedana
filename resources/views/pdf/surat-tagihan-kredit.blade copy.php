<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Tagihan Kredit</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            margin: 32px;
            line-height: 1.5;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-8 { margin-bottom: 8px; }
        .mb-12 { margin-bottom: 12px; }
        .mb-16 { margin-bottom: 16px; }
        .mb-20 { margin-bottom: 20px; }
        .mb-24 { margin-bottom: 24px; }
        .mb-32 { margin-bottom: 32px; }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-decoration: underline;
        }

        .subtitle {
            font-size: 13px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-detail td {
            vertical-align: top;
            padding: 2px 0;
        }

        .signature {
            width: 100%;
            margin-top: 40px;
        }

        .signature td {
            width: 50%;
            vertical-align: top;
        }

        .ttd-space {
            height: 80px;
        }
        .text-justify{
            text-align: justify;
        }
    </style>
</head>
<body>
    @php
        $kredit = $surat->kreditBulanan;
        $member = $kredit?->member;
        $group = $kredit?->group;

        $jenisSpLabel = match($surat->jenis_sp) {
            'SP1' => 'SURAT PERINGATAN 1',
            'SP2' => 'SURAT PERINGATAN 2',
            'SP3' => 'SURAT PERINGATAN 3',
            default => 'SURAT TAGIHAN KREDIT',
        };

        $tanggalSurat = $surat->tanggal_surat?->translatedFormat('d F Y');
        $tanggalJatuhTempo = $surat->tanggal_jatuh_tempo?->translatedFormat('d F Y');

        $nama = $member->nama_lengkap ?? '-';
        $alamat = $member->alamat ?? '-';
        $groupNama = $group->group ?? '-';

        $totalTunggakanBunga = (float) $surat->total_tunggakan_bunga;
        $sisaTunggakanBunga = (float) $surat->sisa_tunggakan_bunga;
        $sisaPokokKredit = (float) $surat->sisa_pokok_kredit;
        $jumlahTunggakan = (int) $surat->jumlah_tunggakan_bunga;
    @endphp

    <div class="text-right mb-20">
        <div>Denpasar, {{ $tanggalSurat }}</div>
    </div>

    <table class="table-detail mb-20">
        <tr>
            <td style="width: 120px;">Nomor</td>
            <td style="width: 10px;">:</td>
            <td>{{ $surat->nomor_surat }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>:</td>
            <td><strong>{{ $jenisSpLabel }}</strong></td>
        </tr>
    </table>

    <div class="mb-12">
        Kepada Yth.
    </div>
    <div class="mb-8"><strong>{{ $nama }}</strong></div>
    <div class="mb-8">{{ $alamat }}</div>
    <div class="mb-20">Di Tempat</div>

    <div class="text-center mb-20">
        <div class="title">{{ $jenisSpLabel }}</div>
    </div>

    <div class="mb-12">Dengan hormat,</div>

    <div class="mb-12 text-justify">
        Berdasarkan data pinjaman pada Koperasi Simpan Pinjam Gunung Sari Sedana, bersama ini kami memberitahukan bahwa pinjaman Saudara/i telah mengalami tunggakan pembayaran dengan rincian sebagai berikut:
    </div>

    <table class="table-detail mb-20">
        <tr>
            <td style="width: 180px;">No Pokok</td>
            <td style="width: 10px;">:</td>
            <td>{{ $surat->no_pokok }}</td>
        </tr>
        <tr>
            <td>Nama Anggota</td>
            <td>:</td>
            <td>{{ $nama }}</td>
        </tr>
        <tr>
            <td>Jumlah Tunggakan Bunga</td>
            <td>:</td>
            <td>{{ $jumlahTunggakan }} bulan</td>
        </tr>
        <tr>
            <td>Sisa Tunggakan Bunga</td>
            <td>:</td>
            <td>Rp {{ number_format($sisaTunggakanBunga, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Total Tunggakan Bunga</td>
            <td>:</td>
            <td>Rp {{ number_format($totalTunggakanBunga, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Sisa Pokok Kredit</td>
            <td>:</td>
            <td>Rp {{ number_format($sisaPokokKredit, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Tanggal Jatuh Tempo</td>
            <td>:</td>
            <td>{{ $tanggalJatuhTempo }}</td>
        </tr>
    </table>

    @if ($surat->jenis_sp === 'SP1')
        <div class="mb-12 text-justify">
            Sehubungan dengan hal tersebut, kami mohon agar Saudara/i segera melakukan pembayaran atas tunggakan dimaksud.
        </div>
    @elseif ($surat->jenis_sp === 'SP2')
        <div class="mb-12 text-justify">
            Karena hingga saat ini kewajiban pembayaran belum juga diselesaikan, maka kami kembali mengingatkan Saudara/i untuk segera melunasi tunggakan tersebut.
        </div>
    @elseif ($surat->jenis_sp === 'SP3')
        <div class="mb-12 text-justify">
            Ini merupakan peringatan terakhir. Apabila Saudara/i belum menyelesaikan kewajiban pembayaran, maka kami akan menindaklanjuti sesuai ketentuan yang berlaku di koperasi.
        </div>
    @endif

    @if ($surat->keterangan)
        <div class="mb-12 text-justify">
            Keterangan tambahan: {{ $surat->keterangan }}
        </div>
    @endif

    <div class="mb-12 text-justify">
        Demikian surat ini kami sampaikan. Atas perhatian dan kerja samanya kami ucapkan terima kasih.
    </div>

    <table class="signature">
        <tr>
            <td></td>
            <td class="text-center">
                Hormat kami,
                <br>
                Kepala Bagian Kredit
                <div class="ttd-space"></div>
                <strong>I Wayan Wartawan</strong>
            </td>
        </tr>
    </table>
</body>
</html>