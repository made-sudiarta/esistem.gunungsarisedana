<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
// use App\Filament\Widgets\JamHariIniWidget;
// use App\Filament\Widgets\AbsenMasukWidget;
// use App\Filament\Widgets\PinjamanHarianChart;
use App\Filament\Widgets\StatistikAnggotaWidget;
use App\Filament\Widgets\StatistikKreditWidget;
use App\Filament\Widgets\KreditTahunanChart;
use App\Filament\Widgets\AbsensiKolektorWidget;

use App\Filament\Widgets\KreditBulananTahunanChart;
use App\Filament\Widgets\KreditHarianTahunanChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    public function getColumns(): int | string | array
    {
        return [
            'default' => 1,
            'md' => 2,
            'lg' => 2,
            'xl' => 2,
        ];
    }

    public function getWidgets(): array
    {
        return [
            StatistikAnggotaWidget::class,
            // StatistikKreditWidget::class,
            // KreditTahunanChart::class,
            KreditBulananTahunanChart::class,
            KreditHarianTahunanChart::class,
            AbsensiKolektorWidget::class,
        ];
    }
}