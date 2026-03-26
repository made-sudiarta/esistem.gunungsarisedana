<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiKreditBulanan extends Model
{
    protected $table = 'transaksi_kredit_bulanans';

    protected $fillable = [
        'kredit_bulanan_id',
        'tanggal_transaksi',
        'saldo_awal',
        'pokok',
        'bunga',
        'nominal_bayar',
        'denda',
        'sisa_saldo',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'saldo_awal' => 'decimal:2',
        'pokok' => 'decimal:2',
        'bunga' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'denda' => 'decimal:2',
        'sisa_saldo' => 'decimal:2',
    ];

    public function kreditBulanan(): BelongsTo
    {
        return $this->belongsTo(KreditBulanan::class, 'kredit_bulanan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($transaksi) {
            $kredit = $transaksi->kreditBulanan;

            if ($kredit) {
                $saldoAwal = (float) $kredit->getSisaSaldo();
                $pokok = (float) ($transaksi->pokok ?? 0);
                $bunga = (float) ($transaksi->bunga ?? 0);
                $denda = (float) ($transaksi->denda ?? 0);

                $transaksi->saldo_awal = $saldoAwal;
                $transaksi->nominal_bayar = $pokok + $bunga + $denda;
                $transaksi->sisa_saldo = max($saldoAwal - $pokok, 0);
            }
        });

        static::created(function ($transaksi) {
            $transaksi->kreditBulanan?->refreshPerhitungan();
        });

        static::updated(function ($transaksi) {
            $transaksi->kreditBulanan?->refreshPerhitungan();
        });

        static::deleted(function ($transaksi) {
            $transaksi->kreditBulanan?->refreshPerhitungan();
        });
    }
}