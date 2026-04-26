<?php

namespace App\Filament\Resources\AbsensiResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class AbsensiKolektorStats extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Kolektor') ?? false;
    }
    protected function getStats(): array
    {
        $today = today();

        $setoranBulanIni = DB::table('absensis')
            ->whereNull('deleted_at')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('jumlah_setoran');

        $penarikanBulanIni = DB::table('absensis')
            ->whereNull('deleted_at')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('penarikan');

        return [

            Stat::make('Setoran Hari Ini', 'Rp ' . number_format($setoranBulanIni, 0, ',', '.'))
                ->description('Jumlah setoran hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Penarikan Hari Ini', 'Rp ' . number_format($penarikanBulanIni, 0, ',', '.'))
                ->description('Jumlah penarikan hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}