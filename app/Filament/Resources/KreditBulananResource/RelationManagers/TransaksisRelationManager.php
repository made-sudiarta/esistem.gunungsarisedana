<?php

namespace App\Filament\Resources\KreditBulananResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransaksisRelationManager extends RelationManager
{
    protected static string $relationship = 'transaksis';

    protected static ?string $title = 'Transaksi Pembayaran';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('tanggal_transaksi')
                ->label('Tanggal Transaksi')
                ->default(now())
                ->required(),

            Forms\Components\TextInput::make('saldo_awal')
                ->label('Saldo Awal')
                ->numeric()
                ->prefix('Rp')
                ->disabled()
                ->dehydrated(false)
                ->default(fn () => (float) $this->ownerRecord->getSisaSaldo()),

            Forms\Components\TextInput::make('pokok')
                ->label('Pokok')
                ->numeric()
                ->prefix('Rp')
                ->required()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $saldoAwal = (float) $this->ownerRecord->getSisaSaldo();
                    $pokok = (float) ($get('pokok') ?? 0);
                    $bunga = (float) ($get('bunga') ?? 0);
                    $denda = (float) ($get('denda') ?? 0);

                    $set('nominal_bayar', $pokok + $bunga + $denda);
                    $set('sisa_saldo', max($saldoAwal - $pokok, 0));
                }),

            Forms\Components\TextInput::make('bunga')
                ->label('Bunga')
                ->numeric()
                ->prefix('Rp')
                ->default(0)
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $saldoAwal = (float) $this->ownerRecord->getSisaSaldo();
                    $pokok = (float) ($get('pokok') ?? 0);
                    $bunga = (float) ($get('bunga') ?? 0);
                    $denda = (float) ($get('denda') ?? 0);

                    $set('nominal_bayar', $pokok + $bunga + $denda);
                    $set('sisa_saldo', max($saldoAwal - $pokok, 0));
                }),

            Forms\Components\TextInput::make('denda')
                ->label('Denda')
                ->numeric()
                ->prefix('Rp')
                ->default(0)
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $saldoAwal = (float) $this->ownerRecord->getSisaSaldo();
                    $pokok = (float) ($get('pokok') ?? 0);
                    $bunga = (float) ($get('bunga') ?? 0);
                    $denda = (float) ($get('denda') ?? 0);

                    $set('nominal_bayar', $pokok + $bunga + $denda);
                    $set('sisa_saldo', max($saldoAwal - $pokok, 0));
                }),

            Forms\Components\TextInput::make('nominal_bayar')
                ->label('Nominal Bayar')
                ->numeric()
                ->prefix('Rp')
                ->disabled()
                ->dehydrated(true),

            Forms\Components\TextInput::make('sisa_saldo')
                ->label('Sisa Saldo')
                ->numeric()
                ->prefix('Rp')
                ->disabled()
                ->dehydrated(true),

            Forms\Components\Textarea::make('keterangan')
                ->label('Keterangan')
                ->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') 
            ->recordTitleAttribute('tanggal_transaksi')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('saldo_awal')
                    ->label('Saldo Awal')
                    ->money('idr', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('pokok')
                    ->label('Pokok')
                    ->money('idr', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('bunga')
                    ->label('Bunga')
                    ->money('idr', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('denda')
                    ->label('Denda')
                    ->money('idr', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('nominal_bayar')
                    ->label('Total Bayar')
                    ->money('idr', true)
                    ->sortable(),

                Tables\Columns\TextColumn::make('sisa_saldo')
                    ->label('Sisa Saldo')
                    ->money('idr', true)
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $saldoAwal = (float) $this->ownerRecord->getSisaSaldo();
                        $pokok = (float) ($data['pokok'] ?? 0);
                        $bunga = (float) ($data['bunga'] ?? 0);
                        $denda = (float) ($data['denda'] ?? 0);

                        $data['saldo_awal'] = $saldoAwal;
                        $data['nominal_bayar'] = $pokok + $bunga + $denda;
                        $data['sisa_saldo'] = max($saldoAwal - $pokok, 0);
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}