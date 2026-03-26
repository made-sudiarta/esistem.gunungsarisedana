<?php

namespace App\Filament\Resources\KreditBulananResource\Pages;

use App\Filament\Resources\KreditBulananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKreditBulanans extends ListRecords
{
    protected static string $resource = KreditBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Pinjaman Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return KreditBulananResource::getWidgets();
    }
    
}