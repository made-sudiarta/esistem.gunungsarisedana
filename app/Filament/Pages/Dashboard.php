<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\TanggalJamWidget;
use App\Filament\Widgets\TenggatBungaHariIni;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    // PUBLIC, bukan protected
    public function getWidgets(): array
    {
        return [
            TanggalJamWidget::class,       // urutan 1
            TenggatBungaHariIni::class,    // urutan 2
        ];
    }
}
