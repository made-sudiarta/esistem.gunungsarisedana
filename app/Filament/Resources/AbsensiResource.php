<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiResource\Pages;
use App\Filament\Resources\AbsensiResource\RelationManagers;
use App\Models\Absensi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions;
use Filament\Tables\Actions\Action;




class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Absensi Karyawan';
    protected static ?string $navigationGroup = 'Absensi';

    protected static ?string $modelLabel = 'Absensi Karyawan';
    protected static ?string $pluralModelLabel = 'Absensi Karyawan';
    protected static ?string $title = 'Absensi Karyawan';

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()->hasRole('super_admin')) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }
    protected static function mutateFormDataBeforeSave(array $data): array
    {
        // proteksi user_id
        if (! auth()->user()->hasRole('super_admin')) {
            unset($data['user_id'], $data['penarikan']);
        }

        // validasi penarikan
        if (
            isset($data['penarikan'], $data['jumlah_setoran']) &&
            $data['penarikan'] > $data['jumlah_setoran']
        ) {
            throw new \Exception('Penarikan tidak boleh melebihi jumlah setoran');
        }

        // hitung jumlah jam (kode Anda tetap di sini)
        if (! empty($data['jam_masuk']) && ! empty($data['jam_keluar'])) {
            $masuk  = Carbon::parse($data['jam_masuk']);
            $keluar = Carbon::parse($data['jam_keluar']);

            if ($keluar->greaterThan($masuk)) {
                $data['jumlah_jam'] = round(
                    $keluar->diffInMinutes($masuk) / 60,
                    2
                );
            }
        }

        return $data;
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // ===== DATA KARYAWAN =====
                Forms\Components\Section::make('Data Karyawan')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Karyawan')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            // Hanya super admin bisa pilih
                            ->visible(fn () => auth()->user()->hasRole('super_admin'))
                            // Default untuk user biasa
                            ->default(fn () => auth()->id())
                            ->dehydrated(true),

                        // Tampilkan read-only nama user untuk user biasa
                        Forms\Components\Placeholder::make('user_name')
                            ->label('Karyawan')
                            ->visible(fn () => ! auth()->user()->hasRole('super_admin'))
                            ->content(fn ($record) => $record ? ($record->user->name ?? '') : auth()->user()->name)
                    ])
                    ->columns(2)
                    ->collapsible(),

                // ===== WAKTU ABSENSI =====
                Forms\Components\Section::make('Waktu Absensi')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->label('Tanggal')
                            ->default(now())
                            ->required()
                            ->disabled(fn ($livewire, $record) => ! auth()->user()->hasRole('super_admin') && $record),


                       Forms\Components\TimePicker::make('jam_masuk')
                            ->label('Jam Masuk')
                            ->seconds(false)
                            ->required()
                            ->default(now()->format('H:i'))
                            ->disabled(fn ($livewire, $record) => ! auth()->user()->hasRole('super_admin') && $record),


                        Forms\Components\TimePicker::make('jam_keluar')
                            ->label('Jam Keluar')
                            ->seconds(false)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                if ($state && $get('jam_masuk')) {
                                    $masuk  = Carbon::parse($get('jam_masuk'));
                                    $keluar = Carbon::parse($state);

                                    $jam = $keluar->diffInMinutes($masuk) / 60;
                                    $set('jumlah_jam', round($jam, 2));
                                }
                            })
                            ->default(now()->format('H:i'))
                            ->disabled(fn ($livewire, $record) => ! auth()->user()->hasRole('super_admin') && $record),

                    ])
                    ->columns(3),

                // ===== HASIL PERHITUNGAN =====
                Forms\Components\Section::make('Perhitungan Jam Kerja')
                    ->icon('heroicon-o-calculator')
                    ->schema([
                        Forms\Components\TextInput::make('jumlah_jam')
                            ->label('Total Jam Kerja')
                            ->disabled()
                            ->dehydrated(true)
                            ->suffix('jam')
                            ->numeric(),
                    ])
                    ->columns(1),

                // ===== KEUANGAN =====
                Forms\Components\Section::make('Keuangan')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        Forms\Components\TextInput::make('jumlah_setoran')
                            ->label('Setoran Hari ini')
                            ->prefix('Rp')
                            ->numeric()
                            ->default('0')
                            ->nullable()
                            // Hanya user biasa atau super admin bisa edit
                            ->disabled(fn () => false),

                        Forms\Components\TextInput::make('penarikan')
                            ->label('Penarikan Hari ini')
                            ->prefix('Rp')
                            ->numeric()
                            ->default('0')
                            ->nullable()
                            ->disabled(fn () => false),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),

                Tables\Columns\TextColumn::make('jam_masuk'),
                Tables\Columns\TextColumn::make('jam_keluar'),

                Tables\Columns\TextColumn::make('jumlah_jam')
                    ->suffix(' jam'),

                Tables\Columns\TextColumn::make('jumlah_setoran')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('penarikan')
                    ->label('Penarikan')
                    ->money('IDR')
                    ->toggleable(),
                
                

            ])
            ->modifyQueryUsing(function ($query) {
                if (! auth()->user()->hasRole('super_admin')) {
                    $query->where('user_id', auth()->id());
                }
            })

            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Actions\ViewAction::make(),

                Actions\EditAction::make()
                    ->visible(fn ($record) =>
                        auth()->user()->hasRole('super_admin') && ! $record->trashed()
                    ),

                Actions\DeleteAction::make()
                    ->visible(fn ($record) =>
                        auth()->user()->hasRole('super_admin') && ! $record->trashed()
                    ),

                Actions\RestoreAction::make()
                    ->visible(fn ($record) =>
                        auth()->user()->hasRole('super_admin') && $record->trashed()
                    ),

                Actions\ForceDeleteAction::make()
                    ->visible(fn ($record) =>
                        auth()->user()->hasRole('super_admin') && $record->trashed()
                    ),
                

            ])
            

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
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
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}
