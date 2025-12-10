<?php

namespace App\Filament\Resources\SimpananBerjangkaResource\Pages;

use App\Filament\Resources\SimpananBerjangkaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSimpananBerjangka extends EditRecord
{
    protected static string $resource = SimpananBerjangkaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
