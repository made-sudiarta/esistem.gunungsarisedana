<?php

namespace App\Filament\Resources\KreditHarianResource\Pages;

use App\Filament\Resources\KreditHarianResource;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class AkadKredit extends Page
{
    use InteractsWithRecord;

    protected static string $resource = KreditHarianResource::class;

    protected static string $view = 'filament.resources.kredit-harian.pages.akad-kredit';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
    }
}