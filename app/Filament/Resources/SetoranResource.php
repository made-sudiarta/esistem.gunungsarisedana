<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SetoranResource\Pages;
use App\Filament\Resources\SetoranResource\RelationManagers;
use App\Models\Setoran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Support\Facades\Auth;
use Filament\Facades\Filament;

class SetoranResource extends Resource
{
    public static function shouldRegisterNavigation(): bool
    {
        return false; // tidak muncul di menu
    }

    protected static ?string $model = Setoran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Setoran Kolektor';
    protected static ?string $pluralModelLabel = 'Setoran Kolektor';
    protected static ?string $title = 'Setoran Kolektor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal_trx')
                    ->label('Tanggal Transaksi')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('group_id')
                    ->label('Kolektor')
                    ->relationship('group', 'group')
                    ->required()
                    ->searchable()
                    ->preload()
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
                    })
                    ->visible(fn () => Filament::auth()->user()->hasRole('super_admin')),
                Forms\Components\Select::make('group_id')
                    ->label('Kolektor')
                    ->relationship(
                        name: 'group',
                        titleAttribute: 'group',
                        modifyQueryUsing: fn ($query) => 
                            Filament::auth()->user()->hasRole('super_admin')
                                ? $query // Super admin bisa akses semua group
                                : $query->where('user_id', Filament::auth()->id()) // Kolektor hanya miliknya
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn () => !Filament::auth()->user()->hasRole('super_admin')),

                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_trx')
                    ->date()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group.group')
                    ->label('Kolektor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('group.employees.members.nama_lengkap')
                    ->label('Petugas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_setoran')
                    ->label('Total Setoran')
                    ->getStateUsing(function ($record) {
                        return $record->setoranSukarelas()->sum('jumlah');
                    })
                    ->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        if($record->status == 0){
                            $status = 'Belum Diproses';
                        }else if($record->status == 1){
                            $status = 'Sudah Diproses';
                        }else{
                            $status = 'Tidak Ketahui';
                        }
                        return $status;
                    }),
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
                Tables\Filters\TrashedFilter::make()
                ->visible(fn () => Filament::auth()->user()->hasRole('super_admin')),
            ])
            ->actions([
                Tables\Actions\Action::make('proses_setoran')
                ->label('Setoran')
                ->icon('heroicon-o-cog')
                ->color('primary')
                ->url(fn ($record) => route('filament.admin.resources.setorans.detail', ['record' => $record])),

                Tables\Actions\EditAction::make()
                ->label(''),
                Tables\Actions\DeleteAction::make()
                ->label(''),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        // Jika Super Admin, tampilkan semua data
        if ($user->hasRole('super_admin')) {
            return parent::getEloquentQuery();
        }

        // Jika bukan super admin, hanya tampilkan Setoran milik group yang user ini pegang
        return parent::getEloquentQuery()->whereHas('group', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSetorans::route('/'),
            'create' => Pages\CreateSetoran::route('/create'),
            'edit' => Pages\EditSetoran::route('/{record}/edit'),
            'detail' => Pages\SetoranDetail::route('/{record}/detail'),
        ];
    }
}
