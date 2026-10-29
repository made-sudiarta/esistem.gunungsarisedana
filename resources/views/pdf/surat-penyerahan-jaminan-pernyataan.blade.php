<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 2.5cm 2.5cm 2cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; line-height: 1.2; }
        .center { text-align: center; }
        .title { font-size: 14pt; font-weight: bold; text-decoration: underline; }
        .meta { margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 5px; vertical-align: top; }
        .label { width: 34%; }
        .signature { margin-top: 45px; width: 100%; }
        .signature td { width: 50%; text-align: center; }
        .t-center{text-align:center; margin-bottom:-25px;}
    </style>
</head>
<body>
    @php
        $kredit = $surat->kreditBulanan;
        $member = $kredit?->member;
    @endphp

    <div class="center">
        <div class="title">SURAT PERNYATAAN</div>
        <div><strong>PENYERAHAN JAMINAN SECARA SUKARELA</strong></div>
        <div class="meta">Nomor: {{ $surat->nomor_surat }}</div>
    </div>

    <p>
        Saya yang bertanda tangan di bawah ini:
    </p>

    <table>
        <tr><td class="label">Nama</td><td>: {{ $member?->nama_lengkap ?? '-' }}</td></tr>
        <tr><td class="label">NIA</td><td>: {{ $member?->nia ?? '-' }}</td></tr>
        <tr><td class="label">NIK</td><td>: {{ $member?->nik ?? '-' }}</td></tr>
        <tr><td class="label">Alamat</td><td>: {{ $member?->alamat ?? '-' }}</td></tr>
        <tr><td class="label">Nomor Pokok Kredit</td><td>: {{ $kredit?->no_pokok ?? '-' }}</td></tr>
    </table>

    <p>
        Dengan ini menyatakan bahwa saya menyerahkan kendaraan di bawah ini
        secara sukarela kepada pihak koperasi sebagai jaminan terkait kewajiban
        kredit saya, berdasarkan kesepakatan dan ketentuan yang berlaku.
    </p>

    <table>
        <tr><td class="label">Jenis Kendaraan</td><td>: {{ $surat->jenis_kendaraan ?: '-' }}</td></tr>
        <tr><td class="label">Merek / Tipe</td><td>: {{ $surat->merek_tipe ?: '-' }}</td></tr>
        <tr><td class="label">Nomor Polisi</td><td>: {{ $surat->nomor_polisi ?: '-' }}</td></tr>
        <tr><td class="label">Nomor Rangka</td><td>: {{ $surat->nomor_rangka ?: '-' }}</td></tr>
        <tr><td class="label">Nomor Mesin</td><td>: {{ $surat->nomor_mesin ?: '-' }}</td></tr>
        <tr><td class="label">Nomor BPKB</td><td>: {{ $surat->nomor_bpkb ?: '-' }}</td></tr>
        <!-- <tr><td class="label">Nomor STNK</td><td>: {{ $surat->nomor_stnk ?: '-' }}</td></tr> -->
        <tr><td class="label">Atas Nama Dokumen</td><td>: {{ $surat->atas_nama_dokumen ?: '-' }}</td></tr>
    </table>

    <p>
        Saya menyatakan telah memperoleh kesempatan untuk membaca dan memahami
        isi surat ini. Penyerahan kendaraan ini dicatat sebagai penyerahan
        jaminan dan tidak dengan sendirinya mengalihkan hak kepemilikan kendaraan.
    </p>

    @if ($surat->catatan)
        <p><strong>Catatan:</strong> {{ $surat->catatan }}</p>
    @endif

    <p>
        Demikian surat pernyataan ini dibuat dengan sebenar-benarnya,
        tanpa paksaan dari pihak mana pun.
    </p>

    <p class="t-center">
        {{ $surat->tempat_serah_terima ?: '________________' }},
        {{ $surat->tanggal_surat?->translatedFormat('d F Y') }}
    </p>

    <table class="signature">
        <tr>
            <td>Pihak yang Menyerahkan,</td>
            <td>Pihak yang Menerima,</td>
        </tr>
        <tr><td style="height: 75px;"></td><td></td></tr>
        <tr>
            <td><strong>{{ $member?->nama_lengkap ?? '________________' }}</strong></td>
            <td><strong>{{ $surat->nama_penerima ?: '________________' }}</strong></td>
        </tr>
        <tr>
            <td>Anggota / Debitur</td>
            <td>{{ $surat->jabatan_penerima ?: 'Petugas Koperasi' }}</td>
        </tr>
    </table>
</body>
</html>