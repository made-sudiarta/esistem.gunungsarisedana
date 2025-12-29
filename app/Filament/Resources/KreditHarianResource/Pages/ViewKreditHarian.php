<?php

namespace App\Filament\Resources\KreditHarianResource\Pages;

use App\Filament\Resources\KreditHarianResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKreditHarian extends ViewRecord
{
    protected static string $resource = KreditHarianResource::class;
    protected function getHeaderActions(): array
    {
        return [];
    }
}
