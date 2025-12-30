<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class TanggalJamWidget extends Widget
{
    protected static string $view = 'filament.widgets.tanggal-jam-widget';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 12;

    public function getTimeProperty(): string
    {
        return now()
            ->setTimezone('Asia/Makassar')
            ->translatedFormat('H:i:s');
    }
}
