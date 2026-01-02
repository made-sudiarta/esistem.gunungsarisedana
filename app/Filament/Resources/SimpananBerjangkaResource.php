<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SimpananBerjangkaResource\Pages;
use App\Filament\Resources\SimpananBerjangkaResource\RelationManagers;
use App\Models\SimpananBerjangka;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\TabsFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Facades\Filament;

use Filament\Tables\Filters\SelectFilter;

class SimpananBerjangkaResource extends Resource
{
    protected static ?string $model = SimpananBerjangka::class;
    protected static ?string $navigationGroup = 'Simpanan Berjangka';


    protected static ?string $modelLabel = 'Simpanan Berjangka';
    protected static ?string $pluralModelLabel = 'Simpanan Berjangka';
    protected static ?string $title = 'Simpanan Berjangka';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    // public static function getEloquentQuery(): Builder
    // {
    //     $query = parent::getEloquentQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //         ]);

    //     $user = Filament::auth()->user();
    //     if ($user->hasRole('super_admin')) {
    //         return $query;
    //     }
    //     return $query->whereHas('group', function (Builder $q) use ($user) {
    //         $q->where('user_id', $user->id);
    //     });
    // }
    

    public static function form(Form $form): Form
    {
        return $form
           ->schema([
                Forms\Components\TextInput::make('kode_bilyet')->required()->unique(ignoreRecord: true),
                Forms\Components\Select::make('group_id')
                    ->label('Kolektor')
                    ->relationship('group', 'group')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('member_id')
                    ->relationship('member', 'nama_lengkap')
                    ->label('Anggota')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_masuk')->required(),
                Forms\Components\TextInput::make('jangka_waktu')
                    ->numeric()
                    ->suffix('bulan')
                    ->required(),
                Forms\Components\TextInput::make('bunga_persen')
                    ->numeric()
                    ->suffix('%')
                    ->required(),
                Forms\Components\TextInput::make('nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')->label('No.')->rowIndex(),
                Tables\Columns\TextColumn::make('kode_bilyet')->label('No. Acc')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('group.group')->label('Group')->sortable(),
                Tables\Columns\TextColumn::make('nama_lengkap')->label('Nama Lengkap')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                ->sortable()
                ->searchable()
                ->label('Tanggal Masuk')
                ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('jangka_waktu')->suffix(' bulan'),
                Tables\Columns\TextColumn::make('tanggal_jatuh_tempo')
                
                ->label('Tanggal Jatuh Tempo')
                ->getStateUsing(function ($record) {
                    return \Carbon\Carbon::parse($record->tanggal_masuk)
                        ->addMonths($record->jangka_waktu)
                        ->format('d-m-Y');
                })
                ->searchable(query: function ($query, $search) {
                    return $query->whereRaw("
                        DATE_FORMAT(
                            DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH),
                            '%d-%m-%Y'
                        ) LIKE ?
                    ", ["%{$search}%"]);
                }),
                Tables\Columns\TextColumn::make('bunga_persen')->suffix('%'),
                Tables\Columns\TextColumn::make('nominal')
                ->label('Nominal')
                ->getStateUsing(fn ($record) => $record->nominal)
                ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.'))
                ->sortable(),

                Tables\Columns\TextColumn::make('bunga_bulan_ini')
                    ->label('Bunga Bulan Ini')
                    ->getStateUsing(function ($record) {
                        return ($record->bunga_persen / 100 / 12) * $record->nominal;
                    })
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.'))
                    ->sortable(),
            ])
            ->defaultSort('kode_bilyet', 'asc')
            ->filters([
                SelectFilter::make('group_id')->relationship('group', 'group')->label('Group'),
                SelectFilter::make('member_id')->relationship('member', 'nama_lengkap')->label('Anggota'),
                Tables\Filters\TrashedFilter::make(), // Untuk soft delete
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->url(fn ($record) => route('cetak-struk', $record->id))
                ->openUrlInNewTab()
                ->visible(fn () =>
                    Filament::auth()->user()?->hasRole('super_admin')
                ),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('printBulk')
                    ->label('Print Struk')
                    ->icon('heroicon-o-printer')
                    ->action(function ($records, $livewire) {
                        $ids = $records->pluck('id')->implode(',');
                        $url = route('cetak-struk-bulk', ['ids' => $ids]);

                        $livewire->dispatch('open-new-tab', url: $url);
                    })
                    ->visible(fn () =>
                        Filament::auth()->user()?->hasRole('super_admin')
                    ),

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
            'index' => Pages\ListSimpananBerjangkas::route('/'),
            'create' => Pages\CreateSimpananBerjangka::route('/create'),
            'edit' => Pages\EditSimpananBerjangka::route('/{record}/edit'),
        ];
    }
}
