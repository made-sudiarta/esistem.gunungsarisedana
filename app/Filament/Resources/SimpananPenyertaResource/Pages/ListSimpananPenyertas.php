<?php

namespace App\Filament\Resources\SimpananPenyertaResource\Pages;

use App\Filament\Resources\SimpananPenyertaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SimpananPenyertaResource\Widgets\TransaksiPenyertaStats;


class ListSimpananPenyertas extends ListRecords
{
    protected static string $resource = SimpananPenyertaResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TransaksiPenyertaStats::class,
        ];
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Transaksi Baru'),
        ];
    }
}
