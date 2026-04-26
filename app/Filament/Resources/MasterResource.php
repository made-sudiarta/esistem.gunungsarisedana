<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MasterResource\Pages;
use App\Models\Master;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MasterResource extends Resource
{
    protected static ?string $model = Master::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Master';

    protected static ?string $modelLabel = 'Master';

    protected static ?string $pluralModelLabel = 'Master';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Master')
                    ->schema([
                        Forms\Components\TextInput::make('group')
                            ->label('Group')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: absensi'),

                        Forms\Components\TextInput::make('key')
                            ->label('Key')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: kantor_latitude'),

                        Forms\Components\Textarea::make('value')
                            ->label('Value')
                            ->required()
                            ->rows(3)
                            ->placeholder('Contoh: -8.6500000'),

                        Forms\Components\TextInput::make('description')
                            ->label('Deskripsi')
                            ->maxLength(255)
                            ->placeholder('Contoh: Latitude lokasi kantor untuk validasi absensi'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Group')
                    ->options(fn (): array => Master::query()
                        ->select('group')
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group', 'group')
                        ->toArray()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMasters::route('/'),
            'create' => Pages\CreateMaster::route('/create'),
            'edit' => Pages\EditMaster::route('/{record}/edit'),
        ];
    }
}