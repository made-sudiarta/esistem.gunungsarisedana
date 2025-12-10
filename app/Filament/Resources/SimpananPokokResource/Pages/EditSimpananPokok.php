<?php

namespace App\Filament\Resources\SimpananPokokResource\Pages;

use App\Filament\Resources\SimpananPokokResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimpananPokok extends EditRecord
{
    protected static string $resource = SimpananPokokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
