<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratPenyerahanJaminanResource\Pages;
use App\Models\KreditBulanan;
use App\Models\SuratPenyerahanJaminan;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

use App\Models\KreditBulananFidusia;
use App\Models\KreditBulananJaminan;

class SuratPenyerahanJaminanResource extends Resource
{
    protected static ?string $model = SuratPenyerahanJaminan::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Penyerahan Jaminan';

    protected static ?string $modelLabel = 'Surat Penyerahan Jaminan';

    protected static ?string $pluralModelLabel = 'Surat Penyerahan Jaminan';

    protected static ?string $navigationGroup = 'Administrasi Kredit';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Data Surat')
                ->schema([
                    TextInput::make('nomor_surat')
                        ->label('Nomor Surat')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Dibuat otomatis saat disimpan'),

                    DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat')
                        ->default(now())
                        ->required(),

                    Select::make('status_surat')
                        ->label('Status')
                        ->options([
                            'draft' => 'Draft',
                            'terbit' => 'Terbit',
                            'cetak' => 'Cetak',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false),
                ])
                ->columns(3),

            Section::make('Data Kredit dan Anggota')
                ->schema([
                    Select::make('kredit_bulanan_id')
                        ->label('Pilih Kredit')
                        ->options(fn () => static::getKreditOptions())
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false)
                        ->live()
                        ->disabled(fn (?Model $record) => filled($record))
                        ->afterStateUpdated(function ($state, Set $set) {
                            $kredit = KreditBulanan::with([
                                'member',
                                'fidusias',
                            ])->find($state);

                            $set('no_pokok_info', $kredit?->no_pokok ?? '-');
                            $set(
                                'nama_anggota_info',
                                $kredit?->member?->nama_lengkap ?? '-'
                            );

                            $fidusia = $kredit?->fidusias?->first();

                            $set('jenis_kendaraan', 'Sepeda Motor');
                            $set(
                                'merek_tipe',
                                trim(($fidusia?->merk ?? '') . ' ' . ($fidusia?->type ?? ''))
                            );
                            $set('nomor_polisi', $fidusia?->no_polisi);
                            $set('nomor_rangka', $fidusia?->no_rangka);
                            $set('nomor_mesin', $fidusia?->no_mesin);
                            $set('nomor_bpkb', $fidusia?->no_bpkb);
                            $set('atas_nama_dokumen', $fidusia?->atasnama);

                            $set(
                                'catatan',
                                $fidusia
                                    ? 'Warna: ' . ($fidusia->warna ?? '-') .
                                    '; Tahun: ' . ($fidusia->tahun ?? '-')
                                    : null
                            );
                        }),

                    TextInput::make('no_pokok_info')
                        ->label('Nomor Pokok Kredit')
                        ->formatStateUsing(function ($state, ?SuratPenyerahanJaminan $record) {
                            return $record?->kreditBulanan?->no_pokok ?? $state ?? '-';
                        })
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('nama_anggota_info')
                        ->label('Nama Anggota')
                        ->formatStateUsing(function ($state, ?SuratPenyerahanJaminan $record) {
                            return $record?->kreditBulanan?->member?->nama_lengkap
                                ?? $state
                                ?? '-';
                        })
                        ->disabled()
                        ->dehydrated(false),
                ])
                ->columns(2),

            Section::make('Identitas Kendaraan')
                ->schema([
                    Select::make('jenis_kendaraan')
                        ->label('Jenis Kendaraan')
                        ->options([
                            'Sepeda Motor' => 'Sepeda Motor',
                            'Mobil' => 'Mobil',
                        ])
                        ->default('Sepeda Motor')
                        ->required()
                        ->native(false),

                    TextInput::make('merek_tipe')
                        ->label('Merek / Tipe'),

                    TextInput::make('nomor_polisi')
                        ->label('Nomor Polisi'),

                    TextInput::make('nomor_rangka')
                        ->label('Nomor Rangka'),

                    TextInput::make('nomor_mesin')
                        ->label('Nomor Mesin'),

                    TextInput::make('nomor_bpkb')
                        ->label('Nomor BPKB'),

                    TextInput::make('nomor_stnk')
                        ->label('Nomor STNK'),

                    TextInput::make('atas_nama_dokumen')
                        ->label('Nama pada Dokumen'),
                ])
                ->columns(2),

            Section::make('Serah Terima Kendaraan')
                ->schema([
                    DatePicker::make('tanggal_serah_terima')
                        ->label('Tanggal Serah Terima'),

                    TimePicker::make('waktu_serah_terima')
                        ->label('Waktu Serah Terima'),

                    Textarea::make('tempat_serah_terima')
                        ->label('Tempat Serah Terima')
                        ->rows(2)
                        ->columnSpanFull(),

                    Textarea::make('kondisi_kendaraan')
                        ->label('Kondisi Kendaraan')
                        ->placeholder('Contoh: kondisi bodi, mesin, ban, dan kerusakan yang terlihat')
                        ->rows(3)
                        ->columnSpanFull(),

                    Textarea::make('kelengkapan_diserahkan')
                        ->label('Kelengkapan yang Diserahkan')
                        ->placeholder('Contoh: 2 kunci, STNK asli, BPKB, dan dokumen lain yang benar-benar diterima')
                        ->rows(3)
                        ->columnSpanFull(),

                    TextInput::make('nama_penerima')
                        ->label('Nama Penerima dari Koperasi'),

                    TextInput::make('jabatan_penerima')
                        ->label('Jabatan Penerima'),

                    Textarea::make('catatan')
                        ->label('Catatan Tambahan')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    protected static function getKreditOptions(): array
    {
        return KreditBulanan::with('member')
            ->get()
            ->mapWithKeys(function ($kredit) {
                $member = $kredit->member;

                $nama = $member?->nama_lengkap ?? 'Anggota tanpa nama';

                return [
                    $kredit->id => "{$kredit->no_pokok} - {$nama}",
                ];
            })
            ->all();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_surat')
                    ->label('Nomor Surat')
                    ->searchable(),

                TextColumn::make('kreditBulanan.no_pokok')
                    ->label('No. Pokok')
                    ->searchable(),

                TextColumn::make('kreditBulanan.member.nama_lengkap')
                    ->label('Nama Anggota')
                    ->searchable(),

                TextColumn::make('nomor_polisi')
                    ->label('Nomor Polisi')
                    ->searchable(),

                TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('status_surat')
                    ->label('Status')
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('cetak_pernyataan')
                    ->label('Cetak Pernyataan')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->url(fn (SuratPenyerahanJaminan $record) =>
                        route('surat-penyerahan-jaminan.pernyataan', $record)
                    )
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('cetak_berita_acara')
                    ->label('Cetak Berita Acara')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn (SuratPenyerahanJaminan $record) =>
                        route('surat-penyerahan-jaminan.berita-acara', $record)
                    )
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratPenyerahanJaminans::route('/'),
            'create' => Pages\CreateSuratPenyerahanJaminan::route('/create'),
            'edit' => Pages\EditSuratPenyerahanJaminan::route('/{record}/edit'),
            'view' => Pages\ViewSuratPenyerahanJaminan::route('/{record}'),
        ];
    }
}