<?php

namespace App\Filament\Resources\SimpananBerjangkaResource\Widgets;

use App\Models\SimpananBerjangka;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

class SimpananBerjangkaOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $user = Filament::auth()->user();

        // 🔐 QUERY DASAR (FILTER ROLE)
        $baseQuery = SimpananBerjangka::query();

        if (! $user->hasRole('super_admin')) {
            $baseQuery->whereHas('group', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // 1️⃣ JATUH TEMPO BULAN INI
        $jatuhTempo = (clone $baseQuery)
            ->whereRaw("
                MONTH(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
                AND YEAR(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
            ", [$today->month, $today->year])
            ->count();

        // 2️⃣ BUNGA HARI INI
        $tenggatBunga = (clone $baseQuery)
            ->whereRaw("
                DAY(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
            ", [$today->day])
            ->get()
            ->sum(fn ($item) =>
                $item->nominal * ($item->bunga_persen / 100) / 12
            );

        // 3️⃣ TOTAL NOMINAL
        $nominal = (clone $baseQuery)->sum('nominal');

        return [
            Stat::make('Bilyet Jatuh Tempo', $jatuhTempo)
                ->description('Klik untuk melihat data jatuh tempo')
                ->url('/admin/simpanan-berjangkas?status=jatuh_tempo')
                ->color('danger')
                ->icon('heroicon-o-clock'),

            Stat::make('Cetak Bunga Hari Ini', 'Rp. ' . number_format($tenggatBunga))
                ->description('Klik untuk melihat bunga simpanan')
                ->url('/admin/simpanan-berjangkas?status=tenggat_bunga')
                ->color('warning')
                ->icon('heroicon-o-banknotes'),

            Stat::make('Jumlah Simpanan Berjangka', 'Rp. ' . number_format($nominal))
                ->description('Klik untuk lihat semua data')
                ->url('/admin/simpanan-berjangkas')
                ->color('success')
                ->icon('heroicon-o-document-text'),
        ];
    }
}
