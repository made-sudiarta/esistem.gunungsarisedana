<?php

namespace App\Http\Controllers;

use App\Models\SuratTagihanKredit;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratTagihanKreditPdfController extends Controller
{
    public function show(SuratTagihanKredit $record)
    {
        $record->load([
            'kreditBulanan.member',
            'kreditBulanan.group',
        ]);

        if ($record->status_surat !== 'cetak') {
            $record->update([
                'status_surat' => 'cetak',
            ]);
        }

        $filename = preg_replace('/[^A-Za-z0-9\-]/', '-', $record->nomor_surat);

        $pdf = Pdf::loadView('pdf.surat-tagihan-kredit', [
            'surat' => $record,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("surat-tagihan-{$filename}.pdf");
    }
}