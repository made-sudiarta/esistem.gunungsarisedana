<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select\CreateOptionAction;
use Filament\Tables\Actions\Action;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationLabel = 'Anggota Koperasi';
    protected static ?string $navigationGroup = 'Keanggotaan';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Anggota Koperasi';
    protected static ?string $pluralModelLabel = 'Anggota Koperasi';
    protected static ?string $title = 'Anggota Koperasi';

    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Section::make('Member / Anggota Koperasi')
                ->description('Lengkapi data anggota secara lengkap dan benar.')
                ->schema([
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

                            Select::make('jenis_id')
                                ->label('Jenis')
                                ->relationship('jenis', 'jenis')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->createOptionForm([
                                    TextInput::make('jenis')
                                        ->label('Jenis')
                                        ->required()
                                        ->maxLength(255),
                                    TextInput::make('keterangan')
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
                ])
                ->columns(2)
                ->columnSpan('full'),
        ]);
    }
    public static function getViewFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Informasi Anggota')
                ->description('🧾 Data ini sudah diverifikasi dan tidak bisa diubah di halaman ini.') // ⬅️ Deskripsi khusus view
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('nia')
                                ->label('NIA (No. Induk Anggota)')
                                ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT))
                                ->disabled(),

                            Forms\Components\TextInput::make('nik')
                                ->label('NIK (No. Induk Kependudukan)')
                                ->disabled(),

                            Forms\Components\TextInput::make('nama_lengkap')
                                ->label('Nama Lengkap')
                                ->columnspan(2)
                                ->disabled(),

                            Forms\Components\TextInput::make('tempat_lahir')
                                ->label('Tempat Lahir')
                                ->disabled(),

                            Forms\Components\DatePicker::make('tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->disabled(),

                            Forms\Components\Textarea::make('alamat')
                                ->label('Alamat')
                                ->disabled()
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('no_hp')
                                ->label('No. Handphone')
                                ->disabled(),

                            Forms\Components\TextInput::make('jenis.jenis')
                                ->label('Jenis')
                                ->disabled(),
                        ]),
                ])
                ->columns(2),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nia', 'asc')
            ->columns([
                // Tables\Columns\TextColumn::make('id')
                //     ->searchable()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('nia')
                    ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_bergabung')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jenis.jenis')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('saldoPokok')
                    ->label('Simpanan Anggota')
                    ->getStateUsing(function ($record) {
                        $debitPokok = $record->trxSimpananPokoks()->sum('debit');
                        $kreditPokok = $record->trxSimpananPokoks()->sum('kredit');
                        $saldoPokok = $kreditPokok - $debitPokok;

                        $debitPenyerta = $record->trxSimpananPenyertas()->sum('debit');
                        $kreditPenyerta = $record->trxSimpananPenyertas()->sum('kredit');
                        $saldoPenyerta = $kreditPenyerta - $debitPenyerta;

                        $debitWajib = $record->trxSimpananWajibs()->sum('debit');
                        $kreditWajib = $record->trxSimpananWajibs()->sum('kredit');
                        $saldoWajib = $kreditWajib - $debitWajib;

                        $saldo = $saldoPokok + $saldoPenyerta + $saldoWajib;
                        return $saldo;
                    })
                    ->formatStateUsing(fn ($state) => 'Rp' . number_format($state, 0, ',', '.'))
                    ->sortable(),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_id')
                    ->label('Jenis Anggota')
                    ->relationship('jenis', 'jenis')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Action::make('view')
                    ->label('Detail')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.members.view', $record)),
                // Action::make('idcard')
                //     ->label('ID Card')
                //     ->icon('heroicon-o-credit-card')
                //     ->url(fn ($record) => route('filament.admin.resources.members.idcard', ['record' => $record->id]))
                //     ->openUrlInNewTab(), 
                Action::make('Cetak')
                    ->icon('heroicon-o-credit-card')
                    ->label('ID Card')
                    ->url(fn ($record) => route('print.member', ['record' => $record]))
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->can('trx_simpanan_pokok')),

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
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
            'view' => Pages\ViewMember::route('/{record}/view'),
            'idcard' => Pages\IdCardMember::route('/{record}/idcard'),
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
