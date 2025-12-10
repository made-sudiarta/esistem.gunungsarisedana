<?php

namespace App\Filament\Resources\SimpananPokokResource\Pages;

use App\Filament\Resources\SimpananPokokResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSimpananPokoks extends ListRecords
{
    protected static string $resource = SimpananPokokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
