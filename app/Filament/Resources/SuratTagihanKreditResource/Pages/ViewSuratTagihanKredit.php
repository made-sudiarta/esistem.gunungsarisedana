<?php

namespace App\Filament\Resources\SuratTagihanKreditResource\Pages;

use App\Filament\Resources\SuratTagihanKreditResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;

class ViewSuratTagihanKredit extends ViewRecord
{
    protected static string $resource = SuratTagihanKreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cetak')
                ->label('Cetak')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(route('surat-tagihan-kredit.pdf', ['record' => $this->record]))
                ->openUrlInNewTab(),

            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Surat')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('nomor_surat')
                                    ->label('Nomor Surat'),

                                TextEntry::make('jenis_sp')
                                    ->label('Jenis SP')
                                    ->badge()
                                    ->color(fn (string $state) => match ($state) {
                                        'SP1' => 'gray',
                                        'SP2' => 'warning',
                                        'SP3' => 'danger',
                                        default => 'gray',
                                    }),

                                TextEntry::make('tanggal_surat')
                                    ->label('Tanggal Surat')
                                    ->date('d F Y'),

                                TextEntry::make('status_surat')
                                    ->label('Status Surat')
                                    ->badge()
                                    ->color(fn (string $state) => match ($state) {
                                        'draft' => 'gray',
                                        'terbit' => 'warning',
                                        'cetak' => 'success',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),

                Section::make('Informasi Kredit')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('no_pokok')
                                    ->label('No Pokok'),

                                TextEntry::make('kreditBulanan.member.nama_lengkap')
                                    ->label('Nama Anggota'),

                                TextEntry::make('kreditBulanan.group.group')
                                    ->label('Group / Kolektor'),

                                TextEntry::make('tanggal_jatuh_tempo')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->date('d F Y'),

                                TextEntry::make('jumlah_tunggakan_bunga')
                                    ->label('Jumlah Tunggakan')
                                    ->suffix(' bulan')
                                    ->badge()
                                    ->color(fn ($state) => (int) $state > 0 ? 'danger' : 'success'),

                                TextEntry::make('sisa_tunggakan_bunga')
                                    ->label('Sisa Tunggakan Bunga')
                                    ->money('IDR', true),

                                TextEntry::make('bunga_per_bulan')
                                    ->label('Bunga Per Bulan')
                                    ->money('IDR', true),

                                TextEntry::make('total_tunggakan_bunga')
                                    ->label('Total Tunggakan Bunga')
                                    ->money('IDR', true),

                                TextEntry::make('sisa_pokok_kredit')
                                    ->label('Sisa Pokok Kredit')
                                    ->money('IDR', true),
                            ]),
                    ]),

                Section::make('Keterangan')
                    ->schema([
                        TextEntry::make('keterangan')
                            ->label('')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}