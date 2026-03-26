<?php

namespace App\Filament\Resources\TransaksiKreditBulananResource\Pages;

use App\Filament\Resources\TransaksiKreditBulananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiKreditBulanans extends ListRecords
{
    protected static string $resource = TransaksiKreditBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}