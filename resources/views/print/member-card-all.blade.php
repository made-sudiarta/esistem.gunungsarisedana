<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak ID Card</title>
  <style>
    html, body {
        width: 200mm;
        height: 300mm;
    }

    @page {
      size: A4 portrait;
      margin: 10mm;
      
    }

    body {
      font-family: sans-serif;
      font-size: 6pt;
      margin: 0;
      padding: 0;
    }

    .page {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      grid-template-rows: repeat(5, auto);
      gap: 2mm;
      justify-items: center;
      align-items: center;
      page-break-after: always;
      padding: 0mm;
      box-sizing: border-box;
    }

    .card {
      width: 95.6mm;
      height: 60mm;
      border: 0;
      box-sizing: border-box;
      position: relative;
      overflow: hidden;
    }

    .card-front {
      background: url('{{ asset('images/cover-koperasi.png') }}') no-repeat center center;
      background-size: cover;
      /* padding:0; */
    }

    .card-back {
      background-color: white;
      padding: 3mm;
    }

    .card-back::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 60%;
      height: 60%;
      background: url('{{ asset('images/logo-koperasi.png') }}') no-repeat center center;
      background-size: contain;
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
      gap: 5mm;
    }

    .left {
      width: 25mm;
    }

    .left img {
      width: 100%;
      height: 30mm;
      object-fit: cover;
      border-radius: 2px;
    }

    .right {
      flex: 1;
      font-size: 6pt;
    }

    .right table {
      width: 100%;
    }

    .right td {
      vertical-align: top;
    }

    .signature {
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      margin-top: -5mm;
      font-size: 6pt;
    }

    .bold {
      font-weight: bold;
    }

    .signature img {
      height: 25px;
      margin: 2px auto;
      display: block;
    }
  </style>
</head>
<body>

@foreach ($records->chunk(10) as $chunk)
  {{-- Halaman Depan --}}
  <div class="page" style="margin-left:5px;">
    @foreach ($chunk as $record)
      <div class="card card-front"></div>
    @endforeach
  </div>

  {{-- Halaman Belakang --}}
  <div class="page">
    @foreach ($chunk as $record)
      <div class="card card-back">
        <div class="row">
          <div class="left">
            <img src="{{ $record->photo ? asset('storage/' . $record->photo) : asset('images/user-placeholder.jpg') }}">
          </div>
          <div class="right">
            <table style="font-weight:bold;">
              <tr>
                <td width="50">NO. INDUK</td><td>:</td><td>{{ str_pad($record->nia, 5, '0', STR_PAD_LEFT) }}/{{ $record->jenis->keterangan }}</td>
              </tr>
              <tr>
                <td>IDENTITAS</td><td>:</td><td>{{ $record->nik ?? '-' }}</td>
              </tr>
              <tr>
                <td>NAMA</td><td>:</td><td>{{ strtoupper($record->nama_lengkap) }}</td>
              </tr>
              <tr>
                <td>ALAMAT</td><td>:</td><td>{{ strtoupper($record->alamat) }}</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="signature">
          <div>
            <div class="bold">Catatan:</div>
            <ol style="margin: 0; padding-left: 10px; font-size:4.5pt;">
              <li style="padding-bottom:3px;">Simpanan Pokok: Rp. 100.000</li>
              <li style="padding-bottom:3px;">Memakai jasa/layanan koperasi</li>
              <li style="padding-bottom:3px;">Taat dan patuh kepada Anggaran Dasar, Anggaran Rumah<br>Tangga dan Peraturan Khusus</li>
            </ol>
          </div>
          <div style="text-align: center;">
            <p style="margin-bottom:-5px;">Denpasar, {{ now()->format('d F Y') }}</p>
            <p>Ketua</p>
            <img src="{{ asset('images/ttd-ketua.png') }}">
            <div class="bold">I Made Sudiarta, S.Kom</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endforeach

</body>
</html>
