<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Anggota Baru'),
            // Actions\Action::make('trxSimpananPokok')
            //     ->label('Pokok')
            //     ->url(fn ($record) => route('filament.admin.resources.simpanan-pokoks.create', $record))
            //     ->color('success')
            //     ->icon('heroicon-o-banknotes'),
            Actions\Action::make('trxSimpananPokok')
                ->label('Pokok')
                ->url(fn ($record) => route('filament.admin.resources.simpanan-pokoks.create', $record))
                ->color('success')
                ->icon('heroicon-o-banknotes')
                ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->can('trx_simpanan_pokok')),
            Actions\Action::make('trxSimpananPenyerta')
                ->label('Penyerta')
                // ->url(route('filament.admin.resources.simpanan-anggotas.index'))     
                ->url(fn ($record) => route('filament.admin.resources.simpanan-penyertas.create', $record))
                ->color('success')
                ->icon('heroicon-o-banknotes')
                ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->can('trx_simpanan_pokok')),
            Actions\Action::make('trxSimpananWajib')
                ->label('Wajib')
                // ->url(route('filament.admin.resources.simpanan-anggotas.index'))     
                ->url(fn ($record) => route('filament.admin.resources.simpanan-wajibs.create', $record))
                ->color('success')
                ->icon('heroicon-o-banknotes')
                ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->can('trx_simpanan_pokok')),
            // Actions\Action::make('print')
            //     ->label('Print')
            //     ->icon('heroicon-o-printer')
            //     ->url(route('prints.members')) // Rute khusus yang akan kamu buat
            //     ->openUrlInNewTab(),
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(fn ($livewire) => route('prints.members', [
                    'filters' => $livewire->tableFilters ?? [],
                ]))
                ->openUrlInNewTab()
                ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->can('trx_simpanan_pokok')),


            Actions\Action::make('print-all')
                ->label('Cetak Semua ID Card')
                ->icon('heroicon-o-document-duplicate')
                ->url(route('print.members.all'))
                ->color('success')
                ->openUrlInNewTab()

                ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->can('trx_simpanan_pokok')),
        ];
    }
}
