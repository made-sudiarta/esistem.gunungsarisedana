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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Simpanan Penyerta Anggota';
    protected static ?string $pluralModelLabel = 'Simpanan Penyerta Anggota';
    protected static ?string $title = 'Simpanan Penyerta Anggota';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Simpanan Penyerta Anggota')
                ->description('Pilih anggota lalu proses transaksi')
                ->schema([
                    Forms\Components\Grid::make(1)
                        ->schema([
                            Select::make('member_id')
                                ->label('Anggota Koperasi')
                                ->relationship('members', 'nama_lengkap')
                                ->searchable()
                                ->preload()
                                ->required(),
                            DatePicker::make('tanggal_trx')
                                ->default(Now())
                                ->label('Tanggal Transaksi')
                                ->required(),
                            TextInput::make('kredit')
                                ->label('Setoran')
                                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                ->prefix('Rp'),
                            TextInput::make('debit')
                                ->label('Penarikan')
                                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                ->prefix('Rp'),
                            Textarea::make('keterangan')
                                ->label('Keterangan'),
                        ])
                ])
                ->columns(2)
                ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
    public static function shouldRegisterNavigation(): bool
    {
        return false; // menyembunyikan dari menu sidebar
    }
}
