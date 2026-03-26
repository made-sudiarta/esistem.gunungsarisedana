<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KreditBulananResource\Pages;
use App\Filament\Resources\KreditBulananResource\RelationManagers\TransaksisRelationManager;
use App\Models\KreditBulanan;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\KreditBulananResource\Widgets\KreditBulananStats;

class KreditBulananResource extends Resource
{
    protected static ?string $model = KreditBulanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pinjaman Bulanan';
    protected static ?string $navigationGroup = 'Pinjaman';

    protected static ?string $modelLabel = 'Pinjaman Bulanan';
    protected static ?string $pluralModelLabel = 'Pinjaman Bulanan';
    protected static ?string $title = 'Pinjaman Bulanan';
    public static function getWidgets(): array
    {
        return [
            KreditBulananStats::class,
        ];
    }
    private static function rowColor($record): ?string
    {
        if ($record->status === 'lunas') {
            return 'success';
        }

        if ($record->status !== 'lunas' && $record->tanggal_jatuh_tempo && now()->greaterThan($record->tanggal_jatuh_tempo)) {
            return 'warning';
        }

        return null;
    }

    public static function getRelations(): array
    {
        return [
            TransaksisRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        $query = parent::getEloquentQuery();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        return $query->whereHas('group', function (Builder $q) use ($user) {
            $q->where('employee_id', $user->employee_id ?? $user->id);
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Peminjam')
                    ->schema([
                        Select::make('member_id')
                            ->relationship('member', 'nama_lengkap')
                            ->label('Anggota')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('group_id', null);
                            }),

                        Select::make('group_id')
                            ->label('Group / Kolektor')
                            ->relationship('group', 'group')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Data Penanggung Jawab')
                    ->relationship('penanggungJawab')
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK')
                            ->maxLength(50),

                        TextInput::make('nama')
                            ->label('Nama')
                            ->required(),

                        TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir'),

                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir'),

                        TextInput::make('pekerjaan')
                            ->label('Pekerjaan'),

                        TextInput::make('no_hp')
                            ->label('No HP')
                            ->tel(),

                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Data Pinjaman')
                    ->schema([
                        TextInput::make('no_pokok')
                            ->label('No Pokok')
                            ->required(),

                        TextInput::make('plafond')
                            ->label('Plafond')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        DatePicker::make('tanggal_pengajuan')
                            ->label('Tanggal Pengajuan')
                            ->default(now())
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $jangka = (int) ($get('jangka_waktu') ?? 0);

                                if ($state && $jangka > 0) {
                                    $set('tanggal_jatuh_tempo', Carbon::parse($state)->addMonths($jangka)->format('Y-m-d'));
                                }

                                static::recalculateLoan($set, $get);
                            }),

                        TextInput::make('jangka_waktu')
                            ->label('Jangka Waktu (bulan)')
                            ->numeric()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $tgl = $get('tanggal_pengajuan');

                                if ($tgl && $state) {
                                    $set('tanggal_jatuh_tempo', Carbon::parse($tgl)->addMonths((int) $state)->format('Y-m-d'));
                                }

                                static::recalculateLoan($set, $get);
                            }),

                        DatePicker::make('tanggal_jatuh_tempo')
                            ->label('Tanggal Jatuh Tempo')
                            ->required(),

                        TextInput::make('bunga_persen')
                            ->label('Bunga (%)')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        Textarea::make('tujuan_pinjaman')
                            ->label('Tujuan Pinjaman')
                            ->rows(2),

                        TextInput::make('biaya_adm_persen')
                            ->label('Biaya Adm (%)')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        TextInput::make('biaya_provisi_persen')
                            ->label('Biaya Provisi (%)')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        TextInput::make('biaya_op_persen')
                            ->label('Biaya OP (%)')
                            ->numeric()
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        TextInput::make('biaya_kyd')
                            ->label('Biaya KYD')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        TextInput::make('biaya_materai')
                            ->label('Biaya Materai')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        TextInput::make('biaya_asuransi')
                            ->label('Biaya Asuransi')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        TextInput::make('biaya_lain')
                            ->label('Biaya Lain')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::recalculateLoan($set, $get)),

                        Textarea::make('keterangan_biaya_lain')
                            ->label('Keterangan Biaya Lain')
                            ->rows(2),

                        

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'aktif' => 'Aktif',
                                'lunas' => 'Lunas',
                                'macet' => 'Macet',
                                'jatuh_tempo' => 'Jatuh Tempo',
                            ])
                            ->default('aktif')
                            ->required(),
                    ])
                    ->columns(3),

                Section::make('Data Jaminan')
                    ->schema([
                        Repeater::make('jaminans')
                            ->relationship()
                            ->label('Daftar Jaminan')
                            ->schema([
                                Textarea::make('keterangan_jaminan')
                                    ->label('Keterangan Jaminan')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),

                                Repeater::make('atasNamas')
                                    ->relationship()
                                    ->label('Atas Nama Jaminan')
                                    ->schema([
                                        TextInput::make('atas_nama')
                                            ->label('Atas Nama')
                                            ->required(),
                                    ])
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->columns(1)
                                    ->columnSpanFull(),

                                Fieldset::make('Data Fidusia')
                                    ->relationship('fidusia')
                                    ->schema([
                                        TextInput::make('merk')->label('Merk'),
                                        TextInput::make('type')->label('Type'),
                                        TextInput::make('warna')->label('Warna'),
                                        TextInput::make('tahun')->label('Tahun'),

                                        TextInput::make('no_rangka')->label('No Rangka'),
                                        TextInput::make('no_mesin')->label('No Mesin'),
                                        TextInput::make('no_polisi')->label('No Polisi'),
                                        TextInput::make('no_bpkb')->label('No BPKB'),

                                        TextInput::make('atasnama')->label('Atas Nama'),
                                        TextInput::make('taksiran_harga')
                                            ->label('Taksiran Harga')
                                            ->numeric()
                                            ->prefix('Rp'),

                                        Textarea::make('tempat_penyimpanan')
                                            ->label('Tempat Penyimpanan')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ])
                            ->defaultItems(1)
                            ->collapsible()
                            ->cloneable()
                            ->itemLabel(fn (array $state): ?string => $state['keterangan_jaminan'] ?? 'Jaminan')
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }

    protected static function recalculateLoan(callable $set, callable $get): void
    {
        $plafond = (float) ($get('plafond') ?? 0);
        $bunga = (float) ($get('bunga_persen') ?? 0);
        $adm = (float) ($get('biaya_adm_persen') ?? 0);
        $provisi = (float) ($get('biaya_provisi_persen') ?? 0);
        $op = (float) ($get('biaya_op_persen') ?? 0);

        $kyd = (float) ($get('biaya_kyd') ?? 0);
        $materai = (float) ($get('biaya_materai') ?? 0);
        $asuransi = (float) ($get('biaya_asuransi') ?? 0);
        $lain = (float) ($get('biaya_lain') ?? 0);

        $jangkaWaktu = (int) ($get('jangka_waktu') ?? 0);

        $nominalPersen = $plafond * ($adm + $provisi + $op) / 100;
        $totalTagihan = $nominalPersen + $materai + $kyd + $asuransi + $lain;
        $angsuranPerBulan = $jangkaWaktu > 0 ? ceil((($plafond*$bunga/100) + ($plafond/$jangkaWaktu))/1000)*1000 : 0;

        $set('total_tagihan', round($totalTagihan, 2));
        $set('angsuran_per_bulan', round($angsuranPerBulan, 2));
        $set('sisa_pokok', round($plafond, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('no_pokok', 'desc')
            ->columns([
                TextColumn::make('no')
                    ->label('No.')
                    ->rowIndex()
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('member.nia')
                    ->label('NIA')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return str_pad($record->member->nia ?? 0, 5, '0', STR_PAD_LEFT);
                    })
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('no_pokok')
                    ->label('No Pokok')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        $nomor = str_pad($record->no_pokok, 5, '0', STR_PAD_LEFT);
                        $kode = 'KGS';
                        $bulan = Carbon::parse($record->tanggal_pengajuan)->format('m');

                        $romawi = [
                            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
                            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
                            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII',
                        ][$bulan];

                        $tahun = Carbon::parse($record->tanggal_pengajuan)->format('Y');

                        return "{$nomor}/{$kode}/{$romawi}/{$tahun}";
                    })
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('group.group')
                    ->label('Group')
                    ->searchable()
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('member.nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('plafond')
                    ->label('Plafond')
                    ->money('idr', true)
                    ->sortable()
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('tanggal_jatuh_tempo')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('sisa_pokok')
                    ->label('Sisa Pokok')
                    ->money('idr', true)
                    ->getStateUsing(fn ($record) => $record->getSisaSaldo())
                    ->sortable()
                    ->color(fn ($record) => static::rowColor($record))
                    ->weight(fn ($record) => static::rowColor($record) ? 'medium' : 'normal'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function ($record) {
                        if ($record->status === 'lunas') {
                            return 'lunas';
                        }

                        if ($record->tanggal_jatuh_tempo && now()->greaterThan($record->tanggal_jatuh_tempo)) {
                            return 'jatuh_tempo';
                        }

                        return $record->status;
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'jatuh_tempo' => 'jatuh tempo',
                        default => $state,
                    })
                    ->color(fn (string $state) => match ($state) {
                        'lunas' => 'success',
                        'jatuh_tempo' => 'warning',
                        'macet' => 'danger',
                        default => 'gray',
                    })
                    ->weight('medium'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Group Kolektor')
                    ->relationship('group', 'group')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin')),

                Tables\Filters\TrashedFilter::make()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin')),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin')),
                Tables\Actions\Action::make('akad')
                ->label('Akad')
                ->icon('heroicon-o-document-text')
                ->color('success')
                ->url(fn ($record) => route('kredit-bulanan.akad-pdf', ['record' => $record]))
                ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKreditBulanans::route('/'),
            'create' => Pages\CreateKreditBulanan::route('/create'),
            'view' => Pages\ViewKreditBulanan::route('/{record}'),
            'edit' => Pages\EditKreditBulanan::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return $record->transaksis()->count() === 0 && $record->status === 'aktif';
    }

    public static function canDelete(Model $record): bool
    {
        return $record->transaksis()->count() === 0;
    }
}