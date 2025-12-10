<?php

namespace App\Filament\Resources\SukarelaResource\Pages;

use App\Filament\Resources\SukarelaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSukarela extends EditRecord
{
    protected static string $resource = SukarelaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
