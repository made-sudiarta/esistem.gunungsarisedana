<?php

namespace App\Filament\Resources\SimpananWajibResource\Pages;

use App\Filament\Resources\SimpananWajibResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimpananWajib extends EditRecord
{
    protected static string $resource = SimpananWajibResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
