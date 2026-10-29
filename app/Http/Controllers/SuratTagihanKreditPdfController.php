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
        $suratSp3 = \App\Models\SuratTagihanKredit::query()
            ->where('kredit_bulanan_id', $record->kredit_bulanan_id)
            ->where('jenis_sp', 'SP3')
            ->orderByDesc('tanggal_surat')
            ->first();

        $nomorSp3 = $suratSp3?->nomor_surat;
        $tanggalSp3 = $suratSp3?->tanggal_surat;

        if ($record->status_surat !== 'cetak') {
            $record->update([
                'status_surat' => 'cetak',
            ]);
        }

        $filename = preg_replace('/[^A-Za-z0-9\-]/', '-', $record->nomor_surat);

        $pdf = Pdf::loadView('pdf.surat-tagihan-kredit', [
            'surat' => $record,
            'nomorSp3' => $nomorSp3,
            'tanggalSp3' => $tanggalSp3,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream("surat-tagihan-{$filename}.pdf");
    }
}