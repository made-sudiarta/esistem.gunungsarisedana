<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiKreditBulananResource\Pages;
use App\Models\KreditBulanan;
use App\Models\TransaksiKreditBulanan;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\RawJs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;
use App\Filament\Resources\TransaksiKreditBulananResource\Widgets\TransaksiKreditBulananStats;

class TransaksiKreditBulananResource extends Resource
{
    protected static ?string $model = TransaksiKreditBulanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Transaksi Bulanan';
    protected static ?string $navigationGroup = 'Pinjaman';

    protected static ?string $modelLabel = 'Transaksi Pinjaman Bulanan';
    protected static ?string $pluralModelLabel = 'Transaksi Pinjaman Bulanan';
    protected static ?string $title = 'Transaksi Pinjaman Bulanan';

    public static function getWidgets(): array
    {
        return [
            TransaksiKreditBulananStats::class,
        ];
    }

    public static function toNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0;
        }

        return (float) preg_replace('/[^0-9]/', '', (string) $value);
    }

    protected static function roundUpHundreds(float $value): float
    {
        return $value <= 0 ? 0 : ceil($value / 100) * 100;
    }

    protected static function calculate($kredit, $saldo, $nominal, $bunga, $denda)
    {
        $saldo = static::toNumber($saldo);
        $nominal = static::toNumber($nominal);
        $bunga = static::toNumber($bunga);
        $denda = static::toNumber($denda);

        if ($bunga > $nominal) {
            $bunga = $nominal;
        }

        $pokok = max($nominal - $bunga - $denda, 0);
        $sisa = max($saldo - $pokok, 0);

        return [$bunga, $pokok, $sisa];
    }

    protected static function calculateTunggakan($kredit): int
    {
        if (! $kredit || $kredit->status === 'lunas') {
            return 0;
        }

        $currentMonth = now()->startOfMonth();

        $lastTransactionDate = $kredit->transaksis()
            ->latest('tanggal_transaksi')
            ->value('tanggal_transaksi');

        if ($lastTransactionDate) {
            $lastMonth = \Carbon\Carbon::parse($lastTransactionDate)->startOfMonth();

            return $lastMonth->greaterThanOrEqualTo($currentMonth)
                ? 0
                : $lastMonth->diffInMonths($currentMonth);
        }

        $startMonth = \Carbon\Carbon::parse($kredit->tanggal_pengajuan)->startOfMonth();

        return $startMonth->greaterThanOrEqualTo($currentMonth)
            ? 0
            : $startMonth->diffInMonths($currentMonth);
    }

    protected static function calculateBungaBulanIni($kredit): float
    {
        if (! $kredit) {
            return 0;
        }

        $bulanIni = now()->startOfMonth();

        // 🔥 cek apakah sudah bayar bulan ini
        $sudahBayarBulanIni = $kredit->transaksis()
            ->whereDate('tanggal_transaksi', '>=', $bulanIni)
            ->exists();

        if ($sudahBayarBulanIni) {
            return 0;
        }

        $sisaPokok = (float) $kredit->getSisaSaldo();
        $bungaPersen = (float) ($kredit->bunga_persen ?? 0);

        $bunga = ($sisaPokok * $bungaPersen) / 100;

        return ceil($bunga / 100) * 100;
    }
    
    public static function form(Form $form): Form
    {
        return $form->schema([

            DatePicker::make('tanggal_transaksi')
                ->default(now())
                ->required(),

            Repeater::make('items')
                ->label('Transaksi')
                ->schema([
                    Section::make('Ringkasan Pinjaman')
                        ->schema([
                            Grid::make(4)->schema([
                                Placeholder::make('info_sisa_pokok')
                                    ->label('Sisa Pokok')
                                    ->content(fn ($get) => 'Rp ' . number_format((float) ($get('sisa_pokok_info') ?? 0), 0, ',', '.')),

                                Placeholder::make('info_bunga_bulan_ini')
                                    ->label('Bunga Bulan Ini')
                                    ->content(fn ($get) => 'Rp ' . number_format((float) ($get('bunga_bulan_ini_info') ?? 0), 0, ',', '.')),

                                Placeholder::make('info_tunggakan_bulan')
                                    ->label('Tunggakan Bulan')
                                    ->content(fn ($get) => ((int) ($get('tunggakan_bulan_info') ?? 0)) . ' x'),
                                Placeholder::make('info_total_bunga_tunggak')
                                    ->label('Total Bunga Ditunggak')
                                    ->content(function ($get) {
                                        $bunga = (float) ($get('bunga_bulan_ini_info') ?? 0);
                                        $tunggakan = (int) ($get('tunggakan_bulan_info') ?? 0);

                                        if ($tunggakan === 0) {
                                            return 'Rp 0';
                                        }

                                        return 'Rp ' . number_format($bunga * $tunggakan, 0, ',', '.');
                                    }),
                            ]),
                            
                        ])
                        ->compact()
                        ->columnSpanFull(),

                    Grid::make(6)->schema([
                        Select::make('kredit_bulanan_id')
                            ->label('No Pokok')
                            ->options(function () {
                                return KreditBulanan::with('member')
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        $nama = $item->member->nama_lengkap ?? '-';
                                        return [$item->id => $item->no_pokok . ' - ' . $nama];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $kredit = KreditBulanan::find($state);

                                $saldo = $kredit?->getSisaSaldo() ?? 0;
                                $bungaBulanIni = static::calculateBungaBulanIni($kredit);
                                $tunggakanBulan = static::calculateTunggakan($kredit);

                                [$bunga, $pokok, $sisa] = static::calculate(
                                    $kredit,
                                    $saldo,
                                    $get('nominal_bayar'),
                                    $get('bunga'),
                                    $get('denda'),
                                );

                                $set('saldo_awal', $saldo);
                                $set('sisa_pokok_info', $saldo);
                                $set('bunga_bulan_ini_info', $bungaBulanIni);
                                $set('tunggakan_bulan_info', $tunggakanBulan);
                                $set('pokok', $pokok);
                                $set('sisa_saldo', $sisa);
                            })->columnSpan(2),

                        TextInput::make('nominal_bayar')
                            ->label('Nominal')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters([',', '.'])
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $kredit = KreditBulanan::find($get('kredit_bulanan_id'));

                                [$bunga, $pokok, $sisa] = static::calculate(
                                    $kredit,
                                    $get('saldo_awal'),
                                    $state,
                                    $get('bunga'),
                                    $get('denda'),
                                );

                                $set('pokok', $pokok);
                                $set('sisa_saldo', $sisa);
                            })->columnSpan(1),

                        TextInput::make('bunga')
                            ->label('Bunga')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters([',', '.'])
                            ->default(0)
                            ->live(onBlur: true)
                            ->rule(function (callable $get) {
                                return function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $nominalBayar = static::toNumber($get('nominal_bayar'));
                                    $bunga = static::toNumber($value);

                                    if ($bunga > $nominalBayar) {
                                        $fail('Bunga tidak boleh lebih besar dari nominal bayar.');
                                    }
                                };
                            })
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $kredit = KreditBulanan::find($get('kredit_bulanan_id'));

                                [$bunga, $pokok, $sisa] = static::calculate(
                                    $kredit,
                                    $get('saldo_awal'),
                                    $get('nominal_bayar'),
                                    $state,
                                    $get('denda'),
                                );

                                $set('pokok', $pokok);
                                $set('sisa_saldo', $sisa);
                            })->columnSpan(1),

                        TextInput::make('denda')
                            ->label('Denda')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters([',', '.'])
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                $kredit = KreditBulanan::find($get('kredit_bulanan_id'));

                                [$bunga, $pokok, $sisa] = static::calculate(
                                    $kredit,
                                    $get('saldo_awal'),
                                    $get('nominal_bayar'),
                                    $get('bunga'),
                                    $state,
                                );

                                $set('pokok', $pokok);
                                $set('sisa_saldo', $sisa);
                            })->columnSpan(1),

                        TextInput::make('pokok')
                            ->label('Pokok')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters([',', '.'])
                            ->default(0)
                            ->readOnly(),
                    ]),

                    TextInput::make('keterangan')
                        ->label('Keterangan')
                        ->columnSpanFull()->columnSpan(1),

                    TextInput::make('saldo_awal')
                        ->hidden()
                        ->dehydrated(true),

                    TextInput::make('sisa_saldo')
                        ->hidden()
                        ->dehydrated(true),

                    TextInput::make('sisa_pokok_info')
                        ->hidden()
                        ->dehydrated(false),

                    TextInput::make('bunga_bulan_ini_info')
                        ->hidden()
                        ->dehydrated(false),

                    TextInput::make('tunggakan_bulan_info')
                        ->hidden()
                        ->dehydrated(false),
                ])
                ->columns(1)
                ->defaultItems(1)
                ->addActionLabel('Tambah Baris')
                ->reorderable(false),
        ])
        ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal')
                    ->date(),

                TextColumn::make('kreditBulanan.no_pokok')
                    ->label('No Pokok'),

                TextColumn::make('kreditBulanan.member.nama_lengkap')
                    ->label('Nama Peminjam'),

                TextColumn::make('saldo_awal')
                    ->label('Saldo Awal')
                    ->money('idr'),

                TextColumn::make('pokok')
                    ->label('Pokok')
                    ->money('idr'),

                TextColumn::make('bunga')
                    ->label('Bunga')
                    ->money('idr'),

                TextColumn::make('denda')
                    ->label('Denda')
                    ->money('idr'),

                TextColumn::make('nominal_bayar')
                    ->label('Total Bayar')
                    ->money('idr'),

                TextColumn::make('sisa_saldo')
                    ->label('Sisa Saldo')
                    ->money('idr'),
            ])
            ->filters([
                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->default()
                    ->query(fn (Builder $query) => $query->whereDate('tanggal_transaksi', now())),

                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari'),
                        DatePicker::make('sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $dari = $data['dari'] ?? null;
                        $sampai = $data['sampai'] ?? null;

                        return $query
                            ->when($dari, fn (Builder $q) => $q->whereDate('tanggal_transaksi', '>=', $dari))
                            ->when($sampai, fn (Builder $q) => $q->whereDate('tanggal_transaksi', '<=', $sampai));
                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['kreditBulanan.member']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiKreditBulanans::route('/'),
            'create' => Pages\CreateTransaksiKreditBulanan::route('/create'),
            // 'edit' => Pages\EditTransaksiKreditBulanan::route('/{record}/edit'),
        ];
    }
}