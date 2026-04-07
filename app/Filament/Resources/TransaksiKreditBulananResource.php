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

    public static function form(Form $form): Form
    {
        return $form->schema([

            DatePicker::make('tanggal_transaksi')
                ->default(now())
                ->required(),

            Repeater::make('items')
                ->label('Transaksi')
                ->schema([
                    Grid::make(4)->schema([
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

                                [$bunga, $pokok, $sisa] = static::calculate(
                                    $kredit,
                                    $saldo,
                                    $get('nominal_bayar'),
                                    $get('bunga'),
                                    $get('denda'),
                                );

                                $set('saldo_awal', $saldo);
                                $set('pokok', $pokok);
                                $set('sisa_saldo', $sisa);
                            }),

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
                            }),

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
                            }),

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
                            }),

                        TextInput::make('pokok')
                            ->label('Pokok')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters([',', '.'])
                            ->default(0)
                            ->live(onBlur: true)
                            ->readOnly(),

                        TextInput::make('saldo_awal')
                            ->label('Saldo')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters([',', '.'])
                            ->default(0)
                            ->live(onBlur: true)
                            ->readOnly(),

                        TextInput::make('sisa_saldo')
                            ->label('Sisa')
                            ->mask(RawJs::make('$money($input)'))
                            ->stripCharacters([',', '.'])
                            ->default(0)
                            ->live(onBlur: true)
                            ->readOnly(),

                        TextInput::make('keterangan')
                            ->label('Ket'),

                    ])
                ])
                ->columns(2)
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