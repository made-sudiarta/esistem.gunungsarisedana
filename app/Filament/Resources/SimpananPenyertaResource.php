<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimpananPenyertaResource\Pages;
use App\Filament\Resources\SimpananPenyertaResource\RelationManagers;
use App\Models\TrxSimpananPenyerta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;

class SimpananPenyertaResource extends Resource
{
    protected static ?string $model = TrxSimpananPenyerta::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Transaksi Penyerta';
    protected static ?string $navigationGroup = 'Keanggotaan';

    protected static ?string $modelLabel = 'Simpanan Penyerta Anggota';
    protected static ?string $pluralModelLabel = 'Simpanan Penyerta Anggota';
    protected static ?string $title = 'Simpanan Penyerta Anggota';
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Simpanan Penyerta Anggota')
                    ->description('Pilih anggota lalu proses beberapa transaksi')
                    ->schema([
                        DatePicker::make('tanggal_trx')
                            ->default(now())
                            ->label('Tanggal Transaksi')
                            ->required(),

                        Forms\Components\Repeater::make('transactions')
                            ->label('Transaksi')
                            ->schema([
                                Select::make('member_id')
                                    ->label('Anggota Koperasi')
                                    ->relationship('members', 'nama_lengkap')
                                    ->getOptionLabelFromRecordUsing(fn ($record) =>
                                        str_pad($record->nia, 5, '0', STR_PAD_LEFT) . ' - ' . $record->nama_lengkap
                                    )
                                    ->searchable(['nia', 'nama_lengkap'])
                                    ->preload()
                                    ->required(),

                                TextInput::make('kredit')
                                    ->label('Setoran')
                                    ->numeric()
                                    ->prefix('Rp'),

                                TextInput::make('debit')
                                    ->label('Penarikan')
                                    ->numeric()
                                    ->prefix('Rp'),

                                TextInput::make('keterangan')
                                    ->label('Keterangan'),                            
                                ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Tambah Transaksi')
                            ->columnSpan('full'),
                    ])
                    ->columns(1)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_trx')
                    ->label('Tanggal')
                    ->date('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('members.nia')
                    ->label('NIA')
                    ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('members.nama_lengkap')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kredit')
                    ->label('Setoran')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('debit')
                    ->label('Penarikan')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->whereDate('tanggal_trx', now()))
                    ->default(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSimpananPenyertas::route('/'),
            'create' => Pages\CreateSimpananPenyerta::route('/create'),
            'edit' => Pages\EditSimpananPenyerta::route('/{record}/edit'),
        ];
    }
    // public static function shouldRegisterNavigation(): bool
    // {
    //     return false; // menyembunyikan dari menu sidebar
    // }
}
