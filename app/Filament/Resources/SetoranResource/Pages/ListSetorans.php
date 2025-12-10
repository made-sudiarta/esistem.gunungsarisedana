<?php

namespace App\Filament\Resources\SetoranResource\Pages;

use App\Filament\Resources\SetoranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSetorans extends ListRecords
{
    protected static string $resource = SetoranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
