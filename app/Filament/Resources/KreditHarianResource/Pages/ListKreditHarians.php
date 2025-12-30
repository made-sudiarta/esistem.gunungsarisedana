<?php

namespace App\Filament\Resources\KreditHarianResource\Pages;

use App\Filament\Resources\KreditHarianResource;
use App\Filament\Resources\KreditHarianResource\Widgets\KreditHarianStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKreditHarians extends ListRecords
{
    protected static string $resource = KreditHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Pinjaman Baru')->icon('heroicon-o-plus'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            KreditHarianStats::class,
        ];
    }
}
