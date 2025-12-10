<?php

namespace App\Observers;

use App\Models\TrxSukarela;

class TrxSukarelaObserver
{
    public function created(TrxSukarela $trx)
    {
        $sukarela = $trx->sukarela;
        // dd($sukarela);
        if ($sukarela) {
            $debit = $trx->debit ?? 0;
            $kredit = $trx->kredit ?? 0;

            $saldoBaru = $debit - $kredit;
            // dd($sukarela->increment('saldo', $saldoBaru));
            $sukarela->increment('saldo', $saldoBaru);
            $sukarela->refresh(); // ← ambil ulang data terbaru dari DB

            // dd($sukarela->toArray());

        } else {
            dd('Tidak ada data');
        }
    }



    public function updated(TrxSukarela $trx)
    {
        $original = $trx->getOriginal();

        $oldAmount = $original['debit'] - $original['kredit'];
        $newAmount = $trx->debit - $trx->kredit;

        $difference = $newAmount - $oldAmount;

        $trx->sukarela->increment('saldo', $difference);
    }

    public function deleted(TrxSukarela $trx)
    {
        $trx->sukarela->decrement('saldo', $trx->debit - $trx->kredit);
    }

    public function restored(TrxSukarela $trx)
    {
        $trx->sukarela->increment('saldo', $trx->debit - $trx->kredit);
    }

    public function forceDeleted(TrxSukarela $trx)
    {
        // No change if soft delete is used.
    }
}

