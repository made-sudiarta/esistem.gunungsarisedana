<?php

namespace App\Filament\Resources\KreditHarianResource\Pages;

use App\Filament\Resources\KreditHarianResource;
use Filament\Resources\Pages\ViewRecord;

class PrintKreditHarian extends ViewRecord
{
    protected static string $resource = KreditHarianResource::class;

    protected static string $view = 'filament.resources.kredit-harian.print';

    /**
     * Bypass authorization (print = read-only)
     */
    protected function authorizeAccess(): void
    {
        // allow
    }
}
