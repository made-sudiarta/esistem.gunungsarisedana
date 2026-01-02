<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\PinjamanHarianChart;
use App\Filament\Widgets\AbsenMasukWidget;

class Dashboard extends BaseDashboard
{
   protected static ?string $title = 'Dashboard';

    // GRID DASHBOARD
    public function getWidgetsColumns(): int
    {
        return 12;
    }

    // Daftar semua widget di dashboard
    public function getWidgets(): array
    {
        return [
             AbsenMasukWidget::class,
        ];
    }

}
