<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KreditHarianTransaksiResource\Pages;
use App\Filament\Resources\KreditHarianTransaksiResource\RelationManagers;
use App\Models\KreditHarianTransaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\KreditHarian;



class KreditHarianTransaksiResource extends Resource
{
    protected static ?string $model = KreditHarianTransaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $navigationGroup = 'Pinjaman';

    protected static ?string $modelLabel = 'Transaksi Pinjaman Harian';
    protected static ?string $pluralModelLabel = 'Transaksi Pinjaman Harian';
    protected static ?string $title = 'Transaksi Pinjaman Harian';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Repeater::make('items')
                ->label('Transaksi Kredit Harian')
                ->schema([
                    Forms\Components\Select::make('kredit_harian_id')
                        ->label('Nomor Kredit')
                        ->relationship(
                            name: 'kreditHarian',
                            titleAttribute: 'no_pokok',
                            modifyQueryUsing: fn ($query) =>
                                $query->where('sisa_pokok', '>', 0)
                        )
                        ->searchable(['no_pokok', 'nama_lengkap'])
                        ->getOptionLabelFromRecordUsing(fn ($record) => 
                            $record->no_pokok . ' — ' . $record->nama_lengkap
                        )
                        ->required(),

                    Forms\Components\TextInput::make('jumlah')
                        ->numeric()
                        ->required()
                        ->prefix('Rp'),

                    Forms\Components\DateTimePicker::make('tanggal_transaksi')
                        ->default(now())
                        ->readonly()
                        ->required(),

                ])
                ->columns(3)
                ->defaultItems(1)
                ->addActionLabel('Tambah Transaksi')
                ->reorderable(false)
                ->required(),
        ])
        ->columns(0);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         //
        //     ])
        //     ->filters([
        //         //
        //     ])
        //     ->actions([
        //         Tables\Actions\EditAction::make(),
        //     ])
        //     ->bulkActions([
        //         Tables\Actions\BulkActionGroup::make([
        //             Tables\Actions\DeleteBulkAction::make(),
        //         ]),
        //     ]);

        return $table
        ->modifyQueryUsing(function (Builder $query) {
            $query->whereDate('tanggal_transaksi', Carbon::today());
        })
        ->columns([
            Tables\Columns\TextColumn::make('tanggal_transaksi')->datetime(),
            Tables\Columns\TextColumn::make('no_pokok')
                    ->label('No Pokok')
                    ->getStateUsing(function ($record) {
                        // Ambil nomor urut dari database, misal "1"
                        $nomor = str_pad($record->kreditHarian->no_pokok, 5, '0', STR_PAD_LEFT);

                        // KGSH fixed
                        $kode = 'KGSH';

                        // Bulan romawi dari tanggal_pengajuan
                        $bulan = Carbon::parse($record->tanggal_pengajuan)->format('m');
                        $romawi = [
                            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
                            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
                            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII',
                        ][$bulan];

                        // Tahun dari tanggal_pengajuan
                        $tahun = Carbon::parse($record->tanggal_pengajuan)->format('Y');

                        return "$nomor/$kode/$romawi/$tahun";
                    }),
            Tables\Columns\TextColumn::make('kreditHarian.nama_lengkap'),
            Tables\Columns\TextColumn::make('jumlah')->money('IDR'),
            Tables\Columns\TextColumn::make('sisa_pokok')
                ->label('Sisa Pokok')
                ->getStateUsing(function ($record) {

                    $kredit = $record->kreditHarian;

                    if (! $kredit) {
                        return 0;
                    }

                    // HITUNG TOTAL KREDIT LANGSUNG
                    $plafond = (float) $kredit->plafond;
                    $bunga   = (float) $kredit->bunga_persen;
                    $admin  = (float) $kredit->admin_persen;

                    $totalKredit = $plafond + ($plafond * ($bunga + $admin) / 100);

                    // HITUNG TOTAL BAYAR SAMPAI TRANSAKSI INI (PAKAI ID)
                    $totalBayar = \App\Models\KreditHarianTransaksi::query()
                        ->where('kredit_harian_id', $record->kredit_harian_id)
                        ->where('id', '<=', $record->id)
                        ->sum('jumlah');

                    return max(0, $totalKredit - $totalBayar);
                })
                ->money('idr', true),

        ])
        ->defaultSort('tanggal_transaksi', 'asc');
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
            'index' => Pages\ListKreditHarianTransaksis::route('/'),
            'create' => Pages\CreateKreditHarianTransaksi::route('/create'),
            // 'edit' => Pages\EditKreditHarianTransaksi::route('/{record}/edit'),
        ];
    }

    
}
