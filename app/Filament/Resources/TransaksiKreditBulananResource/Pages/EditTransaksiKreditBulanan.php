<?php

namespace App\Filament\Resources\TransaksiKreditBulananResource\Pages;

use App\Filament\Resources\TransaksiKreditBulananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaksiKreditBulanan extends EditRecord
{
    protected static string $resource = TransaksiKreditBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}