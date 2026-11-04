<?php

namespace App\Http\Controllers;

use App\Models\KreditHarianTransaksi;
use Barryvdh\DomPDF\Facade\Pdf;

class KreditHarianKuitansiController extends Controller
{
    public function cetak($id)
    {
        $transaksi = KreditHarianTransaksi::with([
            'kreditHarian.member',
            'user',
        ])->findOrFail($id);

        $pdf = Pdf::loadView(
            'pdf.kuitansi-kredit-harian',
            compact('transaksi')
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Kuitansi-Kredit-Harian-' . $transaksi->id . '.pdf'
        );
    }
}