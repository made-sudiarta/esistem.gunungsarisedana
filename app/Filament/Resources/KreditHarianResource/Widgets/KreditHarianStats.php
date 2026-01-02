<?php

namespace App\Filament\Resources\KreditHarianResource\Widgets;

use App\Models\KreditHarian;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class KreditHarianStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    // protected function getStats(): array
    // {
    //     $totalPinjaman = KreditHarian::count();
    //     $totalSisaPokok = KreditHarian::sum('sisa_pokok');

    //     $jatuhTempo = KreditHarian::whereRaw(
    //         "DATE_ADD(tanggal_pengajuan, INTERVAL jangka_waktu DAY) < ?",
    //         [Carbon::today()]
    //     );

    //     return [
    //         Stat::make('Total Pinjaman', $totalPinjaman)
    //             ->description('Jumlah pinjaman aktif')
    //             ->descriptionIcon('heroicon-o-users')
    //             ->icon('heroicon-o-banknotes')
    //             ->color('primary'),

    //         Stat::make(
    //             'Total Sisa Pokok',
    //             'Rp ' . number_format($totalSisaPokok, 0, ',', '.')
    //         )
    //             ->description('Outstanding pinjaman')
    //             ->icon('heroicon-o-currency-dollar')
    //             ->color('success'),

    //         Stat::make('Pinjaman Jatuh Tempo', $jatuhTempo->count())
    //             ->description('Lewat jatuh tempo')
    //             ->icon('heroicon-o-exclamation-triangle')
    //             ->color('danger'),

    //         Stat::make(
    //             'Total Jatuh Tempo',
    //             'Rp ' . number_format($jatuhTempo->sum('sisa_pokok'), 0, ',', '.')
    //         )
    //             ->description('Outstanding jatuh tempo')
    //             ->icon('heroicon-o-clock')
    //             ->color('warning'),
    //     ];
    // }
    protected function getStats(): array
    {
        $user = Filament::auth()->user();
        $today = Carbon::today();

        // 🔥 BASE QUERY (FILTER GROUP USER)
        $query = KreditHarian::query();

        // Super Admin → semua data
        if (! $user->hasRole('super_admin')) {
            $query->whereHas('group', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // CLONE query agar tidak bentrok
        $totalPinjaman = (clone $query)->count();
        $totalSisaPokok = (clone $query)->sum('sisa_pokok');

        $jatuhTempoQuery = (clone $query)->whereRaw(
            "DATE_ADD(tanggal_pengajuan, INTERVAL jangka_waktu DAY) < ?",
            [$today]
        );

        return [
            Stat::make('Total Pinjaman', $totalPinjaman)
                ->description('Jumlah pinjaman aktif')
                ->icon('heroicon-o-banknotes')
                ->color('primary'),

            Stat::make(
                'Total Sisa Pokok',
                'Rp ' . number_format($totalSisaPokok, 0, ',', '.')
            )
                ->description('Outstanding pinjaman')
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),

            Stat::make('Pinjaman Jatuh Tempo', $jatuhTempoQuery->count())
                ->description('Lewat jatuh tempo')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),

            Stat::make(
                'Total Jatuh Tempo',
                'Rp ' . number_format($jatuhTempoQuery->sum('sisa_pokok'), 0, ',', '.')
            )
                ->description('Outstanding jatuh tempo')
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
