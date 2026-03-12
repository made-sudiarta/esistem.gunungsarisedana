<?php

namespace App\Filament\Resources\SimpananWajibResource\Pages;

use App\Filament\Resources\SimpananWajibResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SimpananWajibResource\Widgets\TransaksiWajibStats;

class ListSimpananWajibs extends ListRecords
{
    protected static string $resource = SimpananWajibResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make(),
    //     ];
    // }
    protected function getHeaderWidgets(): array
    {
        return [
            TransaksiWajibStats::class,
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
