<?php

namespace App\Filament\Resources\KreditHarianResource\Pages;

use App\Filament\Resources\KreditHarianResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKreditHarian extends CreateRecord
{
    protected static string $resource = KreditHarianResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $plafond = $data['plafond'];
        $bunga = $data['bunga_persen'];
        $admin = $data['admin_persen'];

        $data['sisa_pokok'] =
            $plafond + ($plafond * ($bunga + $admin) / 100);

        return $data;
    }
}
