<?php

namespace App\Filament\Resources\TransaksiKreditBulananResource\Pages;

use App\Filament\Resources\TransaksiKreditBulananResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Concerns\ExposesTableToWidgets;

class ListTransaksiKreditBulanans extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = TransaksiKreditBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Transaksi Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return TransaksiKreditBulananResource::getWidgets();
    }
}