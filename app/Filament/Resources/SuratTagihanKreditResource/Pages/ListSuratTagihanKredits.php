<?php

namespace App\Filament\Resources\SuratTagihanKreditResource\Pages;

use App\Filament\Resources\SuratTagihanKreditResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratTagihanKredits extends ListRecords
{
    protected static string $resource = SuratTagihanKreditResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Surat Baru')
                ->icon('heroicon-o-plus'),
        ];
    }
}