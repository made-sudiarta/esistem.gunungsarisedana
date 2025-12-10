<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SetoranSukarelaResource\Pages;
use App\Filament\Resources\SetoranSukarelaResource\RelationManagers;
use App\Models\SetoranSukarela;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Actions\Action;

class SetoranSukarelaResource extends Resource
{
    protected static ?string $model = SetoranSukarela::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setoran Simpanan Sukarela')
                ->description('Lengkapi data setoran secara lengkap dan benar.')
                ->schema([
                    Forms\Components\Grid::make(1)
                        ->schema([
                            Forms\Components\hidden::make('tanggal_trx')
                                ->label('Tanggal Transaksi')
                                ->default(function () {
                                    $setoranId = request()->get('setoran_id');

                                    if ($setoranId) {
                                        $setoran = \App\Models\Setoran::find($setoranId);
                                        return $setoran?->tanggal_trx ?? now();
                                    }

                                    return now();
                                }),
                            // Hapus select untuk setoran_id
                            Forms\Components\Hidden::make('setoran_id')
                            ->default(request()->get('setoran_id')),
            
                            // Forms\Components\Hidden::make('setoran_id')
                            //     ->default(fn () => $this->setoranId),

                            Forms\Components\Select::make('sukarela_id')
                                ->label('No. Rekening')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->options(function ($get) {
                                    $setoranId = $get('setoran_id') ?? $this->setoran_id;

                                    $setoran = \App\Models\Setoran::find($setoranId);
                                    
                                    if (! $setoran) {
                                        return [];
                                    }

                                    return \App\Models\Sukarela::where('group_id', $setoran->group_id)
                                        ->with(['groups', 'members'])
                                        ->get()
                                        ->mapWithKeys(function ($record) {
                                            $noRek = str_pad($record->no_rek, 5, '0', STR_PAD_LEFT);
                                            $group = $record->groups->group ?? 'Tanpa Group';
                                            $nama = $record->members->nama_lengkap ?? 'Tanpa Nama';
                                            return [$record->id => "{$noRek}/{$group} - {$nama}"];
                                        });
                                }),
                            Forms\Components\TextInput::make('jumlah')
                                ->label('Jumlah')
                                ->numeric()
                                ->required()
                                ->default(0),
                                \Filament\Forms\Components\Group::make([
                                \Filament\Forms\Components\Actions::make([
                                    Action::make('set10k')
                                        ->label('10.000')
                                        ->color('gray')
                                        ->action(fn (callable $set) => $set('jumlah', 10000)),

                                    Action::make('set20k')
                                        ->label('20.000')
                                        ->color('gray')
                                        ->action(fn (callable $set) => $set('jumlah', 20000)),

                                    Action::make('set50k')
                                        ->label('50.000')
                                        ->color('gray')
                                        ->action(fn (callable $set) => $set('jumlah', 50000)),
                                    Action::make('set100k')
                                        ->label('100.000')
                                        ->color('gray')
                                        ->action(fn (callable $set) => $set('jumlah', 100000)),
                                ]),
                            ]),
                            Forms\Components\Textarea::make('keterangan')
                                ->columnSpanFull(),
                        ])
                ])
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_trx')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('setoran.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sukarela.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListSetoranSukarelas::route('/'),
            'create' => Pages\CreateSetoranSukarela::route('/create'),
            'edit' => Pages\EditSetoranSukarela::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return false; // menyembunyikan dari menu sidebar
    }
}
