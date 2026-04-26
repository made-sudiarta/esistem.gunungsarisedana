<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KreditHarianTahunanChart extends ChartWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }
    protected static ?string $heading = 'Pinjaman Harian';

    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 1,
        'lg' => 1,
        'xl' => 1,
    ];

    protected function getData(): array
    {
        $tahun = now()->year;

        return [
            'datasets' => [
                [
                    'label' => 'Pinjaman Baru',
                    'data' => $this->getPinjamanBaruPerBulan($tahun),
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pinjaman Lunas',
                    'data' => $this->getPinjamanLunasPerBulan($tahun),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getPinjamanBaruPerBulan(int $tahun): array
    {
        return collect(range(1, 12))
            ->map(fn (int $bulan) => DB::table('kredit_harian')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->whereMonth('tanggal_pengajuan', $bulan)
                ->count()
            )
            ->toArray();
    }

    private function getPinjamanLunasPerBulan(int $tahun): array
    {
        return collect(range(1, 12))
            ->map(fn (int $bulan) => DB::table('kredit_harian_transaksis')
                ->join('kredit_harian', 'kredit_harian.id', '=', 'kredit_harian_transaksis.kredit_harian_id')
                ->whereYear('kredit_harian_transaksis.tanggal_transaksi', $tahun)
                ->whereMonth('kredit_harian_transaksis.tanggal_transaksi', $bulan)
                ->where('kredit_harian.sisa_pokok', 0)
                ->distinct('kredit_harian.id')
                ->count('kredit_harian.id')
            )
            ->toArray();
    }
}