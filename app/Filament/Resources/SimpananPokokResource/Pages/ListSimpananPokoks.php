<?php

namespace App\Filament\Resources\SimpananPokokResource\Pages;

use App\Filament\Resources\SimpananPokokResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use App\Filament\Resources\SimpananPokokResource\Widgets\TransaksiPokokStats;

class ListSimpananPokoks extends ListRecords
{
    protected static string $resource = SimpananPokokResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            TransaksiPokokStats::class,
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
