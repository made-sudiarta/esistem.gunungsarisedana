<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\SimpananBerjangka;
use Carbon\Carbon;

class TenggatBungaHariIni extends Widget
{
    protected static string $view = 'filament.widgets.tenggat-bunga-hari-ini';

    public $records;
    public $total;

    public function mount(): void
    {
        $today = Carbon::today();

        $this->records = SimpananBerjangka::get()->filter(function ($item) use ($today) {
            $jatuh = Carbon::parse($item->tanggal_masuk)
                ->addMonths((int) $item->jangka_waktu);

            return $jatuh->day === $today->day;
        });

        $this->total = $this->records->count();
    }
}
