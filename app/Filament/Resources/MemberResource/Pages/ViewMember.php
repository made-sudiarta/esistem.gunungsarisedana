<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil')
                ->label('Ubah Anggota'),
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('print.members', ['member' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }

    // Menampilkan data menggunakan Infolist
    public function infolist(Infolist $infolist): Infolist
    {
        // Ambil data yang sudah dihitung sebelumnya
        $member = $this->record;

        // Menggunakan data yang dihitung di mutateFormDataBeforeFill()
        $totalDebitPokok = $member->trxSimpananPokoks()->sum('debit');
        $totalKreditPokok = $member->trxSimpananPokoks()->sum('kredit');
        $saldoPokok = $totalKreditPokok - $totalDebitPokok;

        $totalDebitPenyerta = $member->trxSimpananPenyertas()->sum('debit');
        $totalKreditPenyerta = $member->trxSimpananPenyertas()->sum('kredit');
        $saldoPenyerta = $totalKreditPenyerta - $totalDebitPenyerta;

        $totalDebitWajib = $member->trxSimpananWajibs()->sum('debit');
        $totalKreditWajib = $member->trxSimpananWajibs()->sum('kredit');
        $saldoWajib = $totalKreditWajib - $totalDebitWajib;

        return $infolist
            ->schema([
                Section::make('Informasi Anggota')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('nia')
                                    ->label('NIA (No. Induk Anggota)')
                                    ->placeholder('-'),

                                TextEntry::make('nama_lengkap')
                                    ->label('Nama Lengkap')
                                    ->placeholder('-'),

                                TextEntry::make('tempat_lahir')
                                    ->label('Tempat Lahir')
                                    ->placeholder('-'),

                                TextEntry::make('tanggal_lahir')
                                    ->label('Tanggal Lahir')
                                    ->date('d F Y')
                                    ->placeholder('-'),

                                TextEntry::make('no_hp')
                                    ->label('No. Handphone')
                                    ->placeholder('-'),
                            ]),
                    ]),

                Section::make('Simpanan Anggota')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('saldoPokok')
                                    ->label('Simpanan Pokok')
                                    ->money('IDR', true)
                                    ->placeholder(number_format($saldoPokok, 0, ',', '.')),  // Menampilkan saldoPenyerta yang dihitung
                                    
                                TextEntry::make('saldoPenyerta')
                                    ->label('Simpanan Penyerta')
                                    ->money('IDR', true)
                                    ->placeholder(number_format($saldoPenyerta, 0, ',', '.')),  // Menampilkan saldoPenyerta yang dihitung

                                TextEntry::make('saldoWajib')
                                    ->label('Simpanan Wajib')
                                    ->money('IDR', true)
                                    ->placeholder(number_format($saldoWajib, 0, ',', '.')) // Menggunakan placeholder untuk menampilkan saldo
                            ]),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $member = $this->record;

        $data['total_debitPokok'] = $member->trxSimpananPokoks()->sum('debit');
        $data['total_kreditPokok'] = $member->trxSimpananPokoks()->sum('kredit');
        $data['saldoPokok'] = $data['total_kreditPokok'] - $data['total_debitPokok'];

        $data['total_debitPenyerta'] = $member->trxSimpananPenyertas()->sum('debit');
        $data['total_kreditPenyerta'] = $member->trxSimpananPenyertas()->sum('kredit');
        $data['saldoPenyerta'] = $data['total_kreditPenyerta'] - $data['total_debitPenyerta'];

        $data['total_debitWajib'] = $member->trxSimpananWajibs()->sum('debit');
        $data['total_kreditWajib'] = $member->trxSimpananWajibs()->sum('kredit');
        $data['saldoWajib'] = $data['total_kreditWajib'] - $data['total_debitWajib'];

        return $data;
    }
}