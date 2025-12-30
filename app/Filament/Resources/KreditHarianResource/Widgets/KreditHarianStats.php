<?php

namespace App\Filament\Resources\KreditHarianResource\Widgets;

use App\Models\KreditHarian;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KreditHarianStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalPinjaman = KreditHarian::count();
        $totalSisaPokok = KreditHarian::sum('sisa_pokok');

        $jatuhTempo = KreditHarian::whereRaw(
            "DATE_ADD(tanggal_pengajuan, INTERVAL jangka_waktu DAY) < ?",
            [Carbon::today()]
        );

        return [
            // Stat::make('Total Pinjaman', $totalPinjaman . ' Pinjaman'),
            // Stat::make('Total Sisa Pokok', 'Rp ' . number_format($totalSisaPokok, 0, ',', '.')),
            // Stat::make('Jatuh Tempo', $jatuhTempo->count() . ' Pinjaman')->color('danger'),
            // Stat::make(
            //     'Total Jatuh Tempo',
            //     'Rp ' . number_format($jatuhTempo->sum('sisa_pokok'), 0, ',', '.')
            // )->color('warning'),

            Stat::make('Total Pinjaman', $totalPinjaman)
                ->description('Jumlah pinjaman aktif')
                ->descriptionIcon('heroicon-o-users')
                ->icon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make(
                'Total Sisa Pokok',
                'Rp ' . number_format($totalSisaPokok, 0, ',', '.')
            )
                ->description('Outstanding pinjaman')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Pinjaman Jatuh Tempo', $jatuhTempo->count())
                ->description('Lewat jatuh tempo')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make(
                'Total Jatuh Tempo',
                'Rp ' . number_format($jatuhTempo->sum('sisa_pokok'), 0, ',', '.')
            )
                ->description('Outstanding jatuh tempo')
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
