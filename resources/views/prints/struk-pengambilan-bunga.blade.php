

@php
if (!function_exists('terbilang')) {
    function terbilang($angka) {
        $angka = abs($angka);
        $angka = (int)$angka;
        $bilangan = [
            "", "satu", "dua", "tiga", "empat", "lima",
            "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"
        ];

        if ($angka < 12) return $bilangan[$angka];
        elseif ($angka < 20) return terbilang($angka - 10) . " belas";
        elseif ($angka < 100) return terbilang(intval($angka / 10)) . " puluh " . terbilang($angka % 10);
        elseif ($angka < 200) return "seratus " . terbilang($angka - 100);
        elseif ($angka < 1000) return terbilang(intval($angka / 100)) . " ratus " . terbilang($angka % 100);
        elseif ($angka < 2000) return "seribu " . terbilang($angka - 1000);
        elseif ($angka < 1000000) return terbilang(intval($angka / 1000)) . " ribu " . terbilang($angka % 1000);
        elseif ($angka < 1000000000) return terbilang(intval($angka / 1000000)) . " juta " . terbilang($angka % 1000000);
        return "";
    }
}

@endphp
<head>
  <title>{{ $title }} - ({{ $count }} Struk | Rp. {{ number_format($bungatotal, 0, ',', '.') }})</title>
</head>
<style>
    body { font-family: sans-serif; font-size: 9px; }

    .row { 
        display: flex; 
        margin-bottom: 18px; 
        padding-bottom: 18px;
        border-bottom: 1px dashed #888;
    }

    .struk-1, .struk-2 {
        width: 50%;
        padding: 8px;
        line-height: 1;
    }

    .struk-1 { 
        margin-right: 18px;
        border-right: 1px dashed #aaa;
        padding-right: 18px;
    }

    .kop-wrap {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
    }
    .kop-logo {
        width: 40px;
        height: 40px;
        margin-right: 8px;
    }
    .kop-text {
        font-size: 9px;
        line-height: 1.2;
    }
    

    /* BOX TERBILANG */
    .terbilang-box {
    background: #e6e6e6;
    padding: 10px 0px;
    display: block;          /* supaya bisa full width */
    width: 100%;             /* melebar penuh */
    text-align: center;      /* tulisannya berada di tengah */
    font-size: 10px;
    font-style: italic;
    font-weight: bold;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

    .page-break { page-break-after: always; }

    .info-table {
    font-size: 9px;
    line-height: 1.2;
    border-collapse: collapse;
    margin-bottom: 5px;
}

.info-table td {
    padding: 1px 2px;
}

.info-table .label {
    width: 70px;          /* Supaya rata */
    font-weight: bold;
}

.info-table .colon {
    width: 10px;
    text-align: center;   /* Kolon tepat di tengah */
    font-weight: bold;
}

.info-table .value {
    width: auto;
}

</style>

@foreach($struks as $i => $data)
    @php 
        $bunga = $data->nominal * (($data->bunga_persen/100)/12);
        $terbilang = strtoupper(terbilang($bunga)) . " RUPIAH";
    @endphp

    <div class="row">

        {{-- STRUK 1 --}}
        <div class="struk-1">
            <div class="kop-wrap">
                <!-- <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f4b0.svg" class="kop-logo"> -->
                 <!-- <img src="{{ public_path('images/logo-koperasi.png') }}" class="kop-logo"> -->
                  <img src="{{ asset('images/logo-koperasi.png') }}" class="kop-logo">



                <div class="kop-text">
                    <strong>KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA</strong><br>
                    Badan Hukum No.: 396/BH/XXVII.9/XII/2015 <br>
                    <p style="margin-top:0px; font-size:5pt;">Jl. Gunung Guntur Gang XIX No. 9 Denpasar Barat <br>
                    Telp. (0361) 8448574 • gunungsari.sedana@gmail.com <br>
                    www.gunungsarisedana.com</p>
                </div>
            </div>

            <strong style="font-size:9px;">BUNGA SIMPANAN BERJANGKA</strong><br>

            <table class="info-table">
                <tr>
                    <td class="label">No. S.BJK</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $data->kode_bilyet }}/{{$data->group->group}}/{{ \Carbon\Carbon::parse($data->tanggal_masuk)->year }}</td>
                </tr>
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $data->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td class="label">Bunga</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. {{ number_format($bunga, 0, ',', '.') }}</td>
                </tr>
            </table>

  
            <em class="terbilang-box">{{ $terbilang }}</em><br>

            @php
                $hari = \Carbon\Carbon::parse($data->tanggal_masuk)->day;
                $bulan = now()->month;
                $tahun = now()->year;

                $tanggalCetak = \Carbon\Carbon::create($tahun, $bulan, $hari);
            @endphp
            Denpasar, {{ $tanggalCetak->format('d M Y') }}
            <br><br><br><br>
            ( {{ $data->nama_lengkap }} )
        </div>

        {{-- STRUK 2 --}}
        <div class="struk-2">
            <div class="kop-wrap">
                <!-- <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f4b0.svg" class="kop-logo"> -->
                 <!-- <img src="{{ public_path('images/logo-koperasi.png') }}" class="kop-logo"> -->
                  <img src="{{ asset('images/logo-koperasi.png') }}" class="kop-logo">



                <div class="kop-text">
                    <strong>KOPERASI SIMPAN PINJAM GUNUNG SARI SEDANA</strong><br>
                    Badan Hukum No.: 396/BH/XXVII.9/XII/2015 <br>
                    <p style="margin-top:0px; font-size:5pt;">Jl. Gunung Guntur Gang XIX No. 9 Denpasar Barat <br>
                    Telp. (0361) 8448574 • gunungsari.sedana@gmail.com <br>
                    www.gunungsarisedana.com</p>
                </div>
            </div>

            <strong style="font-size:9px;">BUNGA SIMPANAN BERJANGKA</strong><br>

            <table class="info-table">
                <tr>
                    <td class="label">No. S.BJK</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $data->kode_bilyet }}/{{$data->group->group}}/{{ \Carbon\Carbon::parse($data->tanggal_masuk)->year }}</td>
                </tr>
                <tr>
                    <td class="label">Nama</td>
                    <td class="colon">:</td>
                    <td class="value">{{ $data->nama_lengkap }}</td>
                </tr>
                <tr>
                    <td class="label">Bunga</td>
                    <td class="colon">:</td>
                    <td class="value">Rp. {{ number_format($bunga, 0, ',', '.') }}</td>
                </tr>
            </table>

  
            <em class="terbilang-box">{{ $terbilang }}</em><br>

            Denpasar, {{ $tanggalCetak->format('d M Y') }}
            <br><br><br><br>
            ( {{ $data->nama_lengkap }} )
        </div>

    </div>

    @if(($i + 1) % 8 == 0)
        <div class="page-break"></div>
    @endif

@endforeach
<p style="text-align:center; margin-bottom:0px; margin-top:0px; font-size:7px;">
    {{ $title }} — ({{ $count }} STRUK) : Rp. {{ number_format($bungatotal, 0, ',', '.') }}
</p>
