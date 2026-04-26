<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KreditBulananTahunanChart extends ChartWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }
    protected static ?string $heading = 'Pinjaman Bulanan';

    protected static ?int $sort = 2;

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
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pinjaman Lunas',
                    'data' => $this->getPinjamanLunasPerBulan($tahun),
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.15)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pinjaman Jatuh Tempo',
                    'data' => $this->getJatuhTempoPerBulan($tahun),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.15)',
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
            ->map(fn (int $bulan) => (float) DB::table('kredit_bulanans')
                ->whereYear('tanggal_pengajuan', $tahun)
                ->whereMonth('tanggal_pengajuan', $bulan)
                ->count()
            )
            ->toArray();
    }

    private function getPinjamanLunasPerBulan(int $tahun): array
    {
        return collect(range(1, 12))
            ->map(fn (int $bulan) => (float) DB::table('transaksi_kredit_bulanans')
                ->whereYear('tanggal_transaksi', $tahun)
                ->whereMonth('tanggal_transaksi', $bulan)
                ->where('sisa_saldo', 0)
                ->count()
            )
            ->toArray();
    }

    private function getJatuhTempoPerBulan(int $tahun): array
    {
        return collect(range(1, 12))
            ->map(fn (int $bulan) => (float) DB::table('kredit_bulanans')
                ->whereYear('tanggal_jatuh_tempo', $tahun)
                ->whereMonth('tanggal_jatuh_tempo', $bulan)
                ->whereDate('tanggal_jatuh_tempo', '<=', today())
                ->where('status', '!=', 'lunas')
                ->count()
            )
            ->toArray();
    }
}