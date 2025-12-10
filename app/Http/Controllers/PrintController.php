<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function print(Member $member)
    {
        // Ambil transaksi
        $pokok = $member->trxSimpananPokoks()->select('tanggal_trx', 'debit', 'kredit', 'keterangan')->get()
            ->map(fn($item) => [
                'tanggal_trx' => $item->tanggal_trx,
                'pokok' => ($item->kredit ?? 0) - ($item->debit ?? 0),
                'penyerta' => 0,
                'wajib' => 0,
                'keterangan' => $item->keterangan,
            ]);

        $penyerta = $member->trxSimpananPenyertas()->select('tanggal_trx', 'debit', 'kredit', 'keterangan')->get()
            ->map(fn($item) => [
                'tanggal_trx' => $item->tanggal_trx,
                'pokok' => 0,
                'penyerta' => ($item->kredit ?? 0) - ($item->debit ?? 0),
                'wajib' => 0,
                'keterangan' => $item->keterangan,
            ]);

        $wajib = $member->trxSimpananWajibs()->select('tanggal_trx', 'debit', 'kredit', 'keterangan')->get()
            ->map(fn($item) => [
                'tanggal_trx' => $item->tanggal_trx,
                'pokok' => 0,
                'penyerta' => 0,
                'wajib' => ($item->kredit ?? 0) - ($item->debit ?? 0),
                'keterangan' => $item->keterangan,
            ]);

        $transactions = collect($pokok)->merge($penyerta)->merge($wajib)->sortBy('tanggal_trx')->values();
        $sukarelas = $member->sukarelas()->with('groups')->orderBy('tanggal_terdaftar')->get();


        return view('print.member', compact('member', 'transactions','sukarelas'));
    }
    public function printAll()
    {
        $records = Member::with(['jenis'])->orderBy('nia','ASC')->get(); // Sesuaikan relasi jika perlu
        return view('print.member-card-all', compact('records'));
    }
}
