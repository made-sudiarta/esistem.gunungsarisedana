<?php

namespace App\Filament\Resources\TransaksiKreditBulananResource\Widgets;

use App\Filament\Resources\TransaksiKreditBulananResource\Pages\ListTransaksiKreditBulanans;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransaksiKreditBulananStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected int | string | array $columnSpan = 'full';

    protected function getTablePage(): string
    {
        return ListTransaksiKreditBulanans::class;
    }

    protected function getStats(): array
{
    $query = $this->getPageTableQuery();

    $totalPokok = (clone $query)->sum('pokok');
    $totalBunga = (clone $query)->sum('bunga');
    $totalDenda = (clone $query)->sum('denda');
    $totalSetoran = $totalPokok + $totalBunga + $totalDenda;

    return [
        Stat::make(
            'Setoran Pokok',
            'Rp ' . number_format($totalPokok, 0, ',', '.')
        )
            ->description('Mengikuti filter tabel')
            ->icon('heroicon-o-banknotes')
            ->color('primary'),

        Stat::make(
            'Setoran Bunga',
            'Rp ' . number_format($totalBunga, 0, ',', '.')
        )
            ->description('Mengikuti filter tabel')
            ->icon('heroicon-o-chart-bar')
            ->color('success'),

        Stat::make(
            'Setoran Denda',
            'Rp ' . number_format($totalDenda, 0, ',', '.')
        )
            ->description('Mengikuti filter tabel')
            ->icon('heroicon-o-exclamation-triangle')
            ->color('danger'),

        Stat::make(
            'Total Setoran',
            'Rp ' . number_format($totalSetoran, 0, ',', '.')
        )
            ->description('Pokok + bunga + denda')
            ->icon('heroicon-o-currency-dollar')
            ->color('warning'),
    ];
}
}