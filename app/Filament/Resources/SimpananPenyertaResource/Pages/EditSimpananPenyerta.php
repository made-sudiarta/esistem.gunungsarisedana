<?php

namespace App\Filament\Resources\SimpananPenyertaResource\Pages;

use App\Filament\Resources\SimpananPenyertaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimpananPenyerta extends EditRecord
{
    protected static string $resource = SimpananPenyertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
