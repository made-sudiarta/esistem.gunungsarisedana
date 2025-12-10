<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    
    protected static ?string $navigationLabel = 'Kolektor';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Kolektor';
    protected static ?string $pluralModelLabel = 'Kolektor';
    protected static ?string $title = 'Kolektor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Kolektor Koperasi')
                ->description('Lengkapi data Kolektor Koperasi secara lengkap dan benar.')
                ->schema([
                    Forms\Components\Grid::make(2)
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

                                Forms\Components\Select::make('user_id')
                                    ->label('Pilih User')
                                    ->relationship('user', 'name')
                                    
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                    Forms\Components\Textarea::make('keterangan')
                                        ->label('Keterangan')
                                        ->columnSpan(2),
                                    ]),

                    ])
                ->columns(3)
                ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_terdaftar', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employees.members.nama_lengkap')
                    ->label('Nama Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_terdaftar')
                    ->label('Terdaftar')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
