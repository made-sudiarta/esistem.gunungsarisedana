<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class StatistikKreditWidget extends BaseWidget
{
    
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    protected function getStats(): array
    {
        $tahun = now()->year;

        $pengajuanBulanan = $this->countPengajuanTahunan('kredit_bulanans', $tahun);
        $lunasBulanan = $this->countLunasTahunan('kredit_bulanans', $tahun);

        $pengajuanHarian = $this->countPengajuanTahunan('kredit_harian', $tahun);
        $lunasHarian = $this->countLunasTahunan('kredit_harian', $tahun);

        return [
            Stat::make('Kredit Bulanan', number_format($pengajuanBulanan, 0, ',', '.'))
                ->description($this->descriptionKredit($lunasBulanan, $tahun))
                ->chart($this->getChartPengajuanPerBulan('kredit_bulanans', $tahun))
                ->color('primary')
                ->extraAttributes([
                    'class' => 'stat-kredit stat-kredit-primary',
                ]),

            Stat::make('Kredit Harian', number_format($pengajuanHarian, 0, ',', '.'))
                ->description($this->descriptionKredit($lunasHarian, $tahun))
                ->chart($this->getChartPengajuanPerBulan('kredit_harian', $tahun))
                ->color('success')
                ->extraAttributes([
                    'class' => 'stat-kredit stat-kredit-success',
                ]),
        ];
    }

    private function countPengajuanTahunan(string $table, int $tahun): int
    {
        return DB::table($table)
            ->whereYear('created_at', $tahun)
            ->count();
    }

    private function countLunasTahunan(string $table, int $tahun): int
    {
        $query = DB::table($table)
            ->whereYear('created_at', $tahun);

        if ($table === 'kredit_harian') {
            return $query
                ->where('sisa_pokok', 0)
                ->count();
        }

        if ($table === 'kredit_bulanans') {
            return $query
                ->where('status', 'lunas')
                ->count();
        }

        return 0;
    }

    private function getChartPengajuanPerBulan(string $table, int $tahun): array
    {
        return collect(range(1, 12))
            ->map(function (int $bulan) use ($table, $tahun) {
                return DB::table($table)
                    ->whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan)
                    ->count();
            })
            ->toArray();
    }

    private function descriptionKredit(int $lunas, int $tahun): HtmlString
    {
        return new HtmlString('
            <div class="flex flex-col gap-1">
                <span>Pengajuan tahun ' . $tahun . '</span>
                <span class="font-semibold">Lunas: ' . number_format($lunas, 0, ',', '.') . '</span>
            </div>
        ');
    }
}