<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationLabel = 'Karyawan';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Karyawan';
    protected static ?string $pluralModelLabel = 'Karyawan';
    protected static ?string $title = 'Karyawan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('Karyawan Koperasi')
                ->description('Lengkapi data Karyawan Koperasi secara lengkap dan benar.')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
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
                                        ->label('Keterangan')
                                        ->columnSpan(3),
                                
                        ])
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_terdaftar', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_terdaftar')
                    ->label('Terdaftar')
                    ->sortable(),
                Tables\Columns\TextColumn::make('members.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jabatans.jabatan')
                    ->label('Jabatan')
                    ->searchable()
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
