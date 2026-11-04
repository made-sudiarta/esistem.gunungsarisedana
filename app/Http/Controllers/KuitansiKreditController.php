<?php

namespace App\Http\Controllers;

use App\Models\TransaksiKreditBulanan;
use Barryvdh\DomPDF\Facade\Pdf;

class KuitansiKreditController extends Controller
{
    public function cetak(TransaksiKreditBulanan $transaksi)
    {
        $transaksi->load([
            'kreditBulanan',
            'user',
        ]);

        $pdf = Pdf::loadView(
            'pdf.kuitansi-kredit-bulanan',
            compact('transaksi')
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Kuitansi-Kredit-' . $transaksi->id . '.pdf'
        );
    }
}