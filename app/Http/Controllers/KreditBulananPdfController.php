<?php

namespace App\Http\Controllers;

use App\Models\KreditBulanan;
use Barryvdh\DomPDF\Facade\Pdf;

class KreditBulananPdfController extends Controller
{
    public function akad(KreditBulanan $record)
    {
        $record->load([
            'member',
            'penanggungJawab',
            'jaminans.atasNamas',
            'jaminans.fidusia',
        ]);

        $pdf = Pdf::loadView('pdf.kredit-bulanan-akad', [
            'record' => $record,
        ])->setPaper('a4', 'portrait');

        $filename = 'akad-kredit-bulanan-' . $record->id . '.pdf';

        return $pdf->stream($filename);
        // kalau mau langsung download:
        // return $pdf->download($filename);
    }
}