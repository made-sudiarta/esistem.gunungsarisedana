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
use Carbon\Carbon;

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
    protected function monthToRoman(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        return $romans[$month] ?? '';
    }
    public function infolist(Infolist $infolist): Infolist
    {
        $user = Auth::user();
        $isSuperAdmin = $user?->hasRole('super_admin') ?? false;

        // Mengambil data dari record
        $noPokok = $this->record->no_pokok;
        $memberName = $this->record->member->nama_lengkap;
        $plafond = $this->record->plafond;
        $jangkaWaktu = $this->record->jangka_waktu;
        $tanggalPengajuan = Carbon::parse($this->record->tanggal_pengajuan);
        $tanggalJatuhTempo = $tanggalPengajuan->copy()->addDays($jangkaWaktu);
        $status = $this->record->status;
        

        // Menghitung bunga dan admin dalam rupiah
        $bungaPersen = $this->record->bunga_persen;
        $adminPersen = $this->record->admin_persen;

        $bungaRupiah = $this->record->plafond * $bungaPersen / 100;
        $adminRupiah = $this->record->plafond * $adminPersen / 100;

        // Menghitung total tagihan dan angsuran per bulan
        $totalTagihan = $plafond + $bungaRupiah + $adminRupiah;
        $angsuranPerBulan = $totalTagihan / $jangkaWaktu;

        $bulanRomawi = $this->monthToRoman($tanggalPengajuan->month);
        $group = $this->record->group->group;

        return $infolist
            ->schema([
                Section::make('Informasi Kredit Harian')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('nopokok')
                                    ->label('No Pokok')
                                    ->default('00' . $noPokok . '/KGSH/' . $group . '/' . $bulanRomawi . '/' . $tanggalPengajuan->isoFormat('YYYY'))
                                    ->placeholder('-'),

                                TextEntry::make('nia')
                                    ->label('No Induk')
                                    ->default('00' . $this->record->member->nia . '/' . ($this->record->member->jenis->keterangan ?? '-'))
                                    ->placeholder('-'),

                                TextEntry::make('member.nama_lengkap')
                                    ->label('Nama Peminjam')
                                    ->default($memberName)
                                    ->placeholder('-'),

                                TextEntry::make('plafond')
                                    ->label('Plafond')
                                    ->money('IDR', true)
                                    ->default($plafond)
                                    ->placeholder('-'),

                                TextEntry::make('sisa_pokok')
                                    ->label('Sisa Pokok')
                                    ->money('IDR', true)
                                    ->placeholder('-'),

                                TextEntry::make('tanggal_pengajuan')
                                    ->label('Tanggal Pengajuan')
                                    ->date('d F Y')
                                    ->default($tanggalPengajuan->format('d F Y'))
                                    ->placeholder('-'),

                                TextEntry::make('tanggal_jatuh_tempo')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->date('d F Y')
                                    ->default($tanggalJatuhTempo->format('d F Y'))
                                    ->placeholder('-'),
                                
                                TextEntry::make('jaminan')
                                    ->label('Jaminan')
                                    ->placeholder('-'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => $state === 'lunas' ? 'success' : 'danger')
                                    ->default($status),
                            ]),
                    ]),

                Section::make('Biaya dan Bunga')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('bungarupiah')
                                    ->label('Bunga')
                                    ->money('IDR', true)
                                    ->default($bungaRupiah)
                                    ->placeholder('-'),

                                TextEntry::make('adminrupaih')
                                    ->label('Admin')
                                    ->money('IDR', true)
                                    ->default($adminRupiah)
                                    ->placeholder('-'),

                                TextEntry::make('total_tagihan')
                                    ->label('Total Tagihan')
                                    ->money('IDR', true)
                                    ->default($totalTagihan)
                                    ->placeholder('-'),

                                TextEntry::make('angsuran_per_bulan')
                                    ->label('Angsuran per Bulan')
                                    ->money('IDR', true)
                                    ->default($angsuranPerBulan)
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->visible($isSuperAdmin),
            ]);
    }
}