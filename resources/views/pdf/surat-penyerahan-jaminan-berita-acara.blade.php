<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 2.5cm 2.5cm 2cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; line-height: 1.2; }
        .center { text-align: center; }
        .title { font-size: 14pt; font-weight: bold; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #333; padding: 6px; vertical-align: top; }
        .plain td { border: none; padding: 3px 5px; }
        .label { width: 34%; }
        .signature { margin-top: 0px; }
        .signature td { width: 50%; text-align: center; border: none;}
    </style>
</head>
<body>
    @php
        $kredit = $surat->kreditBulanan;
        $member = $kredit?->member;
    @endphp

    <div class="center">
        <div class="title">BERITA ACARA</div>
        <div><strong>SERAH TERIMA KENDARAAN</strong></div>
        <div>Nomor: {{ $surat->nomor_surat }}</div>
    </div>

    <p>
        Pada hari {{ $surat->tanggal_serah_terima?->translatedFormat('l') ?? '____________' }},
        tanggal {{ $surat->tanggal_serah_terima?->translatedFormat('d F Y') ?? '____________' }}
        @if ($surat->waktu_serah_terima)
            pukul {{ substr($surat->waktu_serah_terima, 0, 5) }} WITA
        @endif,
        bertempat di {{ $surat->tempat_serah_terima ?: '________________' }},
        telah dilakukan serah terima kendaraan antara:
    </p>

    <p><strong>PIHAK PERTAMA (Yang Menyerahkan)</strong></p>
    <table class="plain">
        <tr><td class="label">Nama</td><td>: {{ $member?->nama_lengkap ?? '-' }}</td></tr>
        <tr><td class="label">NIA</td><td>: {{ $member?->nia ?? '-' }}</td></tr>
        <tr><td class="label">Alamat</td><td>: {{ $member?->alamat ?? '-' }}</td></tr>
        <tr><td class="label">Nomor Pokok Kredit</td><td>: {{ $kredit?->no_pokok ?? '-' }}</td></tr>
    </table>

    <p><strong>PIHAK KEDUA (Penerima atas nama Koperasi)</strong></p>
    <table class="plain">
        <tr><td class="label">Nama</td><td>: {{ $surat->nama_penerima ?: '________________' }}</td></tr>
        <tr><td class="label">Jabatan</td><td>: {{ $surat->jabatan_penerima ?: '________________' }}</td></tr>
    </table>

    <p>Kedua pihak menerangkan bahwa kendaraan berikut telah diserahterimakan:</p>

    <table>
        <tr><th style="width: 34%;">Keterangan</th><th>Data Kendaraan</th></tr>
        <tr><td>Jenis Kendaraan</td><td>{{ $surat->jenis_kendaraan ?: '-' }}</td></tr>
        <tr><td>Merek / Tipe</td><td>{{ $surat->merek_tipe ?: '-' }}</td></tr>
        <tr><td>Nomor Polisi</td><td>{{ $surat->nomor_polisi ?: '-' }}</td></tr>
        <tr><td>Nomor Rangka</td><td>{{ $surat->nomor_rangka ?: '-' }}</td></tr>
        <tr><td>Nomor Mesin</td><td>{{ $surat->nomor_mesin ?: '-' }}</td></tr>
        <tr><td>Nomor BPKB</td><td>{{ $surat->nomor_bpkb ?: '-' }}</td></tr>
        <!-- <tr><td>Nomor STNK</td><td>{{ $surat->nomor_stnk ?: '-' }}</td></tr> -->
        <tr><td>Atas Nama Dokumen</td><td>{{ $surat->atas_nama_dokumen ?: '-' }}</td></tr>
        <tr><td>Kondisi Kendaraan</td><td>{{ $surat->kondisi_kendaraan ?: '-' }}</td></tr>
        <tr><td>Kelengkapan Diserahkan</td><td>{{ $surat->kelengkapan_diserahkan ?: '-' }}</td></tr>
    </table>

    @if ($surat->catatan)
        <p><strong>Catatan:</strong> {{ $surat->catatan }}</p>
    @endif

    <p>
        Berita acara ini dibuat untuk mencatat kondisi dan kelengkapan kendaraan
        pada saat serah terima. Masing-masing pihak menyatakan telah memeriksa
        data yang tercantum dan menerima salinan berita acara ini.
    </p>

    <table class="signature">
        <tr>
            <td>Pihak Pertama,<br>Yang Menyerahkan</td>
            <td>Pihak Kedua,<br>Yang Menerima</td>
        </tr>
        <tr><td style="height: 40px;"></td><td></td></tr>
        <tr>
            <td><strong>{{ $member?->nama_lengkap ?? '________________' }}</strong></td>
            <td><strong>{{ $surat->nama_penerima ?: '________________' }}</strong></td>
        </tr>
    </table>
</body>
</html>