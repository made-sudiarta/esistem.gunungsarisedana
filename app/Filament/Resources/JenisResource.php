<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisResource\Pages;
use App\Filament\Resources\JenisResource\RelationManagers;
use App\Models\Jenis;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class JenisResource extends Resource
{
    protected static ?string $model = Jenis::class;

    protected static ?string $navigationLabel = 'Jenis Keanggotaan';
    protected static ?string $navigationGroup = 'Keanggotaan';
    protected static ?string $navigationIcon = 'heroicon-o-bookmark';
    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Jenis Anggota';
    protected static ?string $pluralModelLabel = 'Jenis Anggota';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('Jenis Anggota Koperasi')
                ->description('Lengkapi data jenis anggota secara lengkap dan benar.')
                ->schema([
                    Forms\Components\Grid::make(1)
                        ->schema([
                            Forms\Components\TextInput::make('jenis')
                                ->label('Jenis Anggota')
                                ->required(),

                            Forms\Components\Textarea::make('keterangan')
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
            ->defaultSort('id', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('jenis')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListJenis::route('/'),
            'create' => Pages\CreateJenis::route('/create'),
            'edit' => Pages\EditJenis::route('/{record}/edit'),
        ];
    }
}
