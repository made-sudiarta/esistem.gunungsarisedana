<?php

namespace App\Http\Controllers;

use App\Models\SuratPenyerahanJaminan;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratPenyerahanJaminanPdfController extends Controller
{
    private function getSurat(SuratPenyerahanJaminan $surat): SuratPenyerahanJaminan
    {
        return $surat->load([
            'kreditBulanan.member',
        ]);
    }

    public function pernyataan(SuratPenyerahanJaminan $surat)
    {
        $surat = $this->getSurat($surat);

        $pdf = Pdf::loadView(
            'pdf.surat-penyerahan-jaminan-pernyataan',
            compact('surat')
        )->setPaper('a4', 'portrait');

        return $pdf->stream('surat-pernyataan-penyerahan-jaminan.pdf');
    }

    public function beritaAcara(SuratPenyerahanJaminan $surat)
    {
        $surat = $this->getSurat($surat);

        $pdf = Pdf::loadView(
            'pdf.surat-penyerahan-jaminan-berita-acara',
            compact('surat')
        )->setPaper('a4', 'portrait');

        return $pdf->stream('berita-acara-serah-terima-kendaraan.pdf');
    }
}