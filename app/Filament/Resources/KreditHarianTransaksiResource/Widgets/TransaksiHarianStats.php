<?php

namespace App\Filament\Resources\KreditHarianTransaksiResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\KreditHarianTransaksi;
use Carbon\Carbon;

class TransaksiHarianStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $query = KreditHarianTransaksi::query()
            ->whereDate('tanggal_transaksi', Carbon::today());

        return [
            Stat::make(
                'Setoran Hari Ini',
                'Rp ' . number_format($query->sum('jumlah'), 0, ',', '.')
            )
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make(
                'Peminjam',
                $query->distinct('kredit_harian_id')->count('kredit_harian_id')
            )
                ->icon('heroicon-o-users')
                ->color('primary'),
        ];
    }
}
