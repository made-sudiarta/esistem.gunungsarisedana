<?php

namespace App\Filament\Resources\KreditHarianTransaksiResource\Pages;

use App\Filament\Resources\KreditHarianTransaksiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKreditHarianTransaksi extends EditRecord
{
    protected static string $resource = KreditHarianTransaksiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
