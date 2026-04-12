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
            margin: 28px 34px;
            line-height: 1.6;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .mb-4 { margin-bottom: 4px; }
        .mb-6 { margin-bottom: 6px; }
        .mb-8 { margin-bottom: 8px; }
        .mb-10 { margin-bottom: 10px; }
        .mb-12 { margin-bottom: 12px; }
        .mb-16 { margin-bottom: 16px; }
        .mb-20 { margin-bottom: 20px; }
        .mb-24 { margin-bottom: 24px; }
        .mb-28 { margin-bottom: 28px; }
        .mb-32 { margin-bottom: 32px; }

        .mt-24 { margin-top: 24px; }
        .mt-32 { margin-top: 32px; }

        .fw-bold { font-weight: bold; }
        .fs-16 { font-size: 16px; }
        .fs-14 { font-size: 14px; }
        .fs-12 { font-size: 12px; }
        .fs-11 { font-size: 11px; }

        .underline { text-decoration: underline; }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            vertical-align: top;
        }

        .logo-wrap {
            width: 90px;
        }

        .logo {
            width: 72px;
            height: auto;
        }

        .header-line {
            border-top: 2px solid #000;
            border-bottom: 1px solid #000;
            margin-top: 10px;
            margin-bottom: 18px;
            height: 3px;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table td {
            vertical-align: top;
            padding: 0;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 16px;
        }

        .info-table td {
            /* border: 1px solid #000; */
            /* padding: 6px 8px; */
            vertical-align: top;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 32px;
        }

        .signature-table td {
            width: 50%;
            vertical-align: top;
        }

        .ttd-space {
            height: 80px;
        }

        .small-note {
            font-size: 11px;
        }
    </style>
</head>
<body>
    @php
        use Carbon\Carbon;

        Carbon::setLocale('id');

        $kredit = $surat->kreditBulanan;
        $member = $kredit?->member;
        $group = $kredit?->group;
        $petugas = "I Wayan Wartawan";

        $jenisSpLabel = match($surat->jenis_sp) {
            'SP1' => 'SURAT PERINGATAN I',
            'SP2' => 'SURAT PERINGATAN II',
            'SP3' => 'SURAT PERINGATAN III',
            default => 'SURAT TAGIHAN KREDIT',
        };

        $tanggalSurat = $surat->tanggal_surat
            ? Carbon::parse($surat->tanggal_surat)->translatedFormat('d F Y')
            : '-';

        $tanggalJatuhTempo = $surat->tanggal_jatuh_tempo
            ? Carbon::parse($surat->tanggal_jatuh_tempo)->translatedFormat('d F Y')
            : '-';

        $nama = $member->nama_lengkap ?? '-';
        $alamat = $member->alamat ?? '-';
        $groupNama = $group->group ?? '-';

        $jumlahTunggakan = (int) $surat->jumlah_tunggakan_bunga;
        $sisaTunggakanBunga = (float) $surat->sisa_tunggakan_bunga;
        $bungaPerBulan = (float) $surat->bunga_per_bulan;
        $totalTunggakanBunga = (float) $surat->total_tunggakan_bunga;
        $sisaPokokKredit = (float) $surat->sisa_pokok_kredit;

        $logoPath = public_path('images/logo-koperasi.png');
        $logoExists = file_exists($logoPath);
    @endphp

    <table class="kop-table">
        <tr>
            <td class="logo-wrap">
                @if($logoExists)
                    <img src="{{ $logoPath }}" class="logo" alt="Logo">
                @endif
            </td>
            <td class="text-center">
                <div class="fw-bold fs-14" style="margin-bottom:-10px;">KOPERASI SIMPAN PINJAM</div>
                <div class="fw-bold fs-16" style="margin-bottom:-10px;">GUNUNG SARI SEDANA DENPASAR</div>
                <div class="fw-bold fs-12" style="margin-bottom:-10px;">Nomor Badan Hukum : 396 / BH / XXVII.9 / XII / 2015</div>
                <div class="fs-12" style="margin-bottom:-10px;">Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian, Denpasar Barat</div>
            </td>
        </tr>
    </table>

    <div class="header-line" style="margin-bottom:0px;"></div>

    <div class="text-right mb-1">
        Denpasar, {{ $tanggalSurat }}
    </div>

    <table class="detail-table" style="margin-top:-25px;">
        <tr>
            <td style="width: 110px;">Nomor</td>
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
            <td><span class="fw-bold">{{ $jenisSpLabel }}</span></td>
        </tr>
    </table>

    <br>
    <div class="mb-1">Kepada Yth.</div>
    <div class="fw-bold mb-1">{{ $nama }}</div>
    <div class="mb-1">{{ $alamat }}</div>
    <div class="mb-20">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Di Tempat</div>

    <div class="text-center mb-20">
        <div class="fw-bold fs-14 underline">{{ $jenisSpLabel }}</div>
    </div>

    <div class="mb-10">Dengan hormat,</div>

    <div class="mb-10" style="text-align: justify;">
        Berdasarkan catatan administrasi pinjaman pada Koperasi Simpan Pinjam Gunung Sari Sedana,
        sampai dengan tanggal surat ini diterbitkan, Saudara/i masih memiliki kewajiban pembayaran
        atas fasilitas kredit yang belum diselesaikan.
    </div>

    <table class="info-table">
        <tr>
            <td style="width: 38%;">No Pokok</td>
            <td>: {{ $surat->no_pokok }}</td>
        </tr>
        <tr>
            <td>Nama Anggota</td>
            <td>: {{ $nama }}</td>
        </tr>
        <tr>
            <td>Tanggal Jatuh Tempo</td>
            <td>: {{ $tanggalJatuhTempo }}</td>
        </tr>
        <!-- <tr>
            <td>Jumlah Tunggakan Bunga</td>
            <td>: {{ $jumlahTunggakan }} bulan</td>
        </tr>
        <tr>
            <td>Sisa Tunggakan Bunga</td>
            <td>: Rp {{ number_format($sisaTunggakanBunga, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bunga Per Bulan</td>
            <td>: Rp {{ number_format($bungaPerBulan, 0, ',', '.') }}</td>
        </tr> -->
        <tr>
            <td>Total Tunggakan Bunga</td>
            <td>: Rp {{ number_format($totalTunggakanBunga, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Sisa Pokok Kredit</td>
            <td>: Rp {{ number_format($sisaPokokKredit, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if ($surat->jenis_sp === 'SP1')
        <div class="mb-12" style="text-align: justify;">
            Melalui surat ini kami menyampaikan <span class="fw-bold">peringatan pertama</span> agar
            Saudara/i segera melakukan pembayaran atas tunggakan tersebut. Kami berharap kewajiban
            pembayaran dapat segera diselesaikan agar pinjaman tetap berjalan dengan baik sesuai ketentuan koperasi.
        </div>
    @elseif ($surat->jenis_sp === 'SP2')
        <div class="mb-12" style="text-align: justify;">
            Karena sampai saat ini kewajiban pembayaran belum juga diselesaikan, maka melalui surat ini
            kami menyampaikan <span class="fw-bold">peringatan kedua</span>. Kami meminta Saudara/i untuk
            segera melunasi tunggakan dimaksud agar tidak berlanjut pada tindakan penagihan berikutnya.
        </div>
    @elseif ($surat->jenis_sp === 'SP3')
        <div class="mb-12" style="text-align: justify;">
            Karena kewajiban pembayaran masih belum diselesaikan, maka melalui surat ini kami menyampaikan
            <span class="fw-bold">peringatan ketiga / terakhir</span>. Apabila Saudara/i tetap belum
            menyelesaikan kewajiban pembayaran, maka koperasi akan menindaklanjuti sesuai prosedur dan
            ketentuan yang berlaku.
        </div>
    @endif

    @if ($surat->keterangan)
        <div class="mb-1" style="text-align: justify;">
            <span class="fw-bold">Keterangan tambahan:</span><br>
            {{ $surat->keterangan }}
        </div>
    @endif

    <div class="mb-1" style="text-align: justify;">
        Demikian surat ini kami sampaikan untuk menjadi perhatian dan agar segera ditindaklanjuti.
        Atas kerja sama Saudara/i kami ucapkan terima kasih.
    </div>

    <table class="signature-table">
        <tr>
            <td></td>
            <td class="text-center">
                <div class="mb-1">Hormat kami,</div>
                <div class="fw-bold">Kepala Bagian Kredit</div>
                <div class="ttd-space"></div>
                <div class="fw-bold underline">({{ $petugas }})</div>
                <!-- <div class="small-note">Petugas / Pengurus</div> -->
            </td>
        </tr>
    </table>
</body>
</html>