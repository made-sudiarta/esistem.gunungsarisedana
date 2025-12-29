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
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $plafond = $data['plafond'];
        $bunga = $data['bunga_persen'];
        $admin = $data['admin_persen'];

        $data['sisa_pokok'] =
            $plafond + ($plafond * ($bunga + $admin) / 100);

        return $data;
    }

}
