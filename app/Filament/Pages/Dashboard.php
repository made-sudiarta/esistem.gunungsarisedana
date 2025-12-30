<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\PinjamanHarianChart;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    // GRID DASHBOARD
    public function getWidgetsColumns(): int
    {
        return 12;
    }
}
