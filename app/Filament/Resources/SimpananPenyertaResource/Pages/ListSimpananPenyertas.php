<?php

namespace App\Filament\Resources\SimpananPenyertaResource\Pages;

use App\Filament\Resources\SimpananPenyertaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSimpananPenyertas extends ListRecords
{
    protected static string $resource = SimpananPenyertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
