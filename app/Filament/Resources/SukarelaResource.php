<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SukarelaResource\Pages;
use App\Filament\Resources\SukarelaResource\RelationManagers;
use App\Models\Sukarela;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\TabsFilter;
use Filament\Tables\Enums\FiltersLayout;

use App\Models\Group;


class SukarelaResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return false; // tidak muncul di menu
    }
    protected static ?string $model = Sukarela::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Simpanan Sukarela';
    protected static ?string $modelLabel = 'Simpanan Sukarela';
    protected static ?string $pluralModelLabel = 'Simpanan Sukarela';
    protected static ?string $title = 'Simpanan Sukarela';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Simpanan Sukarela')
            ->description('Lengkapi data simpanan sukarela')
            ->schema([
                Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\TextInput::make('no_rek')
                    ->label('No. Rekening')
                    ->required()
                    ->numeric(),
                    Forms\Components\DatePicker::make('tanggal_terdaftar')
                    ->default(Now())
                    ->required(),
                    Forms\Components\Select::make('group_id')
                    ->label('Kolektor')
                    ->relationship('groups', 'group')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('group')
                                ->label('Nama Group')
                                ->required(),

                            Forms\Components\Select::make('employee_id')
                                ->label('Pilih Karyawan')
                                ->relationship('employees', 'nama_lengkap')
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->members->nama_lengkap ?? 'Tanpa Nama')
                                // ->options(function () {
                                //     return \App\Models\Employee::with('members') // pastikan relasi member didefinisikan di model Employee
                                //         ->get()
                                //         ->mapWithKeys(function ($employee) {
                                //             return [$employee->id => $employee->members->nama_lengkap ?? 'Tanpa Nama'];
                                //         });
                                // })
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    Forms\Components\DatePicker::make('tanggal_terdaftar')
                                        ->label('Tanggal Daftar')
                                        ->required()
                                        ->default(Now()),
                                    Forms\Components\Select::make('member_id')
                                        ->label('Pilih Anggota')
                                        ->relationship('members', 'nama_lengkap')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([
                                            Forms\Components\DatePicker::make('tanggal_bergabung')
                                                ->label('Tanggal Bergabung')
                                                ->default(Now()),

                                            Forms\Components\TextInput::make('nia')
                                                ->label('NIA (No. Induk Anggota)')
                                                ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT))
                                                ->required(),

                                            Forms\Components\TextInput::make('nik')
                                                ->label('NIK (No. Induk Kependudukan)'),

                                            Forms\Components\TextInput::make('nama_lengkap')
                                                ->label('Nama Lengkap')
                                                ->required()
                                                ->columnspan(2)
                                                ->maxLength(255),

                                            Forms\Components\TextInput::make('tempat_lahir')
                                                ->label('Tempat Lahir')
                                                ->maxLength(255),

                                            Forms\Components\DatePicker::make('tanggal_lahir')
                                                ->label('Tanggal Lahir'),

                                            Forms\Components\Textarea::make('alamat')
                                                ->label('Alamat')
                                                ->rows(3)
                                                ->columnSpan(2),

                                            Forms\Components\TextInput::make('no_hp')
                                                ->label('No. Handphone')
                                                ->tel(),

                                            Forms\Components\Select::make('jenis_id')
                                                ->label('Jenis')
                                                ->relationship('jenis', 'jenis')
                                                ->searchable()
                                                ->preload()
                                                ->required()
                                                ->createOptionForm([
                                                    Forms\Components\TextInput::make('jenis')
                                                        ->label('Jenis')
                                                        ->required()
                                                        ->maxLength(255),
                                                    Forms\Components\TextInput::make('keterangan')
                                                        ->label('Keterangan')
                                                        ->maxLength(255),
                                                ])
                                                ->createOptionAction(function ($action) {
                                                    return $action
                                                        ->label('Tambah Jenis Anggota Baru')         
                                                        ->modalHeading('Form Jenis Anggota Baru')  
                                                        ->modalButton('Simpan');             
                                                })
                                        ])
                                        ->createOptionAction(function ($action) {
                                                    return $action
                                                        ->label('Tambah Anggota Baru')         
                                                        ->modalHeading('Form Anggota Baru')  
                                                        ->modalButton('Simpan');             
                                        }),
                                    Forms\Components\Select::make('jabatan_id')
                                        ->label('Pilih Jabatan')
                                        ->relationship('jabatans', 'jabatan')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('jabatan')
                                                ->label('Jabatan')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('keterangan')
                                                ->label('Keterangan')
                                                ->maxLength(255),
                                        ])
                                        ->createOptionAction(function ($action) {
                                            return $action
                                                ->label('Tambah Jabatan Karyawan Baru')         
                                                ->modalHeading('Form Jabatan Karyawan Baru')  
                                                ->modalButton('Simpan');             
                                        }),
                                    Forms\Components\Textarea::make('keterangan')
                                        ->label('Keterangan'),
                                ])
                                ->createOptionAction(function ($action) {
                                    return $action
                                        ->label('Tambah Karyawan Baru')         
                                        ->modalHeading('Form Karyawan Baru')  
                                        ->modalButton('Simpan');             
                                }),
                            
                            Forms\Components\DatePicker::make('tanggal_terdaftar')
                                ->label('Tanggal Terdaftar')
                                ->default(Now())
                                ->required(),

                            Forms\Components\Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->columnSpan(3),

                        
                    ])
                    ->columns(3)
                    ->columnSpan('full'),
                        ])
                        ->createOptionAction(function ($action) {
                            return $action
                            ->label('Tambah Group Kolektor Baru')         
                            ->modalHeading('Form Group Kolektor Baru')  
                            ->modalButton('Simpan');             
                    }),
                    Forms\Components\Select::make('member_id')
                    ->label('Anggota')
                    ->relationship('members', 'nama_lengkap')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\DatePicker::make('tanggal_bergabung')
                                ->label('Tanggal Bergabung')
                                ->default(Now()),

                            Forms\Components\TextInput::make('nia')
                                ->label('NIA (No. Induk Anggota)')
                                ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT))
                                ->required(),

                            Forms\Components\TextInput::make('nik')
                                ->label('NIK (No. Induk Kependudukan)'),

                            Forms\Components\TextInput::make('nama_lengkap')
                                ->label('Nama Lengkap')
                                ->required()
                                ->columnspan(2)
                                ->maxLength(255),

                            Forms\Components\TextInput::make('tempat_lahir')
                                ->label('Tempat Lahir')
                                ->maxLength(255),

                            Forms\Components\DatePicker::make('tanggal_lahir')
                                ->label('Tanggal Lahir'),

                            Forms\Components\Textarea::make('alamat')
                                ->label('Alamat')
                                ->rows(3)
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('no_hp')
                                ->label('No. Handphone')
                                ->tel(),

                            Forms\Components\Select::make('jenis_id')
                                ->label('Jenis')
                                ->relationship('jenis', 'jenis')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('jenis')
                                        ->label('Jenis')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('keterangan')
                                        ->label('Keterangan')
                                        ->maxLength(255),
                                ])
                                ->createOptionAction(function ($action) {
                                    return $action
                                        ->label('Tambah Jenis Anggota Baru')         
                                        ->modalHeading('Form Jenis Anggota Baru')  
                                        ->modalButton('Simpan');             
                                })
                        ])
                ->columns(2)
                ->columnSpan('full')
                    ])
                    ->createOptionAction(function ($action) {
                            return $action
                            ->label('Tambah Anggota Baru')         
                            ->modalHeading('Form Anggota Baru')  
                            ->modalButton('Simpan');  
                    }),
                    // Forms\Components\TextInput::make('saldo')
                    // ->required()
                    // ->default('0')
                    // ->disabled()
                    // ->numeric(),
                    Forms\Components\Textarea::make('keterangan')
                    ->required()
                    ->columnSpanFull(),
                ])
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rek')
                    ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('groups.group')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_terdaftar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('members.nama_lengkap')
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('saldo')
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.'))
                    ->default('0')
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
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('no_rek')
    //                 ->label('No. Rek')
    //                 ->formatStateUsing(fn ($state, $record) => 
    //                     str_pad($state, 5, '0', STR_PAD_LEFT) . '/' . ($record->groups->group ?? '-')
    //                 )
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('tanggal_terdaftar')
    //                 ->date()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('members.nama_lengkap')
    //                 ->label('Nama Anggota')
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('saldo')
    //                 ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.'))
    //                 ->sortable(),
    //         ]);
    // }


    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSukarelas::route('/'),
            'create' => Pages\CreateSukarela::route('/create'),
            'edit' => Pages\EditSukarela::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
