<?php

namespace App\Http\Controllers;

use App\Models\TrxSimpananPokok;
use Barryvdh\DomPDF\Facade\Pdf;

class SimpananPokokKuitansiController extends Controller
{
    public function cetak($id)
    {
        $transaksi = TrxSimpananPokok::with([
            'members',
            'user',
        ])->findOrFail($id);

        // Ambil transaksi anggota yang sama sebelum transaksi ini.
        $transaksiSebelumnya = TrxSimpananPokok::query()
            ->where('member_id', $transaksi->member_id)
            ->where('id', '<', $transaksi->id);

        $totalSetoranSebelumnya = (clone $transaksiSebelumnya)
            ->sum('kredit');

        $totalPenarikanSebelumnya = (clone $transaksiSebelumnya)
            ->sum('debit');

        // Saldo sebelum transaksi.
        $saldoAwal = (float) $totalSetoranSebelumnya
            - (float) $totalPenarikanSebelumnya;

        $setoran = (float) ($transaksi->kredit ?? 0);
        $penarikan = (float) ($transaksi->debit ?? 0);

        // Saldo setelah transaksi.
        $saldoAkhir = $saldoAwal + $setoran - $penarikan;

        $pdf = Pdf::loadView(
            'pdf.kuitansi-simpanan-pokok',
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
            'Kuitansi-Simpanan-Pokok-' . $transaksi->id . '.pdf'
        );
    }
}