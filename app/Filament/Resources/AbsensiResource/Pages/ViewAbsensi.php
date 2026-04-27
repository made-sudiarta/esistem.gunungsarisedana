<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewAbsensi extends ViewRecord
{
    protected static string $resource = AbsensiResource::class;

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
        $isKolektor = $user?->hasRole('Kolektor') ?? false;
        $isCleaningService = $user?->hasRole('Cleaning Service') ?? false;

        return $infolist
            ->schema([
                Section::make('Informasi Absensi')
                    ->schema([
                        Grid::make($isSuperAdmin ? 5 : 4)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Karyawan')
                                    ->visible($isSuperAdmin),

                                TextEntry::make('tanggal')
                                    ->label('Tanggal')
                                    ->date('d F Y'),

                                TextEntry::make('jam_masuk')
                                    ->label('Jam Masuk')
                                    ->placeholder('-'),

                                TextEntry::make('jam_keluar')
                                    ->label('Jam Keluar')
                                    ->placeholder('-'),

                                TextEntry::make('jumlah_jam')
                                    ->label('Jam Kerja')
                                    ->suffix(' jam')
                                    ->placeholder('-'),

                                TextEntry::make('jumlah_setoran')
                                    ->label('Setoran Hari Ini')
                                    ->money('IDR', true)
                                    ->visible($isSuperAdmin || $isKolektor),

                                TextEntry::make('penarikan')
                                    ->label('Penarikan Hari Ini')
                                    ->money('IDR', true)
                                    ->visible($isSuperAdmin || $isKolektor),
                            ]),
                    ]),

                Section::make('Lokasi Absen Masuk')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('latitude_masuk')
                                    ->label('Latitude Masuk')
                                    ->placeholder('-'),

                                TextEntry::make('longitude_masuk')
                                    ->label('Longitude Masuk')
                                    ->placeholder('-'),

                                TextEntry::make('jarak_masuk')
                                    ->label('Jarak dari Kantor')
                                    ->suffix(' meter')
                                    ->placeholder('-')
                                    ->badge()
                                    ->color(fn ($state) => filled($state) && (int) $state <= 100 ? 'success' : 'warning'),
                            ]),
                    ])
                    ->visible($isSuperAdmin),

                Section::make('Lokasi Absen Keluar')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('latitude_keluar')
                                    ->label('Latitude Keluar')
                                    ->placeholder('-'),

                                TextEntry::make('longitude_keluar')
                                    ->label('Longitude Keluar')
                                    ->placeholder('-'),

                                TextEntry::make('jarak_keluar')
                                    ->label('Jarak dari Kantor')
                                    ->suffix(' meter')
                                    ->placeholder('-')
                                    ->badge()
                                    ->color(fn ($state) => filled($state) && (int) $state <= 100 ? 'success' : 'warning'),
                            ]),
                    ])
                    ->visible($isSuperAdmin),

                Section::make('Informasi Sistem')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('sumber_absen')
                                    ->label('Sumber Absen')
                                    ->badge()
                                    ->placeholder('-')
                                    ->color(fn (?string $state) => match ($state) {
                                        'widget_kolektor' => 'info',
                                        'widget_staff' => 'success',
                                        default => 'gray',
                                    }),

                                TextEntry::make('created_at')
                                    ->label('Dibuat')
                                    ->dateTime('d F Y H:i'),

                                TextEntry::make('updated_at')
                                    ->label('Diubah')
                                    ->dateTime('d F Y H:i'),
                            ]),
                    ])
                    ->visible($isSuperAdmin),
            ]);
    }
}