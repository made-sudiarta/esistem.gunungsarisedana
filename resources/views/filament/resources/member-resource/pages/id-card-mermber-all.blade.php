<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>ID Card Anggota</title>
  <style>
    @page {
      size: B5 landscape;
      margin: 0;
    }

    body {
      font-family: sans-serif;
      font-size: 10pt;
      margin: 0;
      padding: 0;
    }

    .card {
      width: 250mm;
      height: 176mm;
      padding: 24px;
      box-sizing: border-box;
      position: relative;
      background-color: white;
      overflow: hidden;
      page-break-after: always;
    }

    .card::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 100%;
      height: 100%;
      background: url('{{ asset('images/logo-koperasi.png') }}') no-repeat center center;
      background-size: 60%;
      opacity: 0.05;
      transform: translate(-50%, -50%);
      z-index: 0;
    }

    .card > * {
      position: relative;
      z-index: 1;
    }

    .row {
      display: flex;
      margin-bottom: 16px;
    }

    .left {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    img.foto {
      width: 160px;
      height: 160px;
      object-fit: cover;
      border-radius: 4px;
    }

    .right {
      flex: 2;
    }

    .right table {
      text-align: left;
      vertical-align: baseline;
      width: 100%;
    }

    .right table td {
      vertical-align: top;
    }

    ul {
      margin-top: 0;
      padding-left: 18px;
    }

    .signature {
      margin-top: 40px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
    }

    .signature-left {
      width: 60%;
    }

    .signature-right {
      width: 40%;
      text-align: center;
    }

    .signature-right img {
      width: 100px;
      display: block;
      margin: 10px auto;
    }

    .bold {
      font-weight: bold;
    }

    @media print {
      body {
        margin: 0;
      }

      .card {
        page-break-inside: avoid;
      }
    }
  </style>
</head>
<body onload="window.print()">
  @foreach ($records as $record)
    <div class="card">
      <div class="row">
        <div class="left">
          <img src="{{ $record->photo ? asset('storage/' . $record->photo) : asset('images/user-placeholder.png') }}" class="foto">
        </div>
        <div class="right">
          <table>
            <tr>
              <th width="100">NO. INDUK</th>
              <th>:</th>
              <td>{{ $record->no_induk }}</td>
            </tr>
            <tr>
              <th>IDENTITAS</th>
              <th>:</th>
              <td>{{ $record->identitas ?? '-' }}</td>
            </tr>
            <tr>
              <th>NAMA</th>
              <th>:</th>
              <td>{{ strtoupper($record->nama) }}</td>
            </tr>
            <tr>
              <th>ALAMAT</th>
              <th>:</th>
              <td>
                {{ strtoupper($record->alamat) }}<br>
                {{ strtoupper($record->kelurahan ?? '') }}, {{ strtoupper($record->kecamatan ?? '') }}
              </td>
            </tr>
          </table>
        </div>
      </div>

      <div class="signature">
        <div class="signature-left">
          <p class="bold">Catatan :</p>
          <ul>
            <li>Simpanan Pokok : Rp. 100.000,-</li>
            <li>Memakai Jasa/Layanan Koperasi</li>
            <li>Taat dan patuh kepada Anggaran Dasar, Anggaran Rumah Tangga & Peraturan Khusus</li>
          </ul>
        </div>
        <div class="signature-right">
          <p>Denpasar, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
          <p>Disahkan oleh,<br>Ketua</p>
          <img src="{{ asset('images/ttd-ketua.png') }}" alt="Tanda Tangan Ketua">
          <p class="bold">I Made Sudiarta, S.Kom</p>
        </div>
      </div>
    </div>
  @endforeach
</body>

</html>
