<?php
namespace App\Filament\Resources\KreditHarianResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransaksisRelationManager extends RelationManager
{
    protected static string $relationship = 'transaksis';
    protected static ?string $title = 'Riwayat Transaksi';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),

                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->dateTime(),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah Bayar')
                    ->money('idr', true),
                    
                Tables\Columns\TextColumn::make('sisa_pokok')
                ->label('Sisa Pokok')
                ->getStateUsing(function ($record) {

                    $kredit = $record->kreditHarian;

                    if (! $kredit) {
                        return 0;
                    }

                    // HITUNG TOTAL KREDIT LANGSUNG
                    $plafond = (float) $kredit->plafond;
                    $bunga   = (float) $kredit->bunga_persen;
                    $admin  = (float) $kredit->admin_persen;

                    $totalKredit = $plafond + ($plafond * ($bunga + $admin) / 100);

                    // HITUNG TOTAL BAYAR SAMPAI TRANSAKSI INI (PAKAI ID)
                    $totalBayar = \App\Models\KreditHarianTransaksi::query()
                        ->where('kredit_harian_id', $record->kredit_harian_id)
                        ->where('id', '<=', $record->id)
                        ->sum('jumlah');

                    return max(0, $totalKredit - $totalBayar);
                })
                ->money('idr', true),

            ])
            ->defaultSort('tanggal_transaksi', 'asc')
            ->actions([])          // ❌ tidak bisa edit/delete
            ->headerActions([])    // ❌ tidak bisa tambah
            ->bulkActions([]);     // ❌ tidak ada bulk
    }
}
