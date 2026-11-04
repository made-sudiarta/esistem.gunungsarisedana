<?php

namespace App\Http\Controllers;


use App\Models\TrxSimpananPenyerta;
use Barryvdh\DomPDF\Facade\Pdf;

class SimpananPenyertaKuitansiController extends Controller
{
    public function cetak($id)
    {
        $transaksi = TrxSimpananPenyerta::with([
            'members',
            'user',
        ])->findOrFail($id);

        // Hitung seluruh saldo anggota sebelum transaksi ini.
        $transaksiSebelumnya = TrxSimpananPenyerta::query()
            ->where('member_id', $transaksi->member_id)
            ->where('id', '<', $transaksi->id);

        $totalSetoranSebelumnya = (clone $transaksiSebelumnya)
            ->sum('kredit');

        $totalPenarikanSebelumnya = (clone $transaksiSebelumnya)
            ->sum('debit');

        $saldoAwal = (float) $totalSetoranSebelumnya
            - (float) $totalPenarikanSebelumnya;

        $setoran = (float) ($transaksi->kredit ?? 0);
        $penarikan = (float) ($transaksi->debit ?? 0);

        $saldoAkhir = $saldoAwal + $setoran - $penarikan;

        $pdf = Pdf::loadView(
            'pdf.kuitansi-simpanan-penyerta',
            compact(
                'transaksi',
                'saldoAwal',
                'saldoAkhir',
                'setoran',
                'penarikan'
            )
        );

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream(
            'Kuitansi-Simpanan-Penyerta-' . $transaksi->id . '.pdf'
        );
    }
}