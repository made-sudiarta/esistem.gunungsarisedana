<?php

namespace App\Filament\Resources\KreditBulananResource\Widgets;

use App\Models\KreditBulanan;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class KreditBulananStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = Filament::auth()->user();
        $today = Carbon::today();

        $query = KreditBulanan::query();

        if (! $user->hasRole('super_admin')) {
            $query->whereHas('group', function (Builder $q) use ($user) {
                $q->where('employee_id', $user->employee_id ?? $user->id);
            });
        }

        $records = (clone $query)->get();

        $totalPinjaman = $records->count();

        $totalSisaSaldo = $records->sum(function ($record) {
            return (float) $record->getSisaSaldo();
        });

        $jatuhTempoRecords = $records->filter(function ($record) use ($today) {
            return $record->getSisaSaldo() > 0
                && ! empty($record->tanggal_jatuh_tempo)
                && Carbon::parse($record->tanggal_jatuh_tempo)->lt($today);
        });

        $totalJatuhTempo = $jatuhTempoRecords->sum(function ($record) {
            return (float) $record->getSisaSaldo();
        });
        
        $totalPinjamanTahunIni = KreditBulanan::whereYear('tanggal_pengajuan', Carbon::now()->year)->sum('plafond');
        $totalJatuhTempoTahunIni = KreditBulanan::whereYear('tanggal_jatuh_tempo', Carbon::now()->year)->sum('plafond');
        return [
            Stat::make(
                'Pinjaman Jatuh Tempo',
                $jatuhTempoRecords->count() . ' dari ' . $totalPinjaman
            )
                ->description('Jumlah pinjaman jatuh tempo')
                ->icon('heroicon-o-currency-dollar')
                ->color('danger'),
            
            Stat::make(
                'Total Pinjaman Berjalan',
                'Rp ' . number_format($totalSisaSaldo, 0, ',', '.')
            )
                ->description('Pinjaman tahun ' . Carbon::now()->year . ': Rp ' . number_format($totalPinjamanTahunIni, 0, ',', '.'))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),


            Stat::make(

                'Total Jatuh Tempo',

                'Rp ' . number_format($totalJatuhTempo, 0, ',', '.')

            )

                ->description('Jatuh tempo tahun ' . Carbon::now()->year . ': Rp ' . number_format($totalJatuhTempoTahunIni, 0, ',', '.'))

                ->icon('heroicon-o-currency-dollar')

                ->color('warning'),

        ];
        
    }
}