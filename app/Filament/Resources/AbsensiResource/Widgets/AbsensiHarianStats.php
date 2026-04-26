<?php

namespace App\Filament\Resources\AbsensiResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class AbsensiHarianStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = today();

        $jumlahAbsenMasukHariIni = DB::table('absensis')
            ->where('tanggal', $today)
            ->whereNotNull('jam_masuk')
            ->whereNull('deleted_at')
            ->count();

        $jumlahAbsenKeluarHariIni = DB::table('absensis')
            ->whereDate('tanggal', $today)
            ->whereNull('deleted_at')
            ->whereNotNull('jam_keluar')
            ->count();

        $setoranHariIni = DB::table('absensis')
            ->whereNull('deleted_at')
            ->whereDate('tanggal', $today)
            ->sum('jumlah_setoran');

        $penarikanHariIni = DB::table('absensis')
            ->whereNull('deleted_at')
            ->whereDate('tanggal', $today)
            ->sum('penarikan');

        return [
            Stat::make('Hadir Kerja', $jumlahAbsenMasukHariIni. ' Karyawan')
                ->description('Karyawan yang hadir kerja')
                ->descriptionIcon('heroicon-m-arrow-right-on-rectangle')
                ->color('success'),

            Stat::make('Pulang Kerja', $jumlahAbsenKeluarHariIni. ' Karyawan')
                ->description('Karyawan yang pulang kerja')
                ->descriptionIcon('heroicon-m-arrow-left-on-rectangle')
                ->color('warning'),

            Stat::make('Setoran Hari Ini', 'Rp ' . number_format($setoranHariIni, 0, ',', '.'))
                ->description('Jumlah setoran hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Penarikan Hari Ini', 'Rp ' . number_format($penarikanHariIni, 0, ',', '.'))
                ->description('Jumlah penarikan hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
        ];
    }
}