<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Print Data Simpanan Berjangka</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 10px; font-size:10pt; }
        h5{ text-align:center; font-size:8pt; margin-top:0px; margin-bottom:10px;}
        tr.footer{font-weight:bold; font-size:10pt; border:1px solid #ccc; background-color:#f2f2f2}
    </style>
</head>
<body>
    <!-- <h2>Data Simpanan Berjangka</h2> -->
     <br>
     <h2>{{ $title }}</h2>
     <h5>{{ date('M Y')}}</h5>


    <table>
        <thead style="font-size:7pt">
            <tr>
                <th>No</th>
                <th>Kode Bilyet</th>
                <!-- <th>Group</th> -->
                <!-- <th>Anggota</th> -->
                <th>Nama Lengkap</th>
                <!-- <th>Terdaftar</th> -->
                <th>Jatuh Tempo</th>
                <th>%</th>
                <th colspan="2">Nominal</th>
                <th colspan="2">Bunga (Rp)</th>
            </tr>
        </thead>
        <tbody style="font-size:7pt">
            @foreach($records as $index => $r)
                @php
                    $bulanmasuk = \Carbon\Carbon::parse($r->tanggal_masuk)->format('m-Y');
                    $bulanskrg = date('m-Y');
                    if($bulanmasuk == $bulanskrg){
                        $bungaRupiah = 0;
                    }else{
                        $bungaRupiah = $r->nominal * ($r->bunga_persen / 12 / 100);
                    }
                @endphp
                <tr style="border-top:1px solid #ccc;">
                    <td width="1">{{ $index + 1 }}</td>
                    <td width="150" style="font-size:5pt">{{ $r->kode_bilyet }}/{{ $r->group->group }}/KGS/{{ \Carbon\Carbon::parse($r->tanggal_masuk)->format('Y') }}</td>
                    <!-- <td>{{ $r->group->group ?? '-' }}</td> -->
                    <!-- <td>
                        @php
                            $nia = $r->member->nia ?? 0;
                        @endphp

                        {{ $nia == 0 ? '-' : str_pad($nia, 5, '0', STR_PAD_LEFT) }}/{{ $r->member->jenis->keterangan }}
                    </td> -->
                    <td width="400" style="text-align:left;">{{ $r->nama_lengkap }}</td>
                    <!-- <td width="90">{{ \Carbon\Carbon::parse($r->tanggal_masuk)->format('d-m-Y') }}</td> -->
                    <td width="200">{{ \Carbon\Carbon::parse($r->tanggal_masuk)->addMonths($r->jangka_waktu)->format('d-m-Y') }}</td>
                    <td width="10">{{ $r->bunga_persen }}%</td>
                    <td width="1">Rp. </td>
                    <td width="30">{{ number_format($r->nominal, 0, ',', '.') }}</td>
                    <td width="1">Rp. </td>
                    <td width="30">{{ number_format($bungaRupiah, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            @php
                // Total nominal
                $totalNominal = $records->sum('nominal');

                // Total bunga
                $totalBunga = 0;
                foreach($records as $r) {
                    $bulanmasuk = \Carbon\Carbon::parse($r->tanggal_masuk)->format('m-Y');
                    $bulanskrg = date('m-Y');
                    if($bulanmasuk == $bulanskrg){
                        $bungaRupiah = 0;
                    }else{
                        $bungaRupiah = $r->nominal * ($r->bunga_persen / 12 / 100);
                    }
                    $totalBunga += $bungaRupiah;
                }
            @endphp

            <tr class="footer" style="font-size:7pt;">
                <td colspan="5">Grand Total</td>
                <td>Rp. </td>
                <td>{{ number_format($totalNominal, 0, ',', '.') }}</td>
                <td>Rp. </td>
                <td>{{ number_format($totalBunga, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
