<?php

namespace App\Filament\Resources\KreditHarianResource\Pages;

use App\Filament\Resources\KreditHarianResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewKreditHarian extends ViewRecord
{
    protected static string $resource = KreditHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn (): bool => Auth::user()?->hasRole('super_admin') ?? false),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $user = Auth::user();
        $isSuperAdmin = $user?->hasRole('super_admin') ?? false;

        return $infolist
            ->schema([
                Section::make('Informasi Kredit Harian')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('no_pokok')
                                    ->label('No Pokok')
                                    ->placeholder('-'),

                                TextEntry::make('member.nama_lengkap')
                                    ->label('Nama Peminjam')
                                    ->placeholder('-'),

                                TextEntry::make('plafond')
                                    ->label('Plafond')
                                    ->money('IDR', true)
                                    ->placeholder('-'),

                                TextEntry::make('jangka_waktu')
                                    ->label('Jangka Waktu (hari)')
                                    ->placeholder('-'),

                                TextEntry::make('tanggal_pengajuan')
                                    ->label('Tanggal Pengajuan')
                                    ->date('d F Y')
                                    ->placeholder('-'),

                                TextEntry::make('tanggal_jatuh_tempo')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->date('d F Y')
                                    ->placeholder('-'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => $state === 'lunas' ? 'success' : 'danger'),
                            ]),
                    ]),

                Section::make('Biaya dan Bunga')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('bunga_persen')
                                    ->label('Bunga (%)')
                                    ->placeholder('-'),

                                TextEntry::make('admin_persen')
                                    ->label('Admin (%)')
                                    ->placeholder('-'),

                                TextEntry::make('total_tagihan')
                                    ->label('Total Tagihan')
                                    ->money('IDR', true)
                                    ->placeholder('-'),
                                    

                                TextEntry::make('angsuran_per_bulan')
                                    ->label('Angsuran per Bulan')
                                    ->money('IDR', true)
                                    ->placeholder('-')
                                    
                            ]),
                    ])
                    ->visible($isSuperAdmin),
            ]);
    }
}