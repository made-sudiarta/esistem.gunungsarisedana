<?php

namespace App\Filament\Resources\AbsensiResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AbsensiKolektorStats extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Kolektor') ?? false;
    }

    protected function getStats(): array
    {
        $userId = Auth::id();
        $today = today()->toDateString();

        $setoranHariIni = DB::table('absensis')
            ->whereNull('deleted_at')
            ->where('user_id', $userId)
            ->whereDate('tanggal', $today)
            ->sum('jumlah_setoran');

        $penarikanHariIni = DB::table('absensis')
            ->whereNull('deleted_at')
            ->where('user_id', $userId)
            ->whereDate('tanggal', $today)
            ->sum('penarikan');

        $setoranBulanIni = DB::table('absensis')
            ->whereNull('deleted_at')
            ->where('user_id', $userId)
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('jumlah_setoran');

        $penarikanBulanIni = DB::table('absensis')
            ->whereNull('deleted_at')
            ->where('user_id', $userId)
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->sum('penarikan');

        return [
            Stat::make('Setoran Hari Ini', 'Rp ' . number_format($setoranHariIni ?? 0, 0, ',', '.'))
                ->description('Jumlah setoran hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Penarikan Hari Ini', 'Rp ' . number_format($penarikanHariIni ?? 0, 0, ',', '.'))
                ->description('Jumlah penarikan hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Setoran Bulan Ini', 'Rp ' . number_format($setoranBulanIni ?? 0, 0, ',', '.'))
                ->description('Jumlah setoran bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Penarikan Bulan Ini', 'Rp ' . number_format($penarikanBulanIni ?? 0, 0, ',', '.'))
                ->description('Jumlah penarikan bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('warning'),
        ];
    }
}