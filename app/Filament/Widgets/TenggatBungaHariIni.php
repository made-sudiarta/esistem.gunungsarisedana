<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\SimpananBerjangka;
use Carbon\Carbon;
use Filament\Facades\Filament;

class TenggatBungaHariIni extends Widget
{
    protected static string $view = 'filament.widgets.tenggat-bunga-hari-ini';


    protected int | string | array $columnSpan = [
        'default' => 12,
        'md' => 12,
        'xl' => 12,
    ];
    protected static ?int $sort = 5; 
    public static function canView(): bool
    {
        return Filament::auth()->user()?->hasRole('super_admin') ?? false;
    }

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
