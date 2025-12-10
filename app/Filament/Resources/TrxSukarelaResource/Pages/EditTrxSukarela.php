<?php

namespace App\Filament\Resources\TrxSukarelaResource\Pages;

use App\Filament\Resources\TrxSukarelaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrxSukarela extends EditRecord
{
    protected static string $resource = TrxSukarelaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
