<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akad Kredit Harian</title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            font-family: "Calibri";
            font-size: 11pt;
            line-height: 1.15;
            color: #111;
            margin: 0;
            padding: 0;
        }

        .akad-page {
            width: 100%;
            min-height: 100%;
            page-break-after: always;
        }

        .akad-page:last-child {
            page-break-after: auto;
        }

        .header { text-align: center; margin-bottom: 14px; }
        .koperasi-title { font-size: 15pt; font-weight: bold; text-transform: uppercase; }
        .koperasi-subtitle { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-top: 2px; }
        .koperasi-meta { font-size: 10.5pt; margin-top: 3px; }
        .divider { border-top: 1.5px solid #000; border-bottom: 0.5px solid #000; height: 3px; margin: 8px 0 16px; }
        .doc-title { text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; margin-bottom: 4px; }
        .doc-number { text-align: center; margin-bottom: 18px; font-weight: bold; }
        .paragraph { text-align: justify; margin-bottom: 10px; }
        .identity-table, .info-table, .signature-table { width: 100%; border-collapse: collapse; }
        .identity-table td, .info-table td, .signature-table td { vertical-align: top; padding: 2px 0; }
        .identity-table td.label, .info-table td.label { width: 170px; }
        .identity-table td.sep, .info-table td.sep { width: 14px; text-align: center; }
        .boxed { border: 1px solid #000; padding: 10px 12px; margin: 10px 0 14px; }
        .pasal { margin-top: 10px; }
        .pasal-title { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 6px; }
        .list-alpha { margin: 6px 0 6px 18px; padding: 0; }
        .list-alpha li { margin-bottom: 4px; text-align: justify; }
        .signature-wrap { margin-top: 28px; }
        .signature-table td { width: 33.33%; text-align: center; padding-top: 8px; }
        .signature-space { height: 70px; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .mt-12 { margin-top: 12px; }
        .mt-20 { margin-top: 20px; }
    </style>
</head>
<body>
@php
    $member = $record->member;
    $kolektor = $record->group?->employees?->members;

    $tanggalPengajuan = \Carbon\Carbon::parse($record->tanggal_pengajuan);
    $jatuhTempo = (clone $tanggalPengajuan)->addDays($record->jangka_waktu ?? 0);

    $bulanRomawi = [
        '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
        '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
        '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII',
    ];

    $nomorAkad = str_pad($record->no_pokok ?? 0, 4, '0', STR_PAD_LEFT)
        . '/KGSH/'
        . ($bulanRomawi[$tanggalPengajuan->format('m')] ?? '')
        . '/'
        . $tanggalPengajuan->format('Y');

    $namaNasabah = $member?->nama_lengkap ?? $record->nama_lengkap ?? '-';
    $tempatLahir = $member?->tempat_lahir ?? '-';
    $tanggalLahir = !empty($member?->tanggal_lahir)
        ? \Carbon\Carbon::parse($member->tanggal_lahir)->format('d-m-Y')
        : '-';

    $tanggalLahirKolektor = !empty($kolektor?->tanggal_lahir)
        ? \Carbon\Carbon::parse($kolektor->tanggal_lahir)->format('d-m-Y')
        : '-';

    $pekerjaan = $member?->pekerjaan ?? $record->pekerjaan ?? '-';
    $nik = $member?->nik ?? '-';
    $alamat = $member?->alamat ?? $record->alamat ?? '-';

    $jumlahPinjaman = (float) ($record->plafond ?? 0);
    $jangkaWaktu = (int) ($record->jangka_waktu ?? 0);
    $bungaPersen = (float) ($record->bunga_persen ?? 0);
    $adminPersen = (float) ($record->admin_persen ?? 0);
    $cicilanHarian = (float) ($record->cicilan_harian ?? 0);

    $penjaminNama = $record->penjamin_nama ?? '-';
    $penjaminTempatLahir = $record->penjamin_tempat_lahir ?? '-';
    $penjaminTanggalLahir = !empty($record->penjamin_tanggal_lahir)
        ? \Carbon\Carbon::parse($record->penjamin_tanggal_lahir)->format('d-m-Y')
        : '-';
    $penjaminPekerjaan = $record->penjamin_pekerjaan ?? '-';
    $penjaminNik = $record->penjamin_nik ?? '-';
    $penjaminAlamat = $record->penjamin_alamat ?? '-';

    $jaminan = $record->jaminan ?? '-';
    $tujuan = $record->tujuan_pinjaman ?? 'Modal Usaha';
    $tanggalCetak = $tanggalPengajuan->translatedFormat('d F Y');

    $namaKasir = auth()->user()?->name ?? '-';

    if (! function_exists('terbilangSederhana')) {
        function terbilangSederhana($angka) {
            $angka = abs((int) $angka);
            $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
            if ($angka < 12) return $baca[$angka];
            if ($angka < 20) return terbilangSederhana($angka - 10) . " Belas";
            if ($angka < 100) return terbilangSederhana(intval($angka / 10)) . " Puluh " . terbilangSederhana($angka % 10);
            if ($angka < 200) return "Seratus " . terbilangSederhana($angka - 100);
            if ($angka < 1000) return terbilangSederhana(intval($angka / 100)) . " Ratus " . terbilangSederhana($angka % 100);
            if ($angka < 2000) return "Seribu " . terbilangSederhana($angka - 1000);
            if ($angka < 1000000) return terbilangSederhana(intval($angka / 1000)) . " Ribu " . terbilangSederhana($angka % 1000);
            if ($angka < 1000000000) return terbilangSederhana(intval($angka / 1000000)) . " Juta " . terbilangSederhana($angka % 1000000);
            return number_format($angka, 0, ',', '.');
        }
    }

    $terbilangPinjaman = trim(terbilangSederhana($jumlahPinjaman)) . ' Rupiah';

    $pokokHarian = $jangkaWaktu > 0 ? $jumlahPinjaman / $jangkaWaktu : 0;
    $bungaAdmHarian = $cicilanHarian - $pokokHarian;
@endphp

    <div class="akad-page">
        <div class="header">
            <div class="koperasi-title">Koperasi Simpan Pinjam</div>
            <div class="koperasi-subtitle">Gunung Sari Sedana Denpasar</div>
            <div class="koperasi-meta">Nomor Badan Hukum : 396 / BH / XXVII.9 / XII / 2015</div>
            <div class="koperasi-meta">Jalan Gunung Guntur Gg. XIX No. 9 Padangsambian, Denpasar Barat</div>
        </div>

        <div class="divider"></div>

        <div class="doc-title">Bukti Kredit Harian Keluar</div>
        <table class="identity-table">
            <tr><td class="label">Nama</td><td class="sep">:</td><td>{{ $namaNasabah }}</td></tr>
            <tr><td class="label">Alamat</td><td class="sep">:</td><td>{{ $alamat }}</td></tr>
            <tr><td class="label">No. Pokok Kredit</td><td class="sep">:</td><td>{{ $nomorAkad }}</td></tr>
            <tr><td class="label">Besar Pinjaman</td><td class="sep">:</td><td>Rp. {{ number_format($jumlahPinjaman, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Provisi &amp; Adm</td><td class="sep">:</td><td>Rp. {{ number_format($record->prov_adm, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Materai</td><td class="sep">:</td><td>Rp. {{ number_format($record->materai, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya OP</td><td class="sep">:</td><td>Rp. {{ number_format($record->op, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya KYD</td><td class="sep">:</td><td>Rp. {{ number_format($record->kyd, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Lain-lain</td><td class="sep">:</td><td>Rp. {{ number_format($record->biaya_lain, 0, ',', '.') }} &nbsp; {{ $record->keterangan_biaya_lain == '-' ? '' : $record->keterangan_biaya_lain }}</td></tr>
            <tr><td class="label">Jumlah Biaya-biaya</td><td class="sep">:</td><td>Rp. {{ number_format($record->prov_adm+$record->materai+$record->op+$record->kyd+$record->biaya_lain, 0, ',', '.') }}</td></tr>
            <tr><td class="label"><strong>Jumlah Diterima Bersih</strong></td><td class="sep"><strong>:</strong></td><td><strong>Rp. {{ number_format($jumlahPinjaman-($record->prov_adm+$record->materai+$record->op+$record->kyd+$record->biaya_lain), 0, ',', '.') }}</strong></td></tr>
        </table>

        <div class="signature-wrap">
            <table class="signature-table mt-20">
                <tr>
                    <td><br>Kasir</td>
                    <td>Denpasar, {{ $tanggalCetak }} <br>Penerima Kredit</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td><strong>( {{ $namaKasir }} )</strong></td>
                    <td><strong>( {{ $namaNasabah }} )</strong></td>
                </tr>
            </table>
        </div>

        <br>
        <!-- <div class="hr"></div> -->
         <hr style="border:0; border-top:1px dashed #000;">
        <br>

        <div class="header">
            <div class="koperasi-title" style="margin:0; font-size:10pt">Koperasi Simpan Pinjam</div>
            <div class="koperasi-subtitle" style="margin:0;">Gunung Sari Sedana Denpasar</div>
            <div class="koperasi-meta" style="margin:0; font-size:10pt">Nomor Badan Hukum : 396 / BH / XXVII.9 / XII / 2015</div>
        </div>

        <div class="divider"></div>

        <div class="doc-title">Bukti Kredit Harian Keluar</div>
        <table class="identity-table">
            <tr><td class="label">Nama</td><td class="sep">:</td><td>{{ $namaNasabah }}</td></tr>
            <tr><td class="label">Alamat</td><td class="sep">:</td><td>{{ $alamat }}</td></tr>
            <tr><td class="label">No. Pokok Kredit</td><td class="sep">:</td><td>{{ $nomorAkad }}</td></tr>
            <tr><td class="label">Besar Pinjaman</td><td class="sep">:</td><td>Rp. {{ number_format($jumlahPinjaman, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Provisi &amp; Adm</td><td class="sep">:</td><td>Rp. {{ number_format($record->prov_adm, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Materai</td><td class="sep">:</td><td>Rp. {{ number_format($record->materai, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya OP</td><td class="sep">:</td><td>Rp. {{ number_format($record->op, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya KYD</td><td class="sep">:</td><td>Rp. {{ number_format($record->kyd, 0, ',', '.') }}</td></tr>
            <tr style="font-size:10pt"><td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Lain-lain</td><td class="sep">:</td><td>Rp. {{ number_format($record->biaya_lain, 0, ',', '.') }} &nbsp; {{ $record->keterangan_biaya_lain == '-' ? '' : $record->keterangan_biaya_lain }}</td></tr>
            <tr><td class="label">Jumlah Biaya-biaya</td><td class="sep">:</td><td>Rp. {{ number_format($record->prov_adm+$record->materai+$record->op+$record->kyd+$record->biaya_lain, 0, ',', '.') }}</td></tr>
            <tr><td class="label"><strong>Jumlah Diterima Bersih</strong></td><td class="sep"><strong>:</strong></td><td><strong>Rp. {{ number_format($jumlahPinjaman-($record->prov_adm+$record->materai+$record->op+$record->kyd+$record->biaya_lain), 0, ',', '.') }}</strong></td></tr>
        </table>
    </div>

    <div class="akad-page">
        <div class="header">
            <div class="koperasi-title">Koperasi Simpan Pinjam</div>
            <div class="koperasi-subtitle">Gunung Sari Sedana Denpasar</div>
            <div class="koperasi-meta">Nomor Badan Hukum : 396 / BH / XXVII.9 / XII / 2015</div>
            <div class="koperasi-meta">Jalan Gunung Guntur Gg. XIX No. 9 Padangsambian, Denpasar Barat</div>
        </div>

        <div class="divider"></div>

        <div class="text-left">Perihal : <b><u>Permohonan Kredit Harian</u></b></div>
        <div class="text-left">Nomor : <b>{{ $nomorAkad }}</b></div><br>

        <div class="paragraph">Dengan hormat,</div>

        <table class="identity-table">
            <tr><td class="label">Nama Debitur</td><td class="sep">:</td><td>{{ $namaNasabah }}</td></tr>
            <tr><td class="label">Tempat / Tanggal Lahir</td><td class="sep">:</td><td>{{ $tempatLahir }}, {{ $tanggalLahir }}</td></tr>
            <tr><td class="label">Pekerjaan</td><td class="sep">:</td><td>{{ $pekerjaan }}</td></tr>
            <tr><td class="label">No. KTP / SIM / Domisili</td><td class="sep">:</td><td>{{ $nik }}</td></tr>
            <tr><td class="label">Alamat</td><td class="sep">:</td><td>{{ $alamat }}</td></tr>
        </table>

        <div class="paragraph mt-12">
            Dengan ini mengajukan permohonan kredit.
        </div>

        <div class="pasal">
            <table class="info-table">
                <tr><td class="label">a. Sebesar</td><td class="sep">:</td><td><strong>Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}</strong> ({{ $terbilangPinjaman }})</td></tr>
                <tr><td class="label">b. Jangka Waktu</td><td class="sep">:</td><td>{{ $jangkaWaktu }} Hari</td></tr>
                <tr><td class="label">c. Tujuan Kredit</td><td class="sep">:</td><td>{{ $tujuan }}</td></tr>
                <tr><td class="label">d. Jaminan</td><td class="sep">:</td><td>{{ $jaminan }}</td></tr>
            </table>
        </div>

        <div class="paragraph mt-12">
            Sebagai bahan pertimbangan kami lampirkan sebagai berikut:
            <table class="info-table">
                <tr><td class="label">1. Copy identitas yang masih berlaku (Suami/Istri)</td></tr>
                <tr><td class="label">2. Copy jaminan BPKB/Tabungan/harta benda yang bisa diuangkan</td></tr>
                <tr><td class="label">3. Copy Kartu Keluarga yang masih berlaku</td></tr>
                <tr><td class="label">4. Bukti simpanan sukarela/berjangka pada Koperasi Gunung Sari Sedana Denpasar</td></tr>
                <tr><td class="label">5. Daftar gaji dari instansi/perusahaan</td></tr>
            </table>
            <br>
            Demikian atas bantuan serta perkenannya kami ucapkan terima kasih.
        </div>

        <div class="signature-wrap">
            <table class="signature-table mt-20">
                <tr>
                    <td><br><br>Manager</td>
                    <td><br><br>Ketua</td>
                    <td>Denpasar, {{ $tanggalCetak }} <br>Hormat kami,<br>Pemohon</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td><strong>( I Wayan Landuh )</strong></td>
                    <td><strong>( I Made Sudiarta, S.Kom )</strong></td>
                    <td><strong>( {{ $namaNasabah }} )</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="akad-page">
        <div class="doc-title">Perjanjian Kredit</div>
        <div class="doc-number" style="margin-bottom:0;">Nomor : {{ $nomorAkad }}</div>
        <div class="divider"></div>

        <div class="paragraph">Yang bertanda tangan di bawah ini :</div>

        <div class="paragraph">
            <table class="identity-table">
                <tr><td class="label">Nama</td><td class="sep">:</td><td>I Wayan Landuh</td></tr>
                <tr><td class="label">Jabatan</td><td class="sep">:</td><td>Manager</td></tr>
                <tr><td class="label">No. KTP</td><td class="sep">:</td><td>5171031301050001</td></tr>
                <tr><td class="label">Alamat</td><td class="sep">:</td><td>Jl. Gunung Guntur Gg. XIX No. 9 Padangsambian, Denpasar Barat</td></tr>
            </table>
            <div class="paragraph mt-12">
                yang selanjutnya disebut sebagai <strong>Pihak Pertama / Pemberi Kredit</strong>.
            </div>
        </div>

        <table class="identity-table">
            <tr><td class="label">Nama Debitur</td><td class="sep">:</td><td>{{ $namaNasabah }}</td></tr>
            <tr><td class="label">Tempat / Tanggal Lahir</td><td class="sep">:</td><td>{{ $tempatLahir }}, {{ $tanggalLahir }}</td></tr>
            <tr><td class="label">Pekerjaan</td><td class="sep">:</td><td>{{ $pekerjaan }}</td></tr>
            <tr><td class="label">No. KTP / SIM / Domisili</td><td class="sep">:</td><td>{{ $nik }}</td></tr>
            <tr><td class="label">Alamat</td><td class="sep">:</td><td>{{ $alamat }}</td></tr>
        </table>

        <div class="paragraph mt-12">
            untuk selanjutnya disebut sebagai <strong>Pihak Kedua / Penerima Kredit</strong>.
        </div>

        <!-- <div class="divider"></div> -->
         <hr style="border:0; margin-top:40px; margin-bottom: 40px; border-top:1px solid #000;">

        <div class="pasal">
            <div class="pasal-title">Pasal 1</div>
            <div class="paragraph mt-12">
                Maksimum kredit yang diberikan Koperasi Simpan Pinjam Gunung Sari Sedana Denpasar kepada penerima kredit adalah sebesar:
                <div class="doc-number" style="margin-top:10px; margin-bottom:10px;">Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }} ({{ $terbilangPinjaman }})</div>
                Dalam jangka waktu {{ $jangkaWaktu }} hari, terhitung dari tanggal {{ $tanggalPengajuan->format('d F Y') }} sampai berakhir pada tanggal {{ $jatuhTempo->format('d F Y') }}.
                Penerima kredit wajib membayar angsuran kredit kepada Koperasi Simpan Pinjam Gunung Sari Sedana dengan cara dan ketentuan yang ada, mengangsur selama {{ $jangkaWaktu }} hari dengan perincian sebagai berikut:
                <table class="info-table" style="width:50%;">
                    <tr><td class="label">Pokok</td><td class="sep">:</td><td style="text-align:right;">Rp {{ number_format($pokokHarian, 0, ',', '.') }}</td></tr>
                    <tr><td class="label">Bunga/Adm</td><td class="sep" style="border-bottom:1px solid;">:</td><td style="border-bottom:1px solid; text-align:right;">Rp {{ number_format($bungaAdmHarian, 0, ',', '.') }}</td></tr>
                    <tr><td class="label">Total</td><td class="sep">:</td><td style="text-align:right;">Rp {{ number_format($cicilanHarian, 0, ',', '.') }}</td></tr>
                </table>
                Apabila perjanjian kredit ini telah berakhir ternyata kredit belum dilunasi, maka sebelum diperpanjang dan/atau diperbaharui, perjanjian kredit ini masih tetap berlaku.
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 2</div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px">(1)</td>
                    <td>Penerima Kredit wajib membayar bunga kredit sebesar {{ $bungaPersen/100 }}% per hari.</td>
                </tr>
                <tr>
                    <td class="sep">(2)</td>
                    <td>Provisi dan biaya administrasi harus dibayar oleh penerima kredit kepada Koperasi sebesar 2% dari maksimum kredit dan tidak dapat ditarik kembali, sekalipun kredit ini tidak jadi dipergunakan.</td>
                </tr>
            </table>
        </div>
        <div class="pasal">
            <div class="pasal-title">Pasal 3</div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px">(1)</td>
                    <td>
                        Segala harta kekayaan penerima kredit, baik yang bergerak maupun yang tidak bergerak, baik yang sudah ada maupun yang akan ada di kemudian hari, menjadi jaminan pelunasan jumlah kredit yang timbul karena perjanjian kredit ini.
                        Jika dianggap perlu, Koperasi berhak meminta tambahan jaminan baru atau pengganti jaminan yang lama.
                    </td>
                </tr>
                <tr>
                    <td class="sep">(2)</td>
                    <td>
                        Guna lebih menjamin pembayaran kredit tersebut oleh penerima kredit, diserahkan kepada Koperasi barang-barang jaminan sebagai berikut:
                        <table class="identity-table">
                            <tr><td class="sep">a.</td><td class="label">Sebuah</td><td class="sep">:</td><td>{{ $jaminan }}</td></tr>
                            <tr><td class="sep">b.</td><td class="label">Sebuah</td><td class="sep">:</td><td>-</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div class="akad-page page-break">
        
        <div class="pasal">
            <div class="pasal-title">Pasal 4</div>
            <div class="paragraph mt-12">
                Koperasi berhak untuk menagih kredit ini dengan seketika dan sekaligus, termasuk bunga, provisi dan ongkos lainnya apabila penerima kredit:
                <table class="identity-table">
                    <tr><td class="sep" style="vertical-align:top;">a.</td><td class="label">Melalaikan kewajibannya membayar angsuran pokok, bunga, provisi, denda, dan ongkos-ongkos lainnya.</td></tr>
                    <tr><td class="sep" style="vertical-align:top;">b.</td><td class="label">Meninggal dunia.</td></tr>
                    <tr><td class="sep" style="vertical-align:top;">c.</td><td class="label">Dinyatakan pailit atau karena apa pun juga tidak berhak lagi mengurus dan/atau menguasai harta kekayaannya.</td></tr>
                    <tr><td class="sep" style="vertical-align:top;">d.</td><td class="label">Harta kekayaannya sebagian atau seluruhnya disita oleh orang atau badan hukum lainnya.</td></tr>
                    <tr><td class="sep" style="vertical-align:top;">e.</td><td class="label">Tidak mematuhi peraturan-peraturan dan ketentuan-ketentuan yang telah ditetapkan dalam perjanjian kredit ini.</td></tr>
                </table>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 5</div>
            <div class="paragraph mt-12">
                Apabila penerima kredit tidak membayar utangnya 3 kali berturut-turut, maka Koperasi menerima kuasa penuh yang tidak dapat dibatalkan oleh apa pun/siapa pun juga untuk menjual harta kekayaannya yang menjadi jaminan sebagaimana tersebut dalam Pasal 3 di atas, baik di hadapan umum maupun di bawah tangan, dengan harga dan syarat-syarat yang disetujui oleh Koperasi. Apabila masih ada kelebihan, diserahkan kepada Penerima Kredit, dan sebaliknya bila hasil penjualan tersebut ternyata kurang, maka Penerima Kredit wajib menambah kekurangannya sampai dianggap cukup oleh Koperasi Gunung Sari Sedana Denpasar.
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal Penutup</div>
            <div class="paragraph mt-12">
                Segala akibat perjanjian kredit ini kedua belah pihak memilih tempat kediaman hukum yang sah dan tidak berubah di kantor Pengadilan Negeri Denpasar.
            </div>
        </div>

        <div class="signature-wrap" style="margin-bottom:-50px; padding-bottom:0px;">
            <table class="signature-table mt-20">
                <tr>
                    <td><br>Menyetujui,<br>Manager</td>
                    <td>Denpasar, {{ $tanggalCetak }}<br>Menyetujui,<br>Kepala Bagian Kredit</td>
                    <td><br>Penerima Kredit,<br>Peminjam</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td><strong>( I Wayan Landuh )</strong></td>
                    <td><strong>( I Wayan Wartawan )</strong></td>
                    <td><strong>( {{ $namaNasabah }} )</strong></td>
                </tr>
                <tr>
                    <td>Mengetahui,<br>Ketua Pengawas</td>
                    <td>Mengetahui,<br>Ketua</td>
                    <td>Kolektor Kredit/Tabungan,<br>Penanggungjawab</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td><strong>( I Nyoman Supantra )</strong></td>
                    <td><strong>( I Made Sudiarta, S.Kom. )</strong></td>
                    <td><strong>( {{ $kolektor?->nama_lengkap ?? '-' }} )</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="akad-page page-break">
        <div class="doc-title">Surat Pengakuan Hutang</div>
        <div class="divider"></div>

        <div class="paragraph mt-12">
            Yang bertanda tangan di bawah ini:
            <table class="identity-table">
                <tr><td class="sep">I.</td><td class="label">Nama</td><td class="sep">:</td><td>{{ $namaNasabah }}</td></tr>
                <tr><td class="sep"></td><td class="label">Tempat/Tanggal lahir</td><td class="sep">:</td><td>{{ $tempatLahir }}, {{ $tanggalLahir }}</td></tr>
                <tr><td class="sep"></td><td class="label">Pekerjaan</td><td class="sep">:</td><td>{{ $pekerjaan }}</td></tr>
                <tr><td class="sep"></td><td class="label">No. KTP</td><td class="sep">:</td><td>{{ $nik }}</td></tr>
                <tr><td class="sep"></td><td class="label">Alamat</td><td class="sep">:</td><td>{{ $alamat }}</td></tr>

                <tr><td class="sep">II.</td><td class="label">Nama</td><td class="sep">:</td><td>{{ $kolektor?->nama_lengkap ?? '-' }}</td></tr>
                <tr><td class="sep"></td><td class="label">Tempat/Tanggal lahir</td><td class="sep">:</td><td>{{ $kolektor?->tempat_lahir ?? '-' }}, {{ $tanggalLahirKolektor }}</td></tr>
                <tr><td class="sep"></td><td class="label">Pekerjaan</td><td class="sep">:</td><td>{{ $kolektor?->pekerjaan ?? '-' }}</td></tr>
                <tr><td class="sep"></td><td class="label">No. KTP</td><td class="sep">:</td><td>{{ $kolektor?->nik ?? '-' }}</td></tr>
                <tr><td class="sep"></td><td class="label">Alamat</td><td class="sep">:</td><td>{{ $kolektor?->alamat ?? '-' }}</td></tr>
            </table>

            <br>Dengan ini para pihak menyatakan sepakat untuk mengikatkan diri secara bersama-sama dalam menanggung kewajiban pembayaran utang sebagaimana disebutkan dalam surat pengakuan utang ini, baik secara bersama-sama maupun masing-masing secara sendiri-sendiri (tanggung renteng / hoofdelijke aansprakelijkheid).
            <br><br>Adapun jumlah utang yang diakui adalah sebesar <strong>Rp. {{ number_format($jumlahPinjaman, 0, ',', '.') }} ({{ $terbilangPinjaman }})</strong>.
            <br><br>Sejak saat surat pengakuan utang ini ditandatangani, pihak yang berutang tidak lagi menguasai barang-barang jaminan atau agunan sebagaimana dimaksud dalam perjanjian ini, kecuali apabila barang tersebut diberikan kembali oleh Koperasi untuk dipergunakan sementara sebagai pinjam pakai (bruikleen), sehingga pihak yang berutang bertindak sebagai peminjam pakai (bruikleener).
            <br><br>Apabila pada saat jatuh tempo utang tersebut belum dilunasi oleh pihak yang berutang, dan setelah diberitahukan oleh Koperasi kepada pihak penerima kredit namun tetap tidak dilaksanakan pelunasan dalam jangka waktu yang dianggap layak menurut pertimbangan Koperasi, maka tanpa memerlukan perintah atau penetapan dari juru sita maupun surat-surat lainnya, Koperasi berhak untuk melakukan eksekusi terhadap barang-barang jaminan atau agunan dimaksud.
            <br><br>Eksekusi tersebut dapat dilakukan dengan cara menjual atau melelang barang jaminan tersebut, baik melalui pelelangan umum maupun penjualan di bawah tangan, atau dengan cara lain yang dianggap patut dan sah menurut ketentuan yang ditetapkan oleh Koperasi.
            <br><br>Pihak yang berutang dengan ini menyatakan bahwa surat pengakuan utang ini dibuat dengan penuh kesadaran, tanpa adanya paksaan dari pihak mana pun, serta dengan tanggung jawab penuh agar dapat dipergunakan sebagaimana mestinya.
            <br><br>Pihak yang berutang juga menyatakan setuju untuk memilih tempat kedudukan hukum (domisili hukum) yang tetap dan tidak berubah pada Kantor Panitera Pengadilan Negeri Denpasar, untuk segala hal yang berkaitan dengan pelaksanaan dan akibat hukum dari surat pengakuan utang ini.
        </div>

        <div class="signature-wrap">
            <table class="signature-table mt-20">
                <tr>
                    <td style="text-align:right">Denpasar, {{ $tanggalCetak }}<br>Yang Berhutang</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td style="text-align:right"><strong>( {{ $namaNasabah }} )</strong></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>