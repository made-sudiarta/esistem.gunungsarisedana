<?php

namespace App\Filament\Widgets;

use App\Models\KreditHarian;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class PinjamanHarianChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pinjaman Harian';

    protected int | string | array $columnSpan = [
        'default' => 12,
        'md' => 6,
        'xl' => 6,
    ];

    protected static ?int $sort = 2;


    protected function getHeight(): string
    {
        return '300px';
    }

    protected function getData(): array
    {
        $data = KreditHarian::query()
            ->selectRaw('MONTH(tanggal_pengajuan) as bulan, SUM(plafond) as total')
            ->whereYear('tanggal_pengajuan', now()->year)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Pinjaman Harian',
                    'data' => $data->pluck('total')->toArray(),
                    'borderWidth' => 1,
                    'tension' => 0.2, // biar garis halus
                ],
            ],
            'labels' => $data->map(fn ($row) =>
                Carbon::create()->month($row->bulan)->translatedFormat('F')
            )->toArray(),
        ];
    }
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
    

    protected function getType(): string
    {
        return 'line';
    }
}

