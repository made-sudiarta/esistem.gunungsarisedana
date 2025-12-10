<?php

namespace App\Filament\Resources\SetoranSukarelaResource\Pages;

use App\Filament\Resources\SetoranSukarelaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetoranSukarela extends EditRecord
{
    protected static string $resource = SetoranSukarelaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
