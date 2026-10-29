<?php

namespace App\Filament\Resources\SuratPenyerahanJaminanResource\Pages;

use App\Filament\Resources\SuratPenyerahanJaminanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratPenyerahanJaminans extends ListRecords
{
    protected static string $resource = SuratPenyerahanJaminanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
