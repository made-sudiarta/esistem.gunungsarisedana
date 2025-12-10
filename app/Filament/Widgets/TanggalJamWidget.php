<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class TanggalJamWidget extends Widget
{

    protected static string $view = 'filament.widgets.tanggal-jam-widget';

    // Full width
    // protected static ?int $columnSpan = 12; // 12 kolom = full width
    // protected ?int $columnSpan = 12; // full width
    // protected int|string|array $columnSpan = 12;

    /**
     * Pilihan ukuran: small, medium, large
     */
    public $size = 'medium'; // default medium

    /**
     * Property untuk jam WIB
     */
    public function getTimeProperty()
    {
        return now()->setTimezone('Asia/Shanghai')->format('l, d M Y H:i:s');
    }

    /**
     * Column span agar full width
     */
    // protected static ?int $columnSpan = 'full';
}
