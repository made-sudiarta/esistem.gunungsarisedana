<?php

namespace App\Filament\Resources\KreditHarianTransaksiResource\Pages;

use App\Filament\Resources\KreditHarianTransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKreditHarianTransaksis extends ListRecords
{
    protected static string $resource = KreditHarianTransaksiResource::class;

    
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Transaksi Baru')->icon('heroicon-o-plus'),
        ];
    }
}
