<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratTagihanKreditResource\Pages;
use App\Models\KreditBulanan;
use App\Models\SuratTagihanKredit;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SuratTagihanKreditResource extends Resource
{
    protected static ?string $model = SuratTagihanKredit::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Surat Tagihan Kredit';
    protected static ?string $navigationGroup = 'Pinjaman';

    protected static ?string $modelLabel = 'Surat Tagihan Kredit';
    protected static ?string $pluralModelLabel = 'Surat Tagihan Kredit';
    protected static ?string $title = 'Surat Tagihan Kredit';

    public static function toNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (float) preg_replace('/[^0-9]/', '', (string) $value);
    }

    protected static function formatRupiah($value): string
    {
        return number_format((float) $value, 0, ',', '.');
    }

    protected static function getRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
            9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $romans[$month] ?? '';
    }

    

    protected static function getEligibleKreditOptions(): array
    {
        $user = Filament::auth()->user();

        $query = KreditBulanan::query()
            ->with(['member', 'group', 'transaksis']);

        if (! $user->hasRole('super_admin')) {
            $query->whereHas('group', function (Builder $q) use ($user) {
                $q->where('employee_id', $user->employee_id ?? $user->id);
            });
        }

        return $query
            ->get()
            ->filter(function ($item) {
                if ($item->status === 'lunas') {
                    return false;
                }

                return ((int) $item->jumlah_tunggakan > 0) || ((float) ($item->sisa_tunggakan_bunga ?? 0) > 0);
            })
            ->mapWithKeys(function ($item) {
                $nama = $item->member->nama_lengkap ?? '-';
                $group = $item->group->group ?? '-';
                $tunggakan = (int) $item->jumlah_tunggakan;
                $sisaBunga = number_format((float) ($item->sisa_tunggakan_bunga ?? 0), 0, ',', '.');

                $label = "{$item->no_pokok} - {$nama} | {$group} | {$tunggakan}x | Sisa bunga Rp {$sisaBunga}";

                return [$item->id => $label];
            })
            ->toArray();
    }

    protected static function fillKreditData(?int $kreditId, Set $set): void
    {
        if (! $kreditId) {
            $set('no_pokok', null);
            $set('jumlah_tunggakan_bunga', 0);
            $set('sisa_tunggakan_bunga', 0);
            $set('bunga_per_bulan', 0);
            $set('total_tunggakan_bunga', 0);
            $set('sisa_pokok_kredit', 0);
            $set('tanggal_jatuh_tempo', null);
            return;
        }

        $kredit = KreditBulanan::with(['member', 'group'])->find($kreditId);

        if (! $kredit) {
            return;
        }

        $jumlahTunggakan = (int) $kredit->jumlah_tunggakan;
        $sisaTunggakanBunga = (float) ($kredit->sisa_tunggakan_bunga ?? 0);
        $bungaPerBulan = (float) $kredit->getBungaPerBulanTagihan();
        $sisaPokok = (float) $kredit->getSisaSaldo();
        $totalTunggakanBunga = ($jumlahTunggakan * $bungaPerBulan) + $sisaTunggakanBunga;

        $set('no_pokok', $kredit->no_pokok);
        $set('jumlah_tunggakan_bunga', $jumlahTunggakan);
        $set('sisa_tunggakan_bunga', round($sisaTunggakanBunga, 0));
        $set('bunga_per_bulan', round($bungaPerBulan, 0));
        $set('total_tunggakan_bunga', round($totalTunggakanBunga, 0));
        $set('sisa_pokok_kredit', round($sisaPokok, 0));
        $set('tanggal_jatuh_tempo', optional($kredit->tanggal_jatuh_tempo)?->format('Y-m-d'));
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Filament::auth()->user();

        $query = parent::getEloquentQuery()->with(['kreditBulanan.member', 'kreditBulanan.group']);

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        return $query->whereHas('kreditBulanan.group', function (Builder $q) use ($user) {
            $q->where('employee_id', $user->employee_id ?? $user->id);
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Surat')
                    ->schema([
                        Select::make('jenis_sp')
                            ->label('Jenis SP')
                            ->options([
                                'SP1' => 'SP 1',
                                'SP2' => 'SP 2',
                                'SP3' => 'SP 3',
                            ])
                            ->required()
                            ->native(false)
                            ->disabled(fn (?Model $record) => $record?->status_surat === 'cetak'),

                        DatePicker::make('tanggal_surat')
                            ->label('Tanggal Surat')
                            ->default(now())
                            ->required()
                            ->disabled(fn (?Model $record) => $record?->status_surat === 'cetak'),

                        TextInput::make('nomor_surat')
                            ->label('Nomor Surat')
                            ->readOnly()
                            ->dehydrated(fn (?Model $record) => filled($record))
                            ->placeholder('Akan dibuat otomatis saat disimpan')
                            ->visible(fn (?Model $record) => filled($record)),

                        Placeholder::make('nomor_surat_info')
                            ->label('Nomor Surat')
                            ->content(fn (?Model $record) => $record?->nomor_surat ?: 'Akan dibuat otomatis saat data disimpan')
                            ->visible(fn (?Model $record) => blank($record)),

                        Select::make('status_surat')
                            ->label('Status Surat')
                            ->options([
                                'draft' => 'Draft',
                                'terbit' => 'Terbit',
                                'cetak' => 'Cetak',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false)
                            ->disabled(),

                        Actions::make([
                            FormAction::make('cetakSurat')
                                ->label('Cetak Surat')
                                ->icon('heroicon-o-printer')
                                ->color('success')
                                ->url(fn (?Model $record) => $record ? route('surat-tagihan-kredit.pdf', ['record' => $record]) : null, shouldOpenInNewTab: true)
                                ->visible(fn (?Model $record) => filled($record)),
                        ])->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Data Kredit')
                    ->schema([
                        Select::make('kredit_bulanan_id')
                            ->label('Pilih Kredit')
                            ->options(fn () => static::getEligibleKreditOptions())
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required()
                            ->live()
                            ->disabled(fn (?Model $record) => filled($record))
                            ->afterStateUpdated(function ($state, Set $set) {
                                static::fillKreditData($state, $set);
                            }),

                        TextInput::make('no_pokok')
                            ->label('No Pokok')
                            ->readOnly()
                            ->dehydrated()
                            ->required(),

                        TextInput::make('jumlah_tunggakan_bunga')
                            ->label('Jumlah Tunggakan Bunga (bulan)')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->default(0)
                            ->required(),

                        TextInput::make('sisa_tunggakan_bunga')
                            ->label('Sisa Tunggakan Bunga')
                            ->prefix('Rp')
                            ->readOnly()
                            ->dehydrated()
                            ->default(0)
                            ->formatStateUsing(fn ($state) => filled($state) ? static::formatRupiah($state) : '0')
                            ->dehydrateStateUsing(fn ($state) => static::toNumber($state))
                            ->required(),

                        TextInput::make('bunga_per_bulan')
                            ->label('Bunga Per Bulan')
                            ->prefix('Rp')
                            ->readOnly()
                            ->dehydrated()
                            ->default(0)
                            ->formatStateUsing(fn ($state) => filled($state) ? static::formatRupiah($state) : '0')
                            ->dehydrateStateUsing(fn ($state) => static::toNumber($state))
                            ->required(),

                        TextInput::make('total_tunggakan_bunga')
                            ->label('Total Tunggakan Bunga')
                            ->prefix('Rp')
                            ->readOnly()
                            ->dehydrated()
                            ->default(0)
                            ->formatStateUsing(fn ($state) => filled($state) ? static::formatRupiah($state) : '0')
                            ->dehydrateStateUsing(fn ($state) => static::toNumber($state))
                            ->required(),

                        TextInput::make('sisa_pokok_kredit')
                            ->label('Sisa Pokok Kredit')
                            ->prefix('Rp')
                            ->readOnly()
                            ->dehydrated()
                            ->default(0)
                            ->formatStateUsing(fn ($state) => filled($state) ? static::formatRupiah($state) : '0')
                            ->dehydrateStateUsing(fn ($state) => static::toNumber($state))
                            ->required(),

                        DatePicker::make('tanggal_jatuh_tempo')
                            ->label('Tanggal Jatuh Tempo')
                            ->readOnly()
                            ->dehydrated()
                            ->required(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(fn (?Model $record) => $record?->status_surat === 'cetak'),

                        Placeholder::make('catatan_info')
                            ->label('Info')
                            ->content('Dropdown hanya menampilkan kredit aktif yang memiliki tunggakan atau sisa tunggakan bunga.'),

                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_surat', 'desc')
            ->columns([
                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jenis_sp')
                    ->label('Jenis SP')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'SP1' => 'gray',
                        'SP2' => 'warning',
                        'SP3' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('no_pokok')
                    ->label('No Pokok')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kreditBulanan.member.nama_lengkap')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kreditBulanan.group.group')
                    ->label('Group')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jumlah_tunggakan_bunga')
                    ->label('Tunggakan')
                    ->suffix(' x')
                    ->badge()
                    ->color(fn ($state) => (int) $state > 0 ? 'danger' : 'success'),

                TextColumn::make('total_tunggakan_bunga')
                    ->label('Total Tunggakan Bunga')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('sisa_pokok_kredit')
                    ->label('Sisa Pokok')
                    ->money('idr', true)
                    ->sortable(),

                TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date()
                    ->sortable(),

                TextColumn::make('status_surat')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft' => 'gray',
                        'terbit' => 'warning',
                        'cetak' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_sp')
                    ->label('Jenis SP')
                    ->options([
                        'SP1' => 'SP 1',
                        'SP2' => 'SP 2',
                        'SP3' => 'SP 3',
                    ]),

                Tables\Filters\SelectFilter::make('status_surat')
                    ->label('Status Surat')
                    ->options([
                        'draft' => 'Draft',
                        'terbit' => 'Terbit',
                        'cetak' => 'Cetak',
                    ]),

                Tables\Filters\TrashedFilter::make()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('terbitkan')
                    ->label('Terbitkan')
                    ->icon('heroicon-o-check-badge')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status_surat === 'draft')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status_surat' => 'terbit',
                        ]);

                        Notification::make()
                            ->title('Surat berhasil diterbitkan.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cetak')
                    ->label('Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn ($record) => route('surat-tagihan-kredit.pdf', ['record' => $record]))
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratTagihanKredits::route('/'),
            'create' => Pages\CreateSuratTagihanKredit::route('/create'),
            'view' => Pages\ViewSuratTagihanKredit::route('/{record}'),
            'edit' => Pages\EditSuratTagihanKredit::route('/{record}/edit'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return Filament::auth()->user()?->hasRole('super_admin') ?? false;
    }
}