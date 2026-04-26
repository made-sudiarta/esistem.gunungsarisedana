<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class KreditTahunanChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Kredit Tahunan';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    protected function getData(): array
    {
        $tahun = now()->year;

        return [
            'datasets' => [
                [
                    'label' => 'Pengajuan Kredit Bulanan',
                    'data' => $this->getPengajuanPerBulan('kredit_bulanans', $tahun),
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Lunas Kredit Bulanan',
                    'data' => $this->getLunasPerBulan('kredit_bulanans', $tahun),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pengajuan Kredit Harian',
                    'data' => $this->getPengajuanPerBulan('kredit_harian', $tahun),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Lunas Kredit Harian',
                    'data' => $this->getLunasPerBulan('kredit_harian', $tahun),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.15)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'Mei',
                'Jun',
                'Jul',
                'Agu',
                'Sep',
                'Okt',
                'Nov',
                'Des',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getPengajuanPerBulan(string $table, int $tahun): array
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

    private function getLunasPerBulan(string $table, int $tahun): array
    {
        return collect(range(1, 12))
            ->map(function (int $bulan) use ($table, $tahun) {
                $query = DB::table($table)
                    ->whereYear('created_at', $tahun)
                    ->whereMonth('created_at', $bulan);

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
            })
            ->toArray();
    }
}