<?php

namespace App\Filament\Resources\KreditHarianResource\Pages;

use App\Filament\Resources\KreditHarianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKreditHarian extends EditRecord
{
    protected static string $resource = KreditHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
