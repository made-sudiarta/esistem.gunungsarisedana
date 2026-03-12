<?php

namespace App\Filament\Resources\SimpananPenyertaResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\TrxSimpananPenyerta;
use Carbon\Carbon;

class TransaksiPenyertaStats extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $query = TrxSimpananPenyerta::query()
            ->whereDate('tanggal_trx', Carbon::today());

        return [
            Stat::make(
                'Setoran Hari Ini',
                'Rp ' . number_format($query->sum('kredit'), 0, ',', '.')
            )
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make(
                'Penarikan Hari Ini',
                'Rp ' . number_format($query->sum('debit'), 0, ',', '.')
            )
                ->icon('heroicon-o-arrow-up-circle')
                ->color('danger'),

            Stat::make(
                'Jumlah Transaksi',
                $query->count()
            )
                ->icon('heroicon-o-document-text')
                ->color('primary'),
        ];
    }
}