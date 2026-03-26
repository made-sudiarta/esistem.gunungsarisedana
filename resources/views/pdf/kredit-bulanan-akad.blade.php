<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Akad Kredit Bulanan</title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 10pt;
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
        .identity-table, .info-table, .signature-table, .line-table { width: 100%; border-collapse: collapse; }
        .identity-table td, .info-table td, .signature-table td, .line-table td { vertical-align: top; padding: 2px 0; }
        .identity-table td.label, .info-table td.label { width: 170px; }
        .identity-table td.sep, .info-table td.sep { width: 14px; text-align: center; }
        .signature-wrap { margin-top: 28px; }
        .signature-table td { width: 33.33%; text-align: center; padding-top: 8px; }
        .signature-space { height: 70px; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center {text-align:center;}
        .mt-12 { margin-top: 12px; }
        .mt-20 { margin-top: 20px; }
        .bold { font-weight: bold; }
        .pasal-title { text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 6px; }
        .fidusia-table td {
            vertical-align: top;
            padding: 2px 0;
        }
    </style>
</head>
<body>
@php
    use Carbon\Carbon;

    $ketua = "I Made Sudiarta, S.Kom";
    $manager = "I Wayan Landuh";
    $namaKasir = 'I Made Sudiarta, S.Kom';
    $pengawas = "I Nyoman Supantra";
    $kabagkredit = "I Wayan Wartawan";
    $adminkredit = "I Putu Agus Indrawan";

    $member = $record->member;
    $pj = $record->penanggungJawab;
    $jaminanPertama = $record->jaminans->first();
    $atasNamaJaminanPertama = $jaminanPertama?->atasNamas?->pluck('atas_nama')?->join(', ');

    $tanggalPengajuan = !empty($record->tanggal_pengajuan)
        ? Carbon::parse($record->tanggal_pengajuan)
        : now();

    $bulanRomawi = [
        '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
        '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
        '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII',
    ];

    $nomorAkad = str_pad($record->no_pokok ?? 0, 4, '0', STR_PAD_LEFT)
        . '/KGS/'
        . ($bulanRomawi[$tanggalPengajuan->format('m')] ?? '')
        . '/'
        . $tanggalPengajuan->format('Y');

    $namaPeminjam = $member?->nama_lengkap ?? '-';
    $tempatLahir = $pj?->tempat_lahir ?? $member?->tempat_lahir ?? '-';

    $tanggalLahir = !empty($pj?->tanggal_lahir)
        ? Carbon::parse($pj->tanggal_lahir)->format('d-m-Y')
        : (!empty($member?->tanggal_lahir)
            ? Carbon::parse($member->tanggal_lahir)->format('d-m-Y')
            : '-');

    $pekerjaan = $pj?->pekerjaan ?? '-';
    $nik = $pj?->nik ?? $member?->nik ?? '-';
    $alamat = $pj?->alamat ?? $member?->alamat ?? '-';

    $jumlahPinjaman = (float) ($record->plafond ?? 0);
    $jangkaWaktu = (int) ($record->jangka_waktu ?? 0);
    $tujuan = $record->tujuan_pinjaman ?? '-';
    $tanggalCetak = now()->translatedFormat('d F Y');

    $jaminanText = $jaminanPertama?->keterangan_jaminan ?? '-';
    $atasNamaText = $atasNamaJaminanPertama ?: '-';

    if (! function_exists('terbilangBulanan')) {
        function terbilangBulanan($angka) {
            $angka = abs((int) $angka);
            $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
            if ($angka < 12) return $baca[$angka];
            if ($angka < 20) return terbilangBulanan($angka - 10) . " Belas";
            if ($angka < 100) return terbilangBulanan(intval($angka / 10)) . " Puluh " . terbilangBulanan($angka % 10);
            if ($angka < 200) return "Seratus " . terbilangBulanan($angka - 100);
            if ($angka < 1000) return terbilangBulanan(intval($angka / 100)) . " Ratus " . terbilangBulanan($angka % 100);
            if ($angka < 2000) return "Seribu " . terbilangBulanan($angka - 1000);
            if ($angka < 1000000) return terbilangBulanan(intval($angka / 1000)) . " Ribu " . terbilangBulanan($angka % 1000);
            if ($angka < 1000000000) return terbilangBulanan(intval($angka / 1000000)) . " Juta " . terbilangBulanan($angka % 1000000);
            return number_format($angka, 0, ',', '.');
        }
    }

    $terbilangPinjaman = trim(terbilangBulanan($jumlahPinjaman)) . ' Rupiah';
@endphp

    <div class="akad-page">
        <div class="header">
            <div class="koperasi-title">Koperasi Simpan Pinjam</div>
            <div class="koperasi-subtitle">Gunung Sari Sedana Denpasar</div>
            <div class="koperasi-meta">Nomor Badan Hukum : 396 / BH / XXVII.9 / XII / 2015</div>
            <div class="koperasi-meta">Jalan Gunung Guntur Gg. XIX No. 9 Padangsambian, Denpasar Barat</div>
        </div>

        <div class="divider"></div>

        <div class="text-left">Perihal : <b><u>Permohonan Kredit Bulanan</u></b></div>
        <div class="text-left">Nomor : <b>{{ $nomorAkad }}</b></div>
        <br>

        <div class="paragraph">Dengan hormat,</div>

        <table class="identity-table">
            <tr>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td>{{ $namaPeminjam }}</td>
            </tr>
            <tr>
                <td class="label">Tempat / Tanggal Lahir</td>
                <td class="sep">:</td>
                <td>{{ $tempatLahir }}, {{ $tanggalLahir }}</td>
            </tr>
            <tr>
                <td class="label">Pekerjaan</td>
                <td class="sep">:</td>
                <td>{{ $pekerjaan }}</td>
            </tr>
            <tr>
                <td class="label">No. KTP / SIM / Domisili</td>
                <td class="sep">:</td>
                <td>{{ $nik }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td>{{ $alamat }}</td>
            </tr>
        </table>

        <div class="paragraph mt-12">
            Dengan ini mengajukan permohonan kredit.
        </div>

        <div class="pasal">
            <table class="info-table">
                <tr>
                    <td class="label">a. Sebesar</td>
                    <td class="sep">:</td>
                    <td><strong>Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}</strong> ({{ $terbilangPinjaman }})</td>
                </tr>
                <tr>
                    <td class="label">b. Jangka Waktu</td>
                    <td class="sep">:</td>
                    <td>{{ $jangkaWaktu }} Bulan</td>
                </tr>
                <tr>
                    <td class="label">c. Dengan tujuan</td>
                    <td class="sep">:</td>
                    <td>{{ $tujuan }}</td>
                </tr>
                <tr>
                    <td class="label">d. Sebagai jaminan kami serahkan</td>
                    <td class="sep">:</td>
                    <td>{{ $jaminanText }} ({{ $atasNamaText }})</td>
                </tr>
            </table>
        </div>

        <div class="paragraph mt-12">
            Sebagai bahan pertimbangan kami lampirkan sebagai berikut:
            <table class="info-table">
                <tr><td class="label">1. Copy identitas yang masih berlaku Suami / Istri</td></tr>
                <tr><td class="label">2. Copy jaminan BPKB</td></tr>
                <tr><td class="label">3. Copy Kartu Keluarga yang masih berlaku</td></tr>
                <tr><td class="label">4. Bukti Simpanan Sukarela / Berjangka pada Koperasi Gunung Sari Sedana Denpasar</td></tr>
                <tr><td class="label">5. Daftar Gaji dari Instansi / Perusahaan</td></tr>
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
                    <td><strong>( {{$manager}} )</strong></td>
                    <td><strong>( {{$ketua}} )</strong></td>
                    <td><strong>( {{ $namaPeminjam }} )</strong></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="akad-page">
        @php
            $pokokPerBulan = $jangkaWaktu > 0
                ? ceil((($jumlahPinjaman / $jangkaWaktu) / 1000)) * 1000
                : 0;

            $bungaNominalPerBulan = ceil((($jumlahPinjaman * ((float) $record->bunga_persen ?? 0) / 100) / 1000)) * 1000;

            $angsuranPerBulan = $pokokPerBulan + $bungaNominalPerBulan;

            $tanggalPengajuanFormat = !empty($record->tanggal_pengajuan)
                ? Carbon::parse($record->tanggal_pengajuan)->translatedFormat('d F Y')
                : '-';

            $tanggalJatuhTempoFormat = !empty($record->tanggal_jatuh_tempo)
                ? Carbon::parse($record->tanggal_jatuh_tempo)->translatedFormat('d F Y')
                : '-';

            $bungaPersen = (float) ($record->bunga_persen ?? 0);
            $totalPersenBiaya = (float) ($record->biaya_adm_persen ?? 0)
                + (float) ($record->biaya_provisi_persen ?? 0)
                + (float) ($record->biaya_op_persen ?? 0);

            $jaminan1 = $record->jaminans->get(0);
            $jaminan2 = $record->jaminans->get(1);

            $jaminan1Text = $jaminan1?->keterangan_jaminan ?? '-';
            $atasNama1 = $jaminan1?->atasNamas?->pluck('atas_nama')?->join(', ') ?: '-';

            $jaminan2Text = $jaminan2?->keterangan_jaminan ?? '-';
            $atasNama2 = $jaminan2?->atasNamas?->pluck('atas_nama')?->join(', ') ?: '-';

            if (! function_exists('terbilangAkadBulanan')) {
                function terbilangAkadBulanan($angka) {
                    $angka = abs((int) round($angka));
                    $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
                    if ($angka < 12) return $baca[$angka];
                    if ($angka < 20) return terbilangAkadBulanan($angka - 10) . " Belas";
                    if ($angka < 100) return terbilangAkadBulanan(intval($angka / 10)) . " Puluh " . terbilangAkadBulanan($angka % 10);
                    if ($angka < 200) return "Seratus " . terbilangAkadBulanan($angka - 100);
                    if ($angka < 1000) return terbilangAkadBulanan(intval($angka / 100)) . " Ratus " . terbilangAkadBulanan($angka % 100);
                    if ($angka < 2000) return "Seribu " . terbilangAkadBulanan($angka - 1000);
                    if ($angka < 1000000) return terbilangAkadBulanan(intval($angka / 1000)) . " Ribu " . terbilangAkadBulanan($angka % 1000);
                    if ($angka < 1000000000) return terbilangAkadBulanan(intval($angka / 1000000)) . " Juta " . terbilangAkadBulanan($angka % 1000000);
                    return number_format($angka, 0, ',', '.');
                }
            }

            $terbilangPlafond = trim(terbilangAkadBulanan($jumlahPinjaman)) . ' Rupiah';
            $terbilangJangka = trim(terbilangAkadBulanan($jangkaWaktu));
            $terbilangBunga = trim(terbilangAkadBulanan($bungaPersen));
            $terbilangBiaya = trim(terbilangAkadBulanan($totalPersenBiaya));
        @endphp

        <div class="doc-title">Perjanjian Kredit</div>
        <div class="doc-number">Nomor : {{ $nomorAkad }}</div>
        <div class="divider"></div>

        <div class="paragraph">Yang bertanda tangan dibawah ini :</div>

        <table class="identity-table">
            <tr>
                <td style="width: 20px;">I.</td>
                <td>
                    <strong>{{ strtoupper($manager) }}</strong> Manager KOPERASI GUNUNG SARI SEDANA di Jl. Gunung Guntur Gang XIX
                    No. 9 Padangsambian Denpasar, dalam hal ini bertindak untuk dan atas nama KOPERASI GUNUNG SARI SEDANA,
                    berdasarkan Badan Hukum No.396/BH/XXVII.9/XII/2015 yang selanjutnya disebut sebagai
                    <strong>PIHAK PERTAMA</strong> atau <strong>PEMBERI KREDIT</strong>.
                </td>
            </tr>
            <tr>
                <td style="width: 20px; padding-top: 10px;">II.</td>
                <td style="padding-top: 10px;">
                    <strong>{{ strtoupper($namaPeminjam) }}</strong> yang beralamat di <strong>{{ $alamat }}</strong>,
                    dalam hal ini bertindak untuk dan atas nama diri sendiri, selanjutnya disebut
                    <strong>PIHAK KEDUA</strong>. Dengan ini menggabungkan diri masing-masing untuk memikul hutang
                    sejumlah yang tersebut dibawah ini, atau segala hutang yang akan timbul karena perjanjian ini,
                    jadi berarti baik secara bersama-sama maupun seorang demi seorang saja menanggung segala hutang
                    (<i>Hoofdelik</i>), untuk selanjutnya disebut <strong>PENERIMA KREDIT</strong>.
                </td>
            </tr>
        </table>

        <div class="paragraph mt-12">
            Dengan ini sepakat mengadakan Perjanjian Kredit dengan ketentuan-ketentuan dan syarat-syarat berikut:
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 1</div>
            <div class="paragraph">
                Maksimum Kredit yang diberikan Koperasi kepada <strong>PENERIMA KREDIT</strong> adalah sebesar:
            </div>

            <div class="doc-number" style="margin-top: 8px; margin-bottom: 8px;">
                Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}
            </div>

            <div class="paragraph text-center">
                ({{ $terbilangPlafond }})
            </div>

            <div class="paragraph">
                Dalam jangka waktu <strong>{{ $jangkaWaktu }}</strong> ({{ $terbilangJangka }}) Bulan.
                Terhitung dari tanggal <strong>{{ $tanggalPengajuanFormat }}</strong> sampai berakhir pada tanggal
                <strong>{{ $tanggalJatuhTempoFormat }}</strong>.
                <strong>PENERIMA KREDIT</strong> wajib membayar angsuran Kredit kepada <strong>KOPERASI</strong>
                dengan cara dan ketentuan yaitu mengangsur selama <strong>{{ $jangkaWaktu }}</strong> Bulan.
                Perincian sebagai berikut:
            </div>

            <table class="info-table" style="width: 100%; font-size:9pt; padding-left:20%; padding-right:20%">
                <tr>
                    <td class="label">Pokok</td>
                    <td class="sep">:</td>
                    <td style="text-align:right;">Rp {{ number_format($pokokPerBulan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Bunga</td>
                    <td class="sep">:</td>
                    <td style="text-align:right;">Rp {{ number_format($bungaNominalPerBulan, 0, ',', '.') }}</td>
                </tr>
                <tr style="border-top:1px solid">
                    <td class="label"><strong>Total</strong></td>
                    <td class="sep"><strong>:</strong></td>
                    <td style="text-align:right;"><strong>Rp {{ number_format($angsuranPerBulan, 0, ',', '.') }}</strong></td>
                </tr>
            </table>

            <div class="paragraph mt-12">
                Apabila Perjanjian Kredit ini telah berakhir ternyata kredit belum dilunasi maka sebelum diperpanjang
                dan atau diperbaharui, Perjanjian Kredit ini masih berlaku.
            </div>
        </div>
        <br>
        <div class="pasal">
            <div class="pasal-title">Pasal 2</div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px;">1.</td>
                    <td>
                        Penerima Kredit wajib membayar bunga Kredit sebesar <strong>{{ rtrim(rtrim(number_format($bungaPersen, 2, ',', '.'), '0'), ',') }} %</strong>
                        ({{ $terbilangBunga }}) per bulan dan diperhitungkan secara menurun.
                    </td>
                </tr>
                <tr>
                    <td class="sep">2.</td>
                    <td>
                        Provisi dan biaya administrasi yang harus dibayar oleh <strong>PENERIMA KREDIT</strong> kepada
                        <strong>KOPERASI</strong> adalah sebesar
                        <strong>{{ rtrim(rtrim(number_format($totalPersenBiaya, 2, ',', '.'), '0'), ',') }} %</strong>
                        ({{ $terbilangBiaya }}) dari maksimum Kredit, dan tidak dapat ditarik kembali,
                        sekalipun Kredit ini tidak jadi dipergunakan.
                    </td>
                </tr>
                <tr>
                    <td class="sep">3.</td>
                    <td>
                        Terhadap jumlah angsuran yang terlambat dibayar <strong>PENERIMA KREDIT</strong> dikenakan denda
                        sebesar <strong>10% (Sepuluh Persen)</strong> setiap bulan dari angsuran yang tertunggak.
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <div class="pasal">
            <div class="pasal-title">Pasal 3</div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px;">1.</td>
                    <td>
                        Segala harta kekayaan <strong>PENERIMA KREDIT</strong> baik yang bergerak maupun yang tidak bergerak,
                        baik yang sudah ada maupun yang akan ada dikemudian hari, menjadi jaminan pelunasan jumlah kredit
                        yang timbul karena Perjanjian Kredit ini. Jika dianggap perlu <strong>KOPERASI</strong> berhak
                        meminta tambahan jaminan baru atau pengganti jaminan yang lama.
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="akad-page">
        <div class="pasal">
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px;">2.</td>
                    <td>
                        Guna lebih menjamin pembayaran Kredit tersebut, oleh <strong>PENERIMA KREDIT</strong> diserahkan
                        kepada <strong>KOPERASI</strong> barang-barang jaminan sebagai berikut:
                        <table class="identity-table" style="margin-top: 8px;">
                            <tr>
                                <td class="sep">a.</td>
                                <td class="label">Sebuah</td>
                                <td class="sep">:</td>
                                <td>{{ $jaminan1Text }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="label">Atas Nama</td>
                                <td class="sep">:</td>
                                <td>{{ $atasNama1 }}</td>
                            </tr>
                            <tr>
                                <td class="sep">b.</td>
                                <td class="label">Sebuah</td>
                                <td class="sep">:</td>
                                <td>{{ $jaminan2Text }}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td class="label">Atas Nama</td>
                                <td class="sep">:</td>
                                <td>{{ $atasNama2 }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        @php
            $namaPenanggungJawab = $pj?->nama ?? '-';
            $tanggalHariIniFormat = now()->translatedFormat('d F Y');
        @endphp
        <br>
        <div class="pasal">
            <div class="pasal-title">Pasal 4</div>
            <div class="paragraph mt-12">
                <strong>KOPERASI</strong> berhak untuk menagih kredit ini dengan seketika dan sekaligus,
                termasuk bunga, provisi dan ongkos lainnya apabila <strong>PENERIMA KREDIT</strong>:
            </div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px;">1.</td>
                    <td>Melalaikan kewajibannya membayar angsuran pokok, bunga, provisi, denda dan ongkos-ongkos lainnya.</td>
                </tr>
                <tr>
                    <td class="sep">2.</td>
                    <td>Meninggal dunia.</td>
                </tr>
                <tr>
                    <td class="sep">3.</td>
                    <td>Dinyatakan pailit atau karena apapun juga tidak berhak lagi mengurus dan atau menguasai harta kekayaannya.</td>
                </tr>
                <tr>
                    <td class="sep">4.</td>
                    <td>Harta kekayaannya sebagian atau seluruhnya disita oleh orang atau badan hukum lainnya.</td>
                </tr>
                <tr>
                    <td class="sep">5.</td>
                    <td>Tidak mematuhi peraturan-peraturan dan ketentuan-ketentuan yang telah ditetapkan dalam perjanjian Kredit ini.</td>
                </tr>
            </table>
        </div>
        <br>
        <div class="pasal">
            <div class="pasal-title">Pasal 5</div>
            <div class="paragraph mt-12">
                Apabila <strong>PENERIMA KREDIT</strong> tidak membayar hutangnya <strong>3 (Tiga) kali</strong>
                berturut-turut maka otomatis <strong>KOPERASI</strong> menerima kuasa penuh yang tidak dapat
                dibatalkan oleh apapun / siapapun juga untuk menjual harta kekayaannya yang menjadi jaminan
                seperti tersebut dalam Pasal 3 diatas, baik dihadapan umum maupun dibawah tangan, dengan harga
                dan syarat-syarat yang disetujui oleh <strong>KOPERASI</strong> dan bila masih ada kelebihan
                diserahkan kepada <strong>PENERIMA KREDIT</strong> dan sebaliknya bila hasil penjualan tersebut
                kurang maka <strong>PENERIMA KREDIT</strong> wajib menambah kekurangannya sampai dengan dianggap
                cukup oleh <strong>KOPERASI GUNUNG SARI SEDANA DENPASAR</strong>.
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal Penutup</div>
            <div class="paragraph mt-12">
                Segala akibat perjanjian Kredit ini kedua belah pihak memilih tempat kediaman hukum yang sah
                dan tidak berubah di kantor Pengadilan Negeri di Denpasar.
            </div>
        </div>

        <div class="signature-wrap" style="margin-top: -10px;">
            <table class="signature-table ">
                
                <tr>
                    <td><br>Menyetujui,<br>Manager</td>
                    <td><br>Menyetujui,<br>Kabag. Kredit</td>
                    <td>Denpasar, {{ $tanggalHariIniFormat }}<br>Penerima Kredit,<br>Peminjam</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td><strong>( {{$manager}} )</strong></td>
                    <td><strong>( {{$kabagkredit}} )</strong></td>
                    <td><strong>( {{ $namaPeminjam }} )</strong></td>
                </tr>
                <tr>
                    <td>Mengetahui,<br>Ketua</td>
                    <td>Mengetahui,<br>Pengawas</td>
                    <td>Penanggung Suami/Istri/Wali,<br>Penanggungjawab</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td><strong>( {{ $ketua }} )</strong></td>
                    <td><strong>( {{ $pengawas }} )</strong></td>
                    <td><strong>( {{ $namaPenanggungJawab }} )</strong></td>
                </tr>
            </table>
        </div>
    </div>
        <div class="akad-page">
        @php
            $tempatLahirPeminjam = $tempatLahir ?? '-';
            $tanggalLahirPeminjam = $tanggalLahir ?? '-';
            $pekerjaanPeminjam = $pekerjaan ?? '-';
            $nikPeminjam = $nik ?? '-';
            $alamatPeminjam = $alamat ?? '-';

            $namaPenanggungJawab = $pj?->nama ?? '-';
            $tempatLahirPenanggung = $pj?->tempat_lahir ?? '-';
            $tanggalLahirPenanggung = !empty($pj?->tanggal_lahir)
                ? Carbon::parse($pj->tanggal_lahir)->format('d-m-Y')
                : '-';
            $pekerjaanPenanggung = $pj?->pekerjaan ?? '-';
            $nikPenanggung = $pj?->nik ?? '-';
            $alamatPenanggung = $pj?->alamat ?? '-';

            $tanggalHariIniFormat = now()->translatedFormat('d F Y');
        @endphp

        <div class="doc-title">Surat Pengakuan Hutang</div>
        <div class="divider"></div>

        <div class="paragraph">Yang bertanda tangan dibawah ini :</div>

        <table class="identity-table">
            <tr>
                <td style="width: 20px;">I.</td>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td>{{ $namaPeminjam }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="sep">:</td>
                <td>{{ $tempatLahirPeminjam }}, {{ $tanggalLahirPeminjam }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Pekerjaan</td>
                <td class="sep">:</td>
                <td>{{ $pekerjaanPeminjam }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Nomor KTP/SIM/Domisili</td>
                <td class="sep">:</td>
                <td>{{ $nikPeminjam }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Alamat Rumah</td>
                <td class="sep">:</td>
                <td>{{ $alamatPeminjam }}</td>
            </tr>

            <tr><td colspan="4" style="height: 10px;"></td></tr>

            <tr>
                <td>II.</td>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td>{{ $namaPenanggungJawab }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="sep">:</td>
                <td>{{ $tempatLahirPenanggung }}, {{ $tanggalLahirPenanggung }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Pekerjaan</td>
                <td class="sep">:</td>
                <td>{{ $pekerjaanPenanggung }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Nomor KTP/SIM/Domisili</td>
                <td class="sep">:</td>
                <td>{{ $nikPenanggung }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Alamat Rumah</td>
                <td class="sep">:</td>
                <td>{{ $alamatPenanggung }}</td>
            </tr>
        </table>

        <div class="paragraph mt-12">
            Dengan ini menggabungkan diri masing-masing untuk memikul hutang sejumlah dibawah ini atau segala
            hutang yang akan ditimbulkan karena pengakuan ini. Jadi berarti bahwa baik secara bersama-sama
            maupun seorang diri demi seorang atau khusus seorang saja menanggung segala hutang
            (<i>hoofdelijk</i>). Yang untuk selanjutnya dinamakan juga yang berhutang kepada
            <strong>KOPERASI GUNUNG SARI SEDANA DENPASAR</strong>, berkedudukan di Jl. Gunung Guntur
            Gang XIX No. 9 Padangsambian Denpasar Barat. Bahwa dalam surat pengakuan hutang ini untuk
            selanjutnya disebut dengan nama <strong>Koperasi</strong>. Adapun jumlah hutang adalah sebesar:
        </div>

        <div class="doc-number" style="margin-top: 10px; margin-bottom: 10px;">
            Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}
        </div>

        <div class="paragraph text-center">
            ({{ $terbilangPlafond }})
        </div>

        <div class="paragraph">
            Semenjak saat pengakuan ini ditandatangani yang berhutang tidak lagi menguasai barang-barang
            jaminan / anggunan tersebut diatas. Kecuali yang diberikan oleh Koperasi secara pinjam pakai
            (<i>bruikleen</i>), sehingga yang berhutang menjadi <i>bruikleener</i>. Bila setelah tiba waktunya
            hutang-hutang sudah harus dilunasi oleh yang berhutang dan oleh Koperasi telah diberitahukan
            kepada pengambilan kredit, akan tetapi tidak dibayar lunas dalam waktu yang layak menurut
            pertimbangan Koperasi, maka tanpa suatu surat juru sita atau surat-surat lainnya Koperasi berhak
            eksekusi terhadap barang-barang jaminan atau anggunan tersebut maupun menguasai sendiri,
            menjual atau melelang dimuka umum atau secara dibawah tangan atau cara-cara lainnya, dengan
            syarat-syarat yang ditentukan oleh Koperasi.
        </div>

        <div class="paragraph">
            Bahwa yang berhutang membuat Surat Pengakuan Hutang dengan penuh kesadaran dan tanggung jawab,
            agar dapat dipergunakan dengan baik dan semestinya. Yang berhutang mengaku dan setuju untuk
            memilih tempat kediaman hukum (<i>domicilie</i>) yang tetap dan tidak berubah-ubah dalam
            pengakuan hutang ini, dan dalam hal yang berhubungan dengan segala akibatnya di Kantor Panitera
            Pengadilan Negeri Denpasar.
        </div>

        <div class="signature-wrap">
            <table class="signature-table">
                <tr>
                    <td style="text-align: right;">
                        Denpasar, {{ $tanggalHariIniFormat }} <br><br><br>
                        <div class="signature-space"></div>
                        <strong>( {{ $namaPeminjam }} )</strong>
                    </td>
                </tr>
            </table>
        </div>
    </div>
        <div class="akad-page">
        @php
            $fidusia = $jaminanPertama?->fidusia;

            $fidusiaMerk = $fidusia?->merk ?? '-';
            $fidusiaType = $fidusia?->type ?? '-';
            $fidusiaWarna = $fidusia?->warna ?? '-';
            $fidusiaTahun = $fidusia?->tahun ?? '-';
            $fidusiaNoRangka = $fidusia?->no_rangka ?? '-';
            $fidusiaNoMesin = $fidusia?->no_mesin ?? '-';
            $fidusiaNoPolisi = $fidusia?->no_polisi ?? '-';
            $fidusiaNoBpkb = $fidusia?->no_bpkb ?? '-';
            $fidusiaAtasNama = $fidusia?->atasnama ?? ($atasNamaText ?? '-');
            $fidusiaTaksiran = (float) ($fidusia?->taksiran_harga ?? 0);
            $fidusiaTempat = $fidusia?->tempat_penyimpanan ?? '-';
        @endphp

        <div class="doc-title">Perjanjian Fidusia</div>
        <div class="doc-number">Nomor : {{ $nomorAkad }}</div>
        <div class="divider"></div>

        <div class="paragraph">Yang bertanda tangan dibawah ini :</div>

        <table class="identity-table">
            <tr>
                <td style="width: 20px;">I.</td>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td>{{ $namaPeminjam }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Identitas</td>
                <td class="sep">:</td>
                <td>{{ $nikPeminjam ?? $nik }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td>{{ $alamatPeminjam ?? $alamat }}</td>
            </tr>
        </table>

        <div class="paragraph mt-12">
            Untuk selanjutnya disebut sebagai <strong>PEMBERI FIDUSIA</strong>.
        </div>

        <table class="identity-table">
            <tr>
                <td style="width: 20px;">II.</td>
                <td>
                    <strong>{{ strtoupper($manager) }}</strong> Manager KOPERASI GUNUNG SARI SEDANA DENPASAR
                    di Jl. Gunung Guntur Gang XIX No. 9 Padangsambian, dalam hal ini bertindak
                    untuk dan atas nama <strong>KOPERASI GUNUNG SARI SEDANA</strong> berdasarkan
                    Badan Hukum No. : 396/BH/XXVII.9/XII/2015.
                </td>
            </tr>
        </table>

        <div class="paragraph mt-12">
            Kedua belah pihak terlebih dahulu mengemukakan sebagai berikut:
        </div>

        <div class="paragraph">
            Antara Pemberi Fidusia dengan <strong>KOPERASI</strong> telah terjadi perikatan hukum
            berkenaan dengan perjanjian pemberian kredit No. <strong>{{ $nomorAkad }}</strong>.
            Guna menjamin pembayaran hutang serta biaya-biaya lain yang timbul dari perikatan tersebut,
            berikut perikatan-perikatan lainnya yang akan timbul dikemudian hari, dengan ini
            <strong>PEMBERI FIDUSIA</strong> memberikan jaminan dalam bentuk <strong>FIDUSIA</strong>
            dengan ketentuan dan syarat-syarat sebagai berikut:
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 1</div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px;">1.</td>
                    <td>
                        <strong>PEMBERI FIDUSIA</strong> menyerahkan kepada <strong>KOPERASI</strong>
                        secara fidusia barang-barang sebagaimana terinci dalam daftar dibalik ini.
                    </td>
                </tr>
                <tr>
                    <td class="sep">2.</td>
                    <td>
                        Barang-barang yang diserahkan <strong>PEMBERI FIDUSIA</strong> secara fidusia tersebut
                        adalah benar-benar hak milik <strong>PEMBERI FIDUSIA</strong> sendiri, tidak ada pihak
                        lain yang ikut memiliki atau mempunyai hak berupa apapun, tidak dijadikan sebagai jaminan
                        dengan cara bagaimanapun kepada pihak lain, tidak tersangkut dalam perkara maupun sengketa,
                        serta bebas dari sitaan.
                    </td>
                </tr>
            </table>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 2</div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px;">1.</td>
                    <td>
                        Sejak berlakunya perjanjian barang-barang yang difidusiakan pemiliknya berada pada
                        <strong>KOPERASI</strong> sedangkan secara fisik tetap dikuasai dan berada pada
                        <strong>PEMBERI FIDUSIA</strong> dengan kedudukan sebagai peminjam pakai.
                    </td>
                </tr>
                <tr>
                    <td class="sep">2.</td>
                    <td>
                        Sebagai peminjam pakai barang-barang yang difidusiakan pemiliknya berada pada
                        <strong>KOPERASI</strong>, Pemberi Fidusia:
                        <table class="identity-table" style="margin-top: 8px;">
                            <tr>
                                <td class="sep">a.</td>
                                <td>
                                    Bertanggung jawab sepenuhnya atas barang-barang tersebut yang menyangkut jumlah,
                                    nilai, bentuk maupun jenisnya dan sehubungan dengan itu wajib memelihara dengan
                                    sebaik-baiknya, memperbaiki atau mengganti barang-barang yang sama jenisnya dan
                                    nilainya bila barang-barang tersebut hilang, berkurang nilainya, rusak atau tidak
                                    dapat dipergunakan lagi.
                                </td>
                            </tr>
                            <tr>
                                <td class="sep">b.</td>
                                <td>
                                    Dilarang untuk menyewakan, meminjamkan dengan cara bagaimanapun kepada pihak lain
                                    dan mengubah bentuk atas barang-barang yang difidusiakan tersebut tanpa persetujuan
                                    tertulis dari <strong>KOPERASI</strong>.
                                </td>
                            </tr>
                            <tr>
                                <td class="sep">c.</td>
                                <td>
                                    Wajib membuat dan menyerahkan kepada Koperasi daftar baru barang-barang yang
                                    difidusiakan sebagai pengganti dari daftar fidusia yang sudah ada seperti yang
                                    dimaksud dalam Pasal 1, bila diminta Koperasi atau bila terjadi perubahan, baik
                                    mengenai jumlah, nilai, bentuk maupun jenisnya.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="akad-page">
        <div class="pasal">
            <div class="pasal-title">Pasal 3</div>
            <div class="paragraph mt-12">
                Koperasi atau kuasanya atau pihak lain yang ditunjukkan oleh Koperasi berhak
                dan dengan ini disetujui serta diijinkan oleh Pemberi Fidusia untuk memasuki
                tempat-tempat dimana barang-barang tersebut disimpan guna memeriksa keadaan
                barang-barang tersebut.
            </div>
        </div>
        <br>
        <div class="pasal">
            <div class="pasal-title">Pasal 4</div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px;">1.</td>
                    <td>
                        Bila hutang yang timbul dari perikatan dimaksud tidak diselesaikan sebagaimana
                        mestinya atas permintaan <strong>KOPERASI</strong>, Pemberi Fidusia wajib
                        menyerahkan barang-barang tersebut secara fisik selambat-lambatnya
                        <strong>14 (Empat Belas) hari</strong> sejak diterimanya permintaan tertulis
                        dari <strong>KOPERASI</strong>.
                    </td>
                </tr>
                <tr>
                    <td class="sep">2.</td>
                    <td>
                        Bila sampai batas yang ditentukan, Pemberi Fidusia tidak menyerahkan
                        barang-barang tersebut, <strong>KOPERASI</strong> diberi hak:
                        <table class="identity-table" style="margin-top: 8px;">
                            <tr>
                                <td class="sep">a.</td>
                                <td>
                                    Menguasai barang-barang tersebut secara fisik dengan cara mengambil
                                    sendiri maupun dengan bantuan pihak lain dari penguasaan Pemberi
                                    Fidusia; dan / atau,
                                </td>
                            </tr>
                            <tr>
                                <td class="sep">b.</td>
                                <td>
                                    Menempatkan petugas dan / atau pihak lain yang ditunjuk oleh
                                    <strong>KOPERASI</strong> pada tempat-tempat penyimpanan barang-barang
                                    yang difidusiakan dengan maksud untuk melakukan pengawasan agar tidak
                                    terjadi perubahan terhadap jumlah, nilai, bentuk maupun jenis barang tersebut.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="sep">3.</td>
                    <td>
                        Dengan dikuasainya secara fisik barang-barang yang difidusiakan oleh
                        <strong>KOPERASI</strong>, maka <strong>KOPERASI</strong> berhak dan dengan ini
                        diberi kuasa dengan hak substitusi oleh Pemberi Fidusia, kuasa mana merupakan
                        bagian yang terpenting dan tidak dipisahkan dari perjanjian ini, oleh karena itu
                        kuasa tersebut tidak dapat ditarik kembali dan juga tidak akan berakhir karena sebab
                        yang termaksud dalam Pasal 1813 Kitab Undang-Undang Hukum Perdata, untuk menjual
                        barang-barang tersebut secara dibawah tangan maupun melalui lelang dimuka umum
                        berdasarkan kebiasaan setempat dengan syarat-syarat penjualan dan harga yang
                        ditetapkan oleh <strong>KOPERASI</strong>.
                    </td>
                </tr>
                <tr>
                    <td class="sep">4.</td>
                    <td>
                        Dalam hal hasil penjualan barang-barang tersebut melebihi jumlah kewajiban
                        yang timbul dari perikatan termaksud, <strong>KOPERASI</strong> harus
                        mengembalikan kelebihan tersebut kepada Pemberi Fidusia.
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <div class="pasal">
            <div class="pasal-title">Pasal 5</div>
            <div class="paragraph mt-12">
                Bila hutang yang timbul berdasarkan perikatan termaksud telah dilunasi sebagaimana
                mestinya, Perjanjian Fidusia ini dinyatakan berakhir dan tidak mengikat kedua belah pihak.
            </div>
        </div>
        <br>
        <div class="pasal">
            <div class="pasal-title">Pasal 6</div>
            <table class="identity-table">
                <tr>
                    <td class="sep" style="width:30px;">1.</td>
                    <td>
                        Untuk perjanjian ini dan segala akibatnya para pihak memilih tempat kedudukan
                        yang tetap dan seumumnya pada Kantor Panitera Pengadilan Negeri di Denpasar.
                    </td>
                </tr>
                <tr>
                    <td class="sep">2.</td>
                    <td>
                        Perjanjian mulai berlaku sejak ditandatangani oleh kedua belah pihak.
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="akad-page">
        @php
            $fidusia = $jaminanPertama?->fidusia;

            $fidusiaMerk = $fidusia?->merk ?? '-';
            $fidusiaType = $fidusia?->type ?? '-';
            $fidusiaWarna = $fidusia?->warna ?? '-';
            $fidusiaTahun = $fidusia?->tahun ?? '-';
            $fidusiaNoRangka = $fidusia?->no_rangka ?? '-';
            $fidusiaNoMesin = $fidusia?->no_mesin ?? '-';
            $fidusiaNoPolisi = $fidusia?->no_polisi ?? '-';
            $fidusiaNoBpkb = $fidusia?->no_bpkb ?? '-';
            $fidusiaAtasNama = $fidusia?->atasnama ?? ($atasNamaText ?? '-');
            $fidusiaTaksiran = (float) ($fidusia?->taksiran_harga ?? 0);
            $fidusiaTempat = $fidusia?->tempat_penyimpanan ?? 'KSP. GUNUNG SARI SEDANA';
        @endphp

        <div class="doc-title">Daftar Barang - Barang Yang Dijadikan Secara Fidusia</div>
        <div class="divider"></div>

        <table class="identity-table text-center" style="margin-bottom: 10px; border:1px solid; padding:10px;">
            <tr style="border:1px solid; padding-bottom:10px">
                <td style="width: 70px;"><strong>No.</strong></td>
                <td style="width: 320px;"><strong>Jenis Barang</strong></td>
                <td style="width: 50px;"><strong>Taksiran Harga</strong></td>
                <td><strong>Tempat Penyimpanan</strong></td>
            </tr>
            <tr style="padding-bottom:10px;">
                <td style="width: 70px;">1</td>
                <td style="width: 320px;">
                    Merek/Type : {{ $fidusiaMerk }}/{{ $fidusiaType }}<br>
                    Warna/Tahun : {{ $fidusiaWarna }}/{{ $fidusiaTahun }}<br>
                    No. Rangka : {{ $fidusiaNoRangka }}<br>
                    No. Mesin : {{ $fidusiaNoMesin }}<br>
                    No. Polisi : {{ $fidusiaNoPolisi }}<br>
                    No. BPKB : {{ $fidusiaNoBpkb }}<br>
                    Atas Nama : {{ $fidusiaAtasNama }}
                </td>
                <td style="width: 50px; vertical-align: top;">
                    {{ $fidusiaTaksiran > 0 ? 'Rp ' . number_format($fidusiaTaksiran, 0, ',', '.') : '-' }}
                </td>
                <td style="vertical-align: top;">
                    {{ $fidusiaTempat }}
                </td>
            </tr>
        </table>

        <div style="height: 460px;"></div>

        <table class="signature-table">
            <tr>
                <td colspan="3" style="text-align: right; padding-bottom: 10px;">
                    
                </td>
            </tr>
            <tr>
                <td><br>KSP. Gunung Sari Sedana<br>Manager</td>
                <td><br>Mengetahui,<br>Ketua</td>
                <td>Denpasar, {{ now()->translatedFormat('d F Y') }}<br>Pemberi Fidusia<br></td>
            </tr>
            <tr>
                <td class="signature-space"><br><br></td>
                <td class="signature-space"><br><br></td>
                <td class="signature-space"><br><br></td>
            </tr>
            <tr>
                <td><strong>( {{$manager}} )</strong></td>
                <td><strong>( {{$ketua}} )</strong></td>
                <td><strong>( {{ $namaPeminjam }} )</strong></td>
            </tr>
        </table>
    </div>
        <div class="akad-page">
        @php
            $fidusia = $jaminanPertama?->fidusia;

            $fidusiaMerk = $fidusia?->merk ?? '-';
            $fidusiaType = $fidusia?->type ?? '-';
            $fidusiaWarna = $fidusia?->warna ?? '-';
            $fidusiaTahun = $fidusia?->tahun ?? '-';
            $fidusiaNoRangka = $fidusia?->no_rangka ?? '-';
            $fidusiaNoMesin = $fidusia?->no_mesin ?? '-';
            $fidusiaNoPolisi = $fidusia?->no_polisi ?? '-';
            $fidusiaNoBpkb = $fidusia?->no_bpkb ?? '-';
            $fidusiaAtasNama = $fidusia?->atasnama ?? ($atasNamaText ?? '-');
            $tanggalHariIniFormat = now()->translatedFormat('d F Y');
        @endphp

        <div class="doc-title">Surat Keterangan / Pernyataan Cek Pisik Kendaraan Bermotor</div>
        <div class="divider"></div>

        <div class="paragraph">
            Yang bertandatangan dibawah ini :
        </div>

        <table class="identity-table">
            <tr>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td>{{ $namaPeminjam }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td>{{ $alamatPeminjam ?? $alamat }}</td>
            </tr>
        </table>

        <div class="paragraph mt-12">
            Dengan ini menyerahkan Kendaraan Bermotor untuk dilakukan Cek Pisik oleh petugas
            Koperasi dalam hal ini <strong>KOPERASI GUNUNG SARI SEDANA DENPASAR</strong>.
            Dengan spesifikasi sebagai berikut:
        </div>

        <table class="identity-table">
            <tr>
                <td class="label">Merek/Type</td>
                <td class="sep">:</td>
                <td>{{ $fidusiaMerk }}/{{ $fidusiaType }}</td>
            </tr>
            <tr>
                <td class="label">Warna/Tahun</td>
                <td class="sep">:</td>
                <td>{{ $fidusiaWarna }}/{{ $fidusiaTahun }}</td>
            </tr>
            <tr>
                <td class="label">No. Rangka</td>
                <td class="sep">:</td>
                <td>{{ $fidusiaNoRangka }}</td>
            </tr>
            <tr>
                <td class="label">No. Mesin</td>
                <td class="sep">:</td>
                <td>{{ $fidusiaNoMesin }}</td>
            </tr>
            <tr>
                <td class="label">No. Polisi</td>
                <td class="sep">:</td>
                <td>{{ $fidusiaNoPolisi }}</td>
            </tr>
            <tr>
                <td class="label">No. BPKB</td>
                <td class="sep">:</td>
                <td>{{ $fidusiaNoBpkb }}</td>
            </tr>
            <tr>
                <td class="label">Atas Nama</td>
                <td class="sep">:</td>
                <td>{{ $fidusiaAtasNama }}</td>
            </tr>
        </table>

        <div class="paragraph mt-12">
            Yang selanjutnya akan dipergunakan sebagai jaminan Kredit secara <strong>FIDUSIA</strong>.
        </div>

        <div class="paragraph">
            Dengan ini menyatakan bahwa saya akan memelihara, memperbaiki, mengganti peralatan
            dan perlengkapan serta tidak akan memindahtangankan kendaraan sekaligus menanggung
            resiko atas kehilangan kendaraan selama menjadi jaminan di
            <strong>KOPERASI GUNUNG SARI SEDANA</strong>.
        </div>

        <div class="paragraph">
            Demikian surat Keterangan / Pernyataan ini dibuat dengan sebenarnya tanpa paksaan
            dari pihak manapun untuk dapat dipergunakan sebagaimana mestinya.
        </div>

        <div class="paragraph">
            Data Kendaraan secara umum sesuai dengan standar dan layak sebagai jaminan Kredit.
        </div>

        <div class="paragraph">
            Keterangan Tambahan (Accessories) Kendaraan :
        </div>

        <table class="identity-table">
            <tr><td>1. .................................................................................................</td></tr>
            <tr><td>2. .................................................................................................</td></tr>
            <tr><td>3. .................................................................................................</td></tr>
            <tr><td>4. .................................................................................................</td></tr>
            <tr><td>5. .................................................................................................</td></tr>
        </table>

        <div style="height: 30px;"></div>

        <table class="signature-table mt-20">
            <tr>
                <td><br><br>Yang Menyerahkan,</td>
                <td> Denpasar, {{ $tanggalHariIniFormat }}<br>Mengetahui,<br>KSP. GUNUNG SARI SEDANA<br>Manager</td>
                <td><br><br>Adm. Kredit</td>
            </tr>
            <tr>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
                <td class="signature-space"></td>
            </tr>
            <tr>
                <td><strong>( {{ $namaPeminjam }} )</strong></td>
                <td><strong>( {{$manager}} )</strong></td>
                <td><strong>( {{ $adminkredit }} )</strong></td>
            </tr>
        </table>
    </div>
        <div class="akad-page">
        @php
            $tanggalHariIniFormat = now()->translatedFormat('d F Y');
            $jenisMember = $member?->jenis?->keterangan ?? '-';
        @endphp

        <div class="doc-title">Surat Permohonan Menjadi Anggota<br>Koperasi Gunung Sari Sedana Denpasar</div>
        <div class="doc-number">Nomor : {{ $nomorAkad }}</div>
        <div class="divider"></div>

        <div class="paragraph">Dengan hormat,</div>

        <div class="paragraph">Yang bertandatangan dibawah ini :</div>

        <table class="identity-table">
            <tr>
                <td class="label">Nama</td>
                <td class="sep">:</td>
                <td>{{ $namaPeminjam }}</td>
            </tr>
            <tr>
                <td class="label">Tempat, Tanggal Lahir</td>
                <td class="sep">:</td>
                <td>{{ $tempatLahirPeminjam ?? $tempatLahir }}, {{ $tanggalLahirPeminjam ?? $tanggalLahir }}</td>
            </tr>
            <tr>
                <td class="label">Identitas Diri</td>
                <td class="sep">:</td>
                <td>{{ $nikPeminjam ?? $nik }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td>{{ $alamatPeminjam ?? $alamat }}</td>
            </tr>
        </table>

        <div class="paragraph mt-12">
            Dengan ini mengajukan permohonan menjadi anggota
            <strong>KOPERASI GUNUNG SARI SEDANA DENPASAR</strong>.
            Kode : <strong>{{ $jenisMember }}</strong> ({{$member->jenis->jenis}}).
        </div>

        <div class="paragraph">
            Saya bersedia mematuhi peraturan-peraturan yang berlaku di
            <strong>KOPERASI GUNUNG SARI SEDANA DENPASAR</strong>, melaksanakan
            kewajiban-kewajiban dan menerima hak yang ditentukan oleh
            <strong>KOPERASI GUNUNG SARI SEDANA DENPASAR</strong>.
        </div>

        <div class="paragraph">
            Demikian surat permohonan ini saya buat dengan sebenarnya.
            Mohon kiranya dapat diterima. Terima Kasih.
        </div>

        <div style="height: 220px;"></div>

        <table class="signature-table mt-20">
            <tr>
                <td><br>Menyetujui,<br>Ketua</td>
                <td><br>Mengetahui,<br>Pengawas</td>
                <td>Denpasar, {{ $tanggalHariIniFormat }}<br>Hormat kami,<br>Pemohon</td>
            </tr>
            <tr>
                <td class="signature-space"><br><br><br><br></td>
                <td class="signature-space"><br><br><br><br></td>
                <td class="signature-space"><br><br><br><br></td>
            </tr>
            <tr>
                <td><strong>( {{$ketua}} )</strong></td>
                <td><strong>( {{$pengawas}} )</strong></td>
                <td><strong>( {{ $namaPeminjam }} )</strong></td>
            </tr>
        </table>
    </div>
        <div class="akad-page">
        @php
            $biayaAsuransi = (float) ($record->biaya_asuransi ?? 0);
            $biayaAdm = ((float) ($record->plafond ?? 0) * (float) ($record->biaya_adm_persen ?? 0)) / 100;
            $biayaProvisi = ((float) ($record->plafond ?? 0) * (float) ($record->biaya_provisi_persen ?? 0)) / 100;
            $biayaMaterai = (float) ($record->biaya_materai ?? 0);
            $biayaOp = ((float) ($record->plafond ?? 0) * (float) ($record->biaya_op_persen ?? 0)) / 100;
            $biayaKyd = (float) ($record->biaya_kyd ?? 0);
            $biayaLain = (float) ($record->biaya_lain ?? 0);

            $totalBiaya = $biayaAsuransi + $biayaAdm + $biayaProvisi + $biayaMaterai + $biayaOp + $biayaKyd + $biayaLain;
            $diterimaBersih = (float) ($record->plafond ?? 0) - $totalBiaya;

            $tanggalHariIniFormat = now()->translatedFormat('d F Y');
        @endphp

        <div class="header">
            <div class="koperasi-title">Koperasi Gunung Sari Sedana Denpasar</div>
            <div class="koperasi-meta">Badan Hukum No. : 396/BH/XXVII.9/XII/2015</div>
        </div>
        <hr>

        <div class="doc-title">Bukti Kredit Keluar</div>

        <table class="identity-table">
            <tr>
                <td class="label" style="width:230px">Nama</td>
                <td class="sep">:</td>
                <td>{{ $namaPeminjam }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td>{{ $alamatPeminjam ?? $alamat }}</td>
            </tr>
            <tr>
                <td class="label">No. Kredit</td>
                <td class="sep">:</td>
                <td>{{ $nomorAkad }}</td>
            </tr>
            <tr>
                <td class="label">Besar Pinjaman</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Asuransi</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaAsuransi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Administrasi</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaAdm, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Provisi</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaProvisi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Materai</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaMaterai, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya OP.</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaOp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya KYD.</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaKyd, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Lain-lain</td>
                <td class="sep">:</td>
                <td>
                    Rp {{ number_format($biayaLain, 0, ',', '.') }}
                    {{ !empty($record->keterangan_biaya_lain) && $record->keterangan_biaya_lain !== '-' ? ' - ' . $record->keterangan_biaya_lain : '' }}
                </td>
            </tr>
            <tr>
                <td class="label"><strong>Jumlah Biaya - biaya</strong></td>
                <td class="sep"><strong>:</strong></td>
                <td><strong>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td class="label"><strong>Jumlah yang diterima bersih</strong></td>
                <td class="sep"><strong>:</strong></td>
                <td><strong>Rp {{ number_format($diterimaBersih, 0, ',', '.') }}</strong></td>
            </tr>
        </table>

        <div class="signature-wrap" style="margin-top: 0px;">
            <table class="signature-table">
                <tr>
                    <td><br>Kasir</td>
                    <td>Denpasar, {{ $tanggalHariIniFormat }}<br>Penerima Kredit</td>
                </tr>
                <tr>
                    <td class="signature-space"></td>
                    <td class="signature-space"></td>
                </tr>
                <tr>
                    <td><strong>( {{ $namaKasir }} )</strong></td>
                    <td><strong>( {{ $namaPeminjam }} )</strong></td>
                </tr>
            </table>
        </div>

        <br><br>
        <hr style="border:0; border-top:1px dashed #000;">
        <br>

        <div class="header" style="margin-bottom: 10px;">
            <div class="koperasi-title" style="font-size: 11pt;">Koperasi Gunung Sari Sedana Denpasar</div>
            <div class="koperasi-meta" style="font-size:9pt;">Badan Hukum No. : 396/BH/XXVII.9/XII/2015</div>
        </div>
        <hr>

        <div class="doc-title" style="font-size:11pt">Bukti Kredit Keluar</div>

        <table class="identity-table" style="font-size:9pt">
            <tr>
                <td class="label" style="width:230px;">Nama</td>
                <td class="sep">:</td>
                <td>{{ $namaPeminjam }}</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td class="sep">:</td>
                <td>{{ $alamatPeminjam ?? $alamat }}</td>
            </tr>
            <tr>
                <td class="label">No. Kredit</td>
                <td class="sep">:</td>
                <td>{{ $nomorAkad }}</td>
            </tr>
            <tr>
                <td class="label">Besar Pinjaman</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($jumlahPinjaman, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Asuransi</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaAsuransi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Administrasi</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaAdm, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Provisi</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaProvisi, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Materai</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaMaterai, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya OP.</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaOp, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya KYD.</td>
                <td class="sep">:</td>
                <td>Rp {{ number_format($biayaKyd, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">&nbsp;&nbsp;&nbsp;&nbsp;Biaya Lain-lain</td>
                <td class="sep">:</td>
                <td>
                    Rp {{ number_format($biayaLain, 0, ',', '.') }}
                    {{ !empty($record->keterangan_biaya_lain) && $record->keterangan_biaya_lain !== '-' ? ' - ' . $record->keterangan_biaya_lain : '' }}
                </td>
            </tr>
            <tr>
                <td class="label"><strong>Jumlah Biaya - biaya</strong></td>
                <td class="sep"><strong>:</strong></td>
                <td><strong>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td class="label"><strong>Jumlah yang diterima bersih</strong></td>
                <td class="sep"><strong>:</strong></td>
                <td><strong>Rp {{ number_format($diterimaBersih, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
</body>
</html>