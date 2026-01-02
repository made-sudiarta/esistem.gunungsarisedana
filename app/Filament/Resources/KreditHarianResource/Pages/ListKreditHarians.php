<?php

namespace App\Filament\Resources\KreditHarianResource\Pages;

use App\Filament\Resources\KreditHarianResource;
use App\Filament\Resources\KreditHarianResource\Widgets\KreditHarianStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKreditHarians extends ListRecords
{
    protected static string $resource = KreditHarianResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\CreateAction::make()->label('Pinjaman Baru')->icon('heroicon-o-plus'),
    //     ];
    // }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Pinjaman Baru')
                ->icon('heroicon-o-plus'),

            Actions\Action::make('print')
                ->label('Print Data')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn () => route('kredit-harian.print.index'))
                ->openUrlInNewTab(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            KreditHarianStats::class,
        ];
    }
}
