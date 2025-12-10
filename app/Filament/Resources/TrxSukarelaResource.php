<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrxSukarelaResource\Pages;
use App\Filament\Resources\TrxSukarelaResource\RelationManagers;
use App\Models\TrxSukarela;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrxSukarelaResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return false; // tidak muncul di menu
    }
    protected static ?string $model = TrxSukarela::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Simpanan Sukarela';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $pluralModelLabel = 'Transaksi';
    protected static ?string $title = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Section::make('Transaksi Simpanan Sukarela')
            ->description('Lengkapi transaksi simpanan sukarela')
            ->schema([
                Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Select::make('sukarela_id')
                    ->required()
                    ->label('No. Rekening')
                    ->relationship('sukarela', 'no_rek')
                    ->searchable()
                    ->preload()
                    // ->getOptionLabelFromRecordUsing(fn ($record) => $record->members->nama_lengkap ?? 'Tanpa Nama')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $noRek = str_pad($record->no_rek, 5, '0', STR_PAD_LEFT);
                        $group = $record->groups->group ?? 'Tanpa Group';
                        $nama = $record->members->nama_lengkap ?? 'Tanpa Nama';
                        return "{$noRek}/{$group} - {$nama}";
                    })
                    ->createOptionForm([
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
                                Forms\Components\Textarea::make('keterangan')
                                ->required()
                                ->columnSpanFull(),
                            ])
                        ])
                    ]),
                    Forms\Components\DatePicker::make('tanggal_trx')
                    ->label('Tanggal Transaksi')
                    ->default(Now())
                    ->required(),
                    Forms\Components\TextInput::make('debit')
                    ->numeric(),
                    Forms\Components\TextInput::make('kredit')
                    ->numeric(),
                    Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull()
                ])
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sukarela.no_rek_with_group')
                    ->label('No.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sukarela.members.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_trx')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kredit')
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
                Tables\Filters\Filter::make('hari_ini')
                ->label('Hari Ini')
                ->query(fn (Builder $query): Builder => $query->whereDate('tanggal_trx', now()->toDateString()))
                ->default(), // aktifkan filter secara default
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
            'index' => Pages\ListTrxSukarelas::route('/'),
            'create' => Pages\CreateTrxSukarela::route('/create'),
            'edit' => Pages\EditTrxSukarela::route('/{record}/edit'),
        ];
    }
}
