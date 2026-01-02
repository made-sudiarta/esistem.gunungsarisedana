<?php 

namespace App\Filament\Resources;

use App\Filament\Resources\KreditHarianResource\Pages;
use App\Models\KreditHarian;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
// use Filament\Forms\Components\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;



use App\Filament\Resources\KreditHarianResource\RelationManagers\TransaksisRelationManager;

class KreditHarianResource extends Resource
{
    protected static ?string $model = KreditHarian::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Pinjaman Harian';
    protected static ?string $navigationGroup = 'Pinjaman';

    protected static ?string $modelLabel = 'Pinjaman Harian';
    protected static ?string $pluralModelLabel = 'Pinjaman Harian';
    protected static ?string $title = 'Pinjaman Harian';


    
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

        // Super Admin → lihat semua
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Kolektor → hanya data group miliknya
        return $query->whereHas('group', function (Builder $q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Baris 1
                TextInput::make('no_pokok')
                    ->label('No Pokok')
                    ->required()
                    ->columnSpan(1),

                Select::make('member_id')
                    ->relationship('member', 'nama_lengkap')
                    ->label('Anggota')
                    ->searchable()
                    ->required()
                    ->columnSpan(1),

                Select::make('group_id')
                    ->label('Kolektor')
                    ->relationship('group', 'group')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpan(1),

                TextInput::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->required()
                    ->columnSpan(1),

                // Baris 2
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpan(2),

                TextInput::make('no_hp')
                    ->label('No HP')
                    ->tel()
                    ->columnSpan(1),

                // Baris 3
                DatePicker::make('tanggal_pengajuan')
                    ->label('Tanggal Pengajuan')
                    ->default(now())
                    ->required()
                    ->columnSpan(1),

                TextInput::make('jangka_waktu')
                    ->label('Jangka Waktu (hari)')
                    ->numeric()
                    ->required()
                    ->columnSpan(1),

                // DatePicker::make('tanggal_jatuhtempo')
                //     ->label('Tanggal Jatuh Tempo')
                //     ->required()
                //     ->default(function ($get) {
                //         // Hitung hanya saat create
                //         $tanggalPengajuan = $get('tanggal_pengajuan') ?? now();
                //         $jangkaWaktu = $get('jangka_waktu') ?? 0;

                //         return now()->parse($tanggalPengajuan)->addDays($jangkaWaktu);
                //     })
                //     ->disabled() // user tidak bisa edit manual
                //     ->columnSpan(1),

                

                TextInput::make('plafond')
                    ->label('Plafond')
                    ->numeric()
                    ->required()
                    ->columnSpan(1),

                TextInput::make('bunga_persen')
                    ->label('Bunga (%)')
                    ->numeric()
                    ->required()
                    ->columnSpan(1),

                TextInput::make('admin_persen')
                    ->label('Admin (%)')
                    ->numeric()
                    ->required()
                    ->columnSpan(1),

                TextInput::make('sisa_pokok_preview')
                    ->label('Total Tagihan (Plafond + Bunga + Admin)')
                    ->disabled()
                    ->dehydrated(false) // ⬅️ tidak dikirim ke backend
                    ->reactive()
                    ->afterStateHydrated(function ($state, callable $set, callable $get) {
                        $plafond = $get('plafond') ?? 0;
                        $bunga = $get('bunga_persen') ?? 0;
                        $admin = $get('admin_persen') ?? 0;

                        $total = $plafond + ($plafond * ($bunga + $admin) / 100);
                        $set('sisa_pokok_preview', number_format($total, 2));
                    })
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $plafond = $get('plafond') ?? 0;
                        $bunga = $get('bunga_persen') ?? 0;
                        $admin = $get('admin_persen') ?? 0;

                        $total = $plafond + ($plafond * ($bunga + $admin) / 100);
                        $set('sisa_pokok_preview', number_format($total, 2));
                    })
                    ->columnSpan(1),

            ])
            ->columns(3);

        }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')->label('No.')->rowIndex(),
                TextColumn::make('member.nia')->label('NIA')->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Ambil nomor urut dari database, misal "1"
                        $nia = str_pad($record->member->nia, 5, '0', STR_PAD_LEFT);
                        $jenis = $record->member->jenis->keterangan;
                        return "$nia/$jenis";
                    }),
                // TextColumn::make('no_pokok')->label('No Pokok'),
                TextColumn::make('no_pokok')
                    ->label('No Pokok')
                    ->getStateUsing(function ($record) {
                        // Ambil nomor urut dari database, misal "1"
                        $nomor = str_pad($record->no_pokok, 5, '0', STR_PAD_LEFT);

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
                TextColumn::make('group.group')->label('Group'),
                TextColumn::make('nama_lengkap')->label('Nama Lengkap')->searchable()
                    ->sortable(),
                TextColumn::make('sisa_pokok')
                    ->label('Sisa Pokok')
                    ->money('idr', true),


                TextColumn::make('bunga_persen')->suffix('%'),
                TextColumn::make('admin_persen')->suffix('%'),
                TextColumn::make('tanggal_pengajuan')->date(),
                TextColumn::make('jatuh_tempo')
                    ->label('Tanggal Jatuh Tempo')
                    ->getStateUsing(function ($record) {
                        return \Carbon\Carbon::parse($record->tanggal_pengajuan)
                            ->addDays($record->jangka_waktu)
                            ->format('M d, Y');
                    }),

                TextColumn::make('cicilan_harian')->label('Cicilan/Hari')->money('idr', true),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => $state === 'lunas' ? 'success' : 'warning'),

            ])
            ->filters([
                // bisa ditambah filter Member/Group/Tanggal
                Tables\Filters\SelectFilter::make('group_id')
                    ->label('Group Kolektor')
                    ->relationship('group', 'group')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->visible(fn () =>
                    Filament::auth()->user()?->hasRole('super_admin')
                ),
                Tables\Filters\TrashedFilter::make()->visible(fn () =>
                    Filament::auth()->user()?->hasRole('super_admin')
                ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->visible(fn () =>
                    Filament::auth()->user()?->hasRole('super_admin')
                ),
                Tables\Actions\DeleteAction::make()->visible(fn () =>
                    Filament::auth()->user()?->hasRole('super_admin')
                ),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => static::getUrl('print', ['record' => $record]))
                    ->openUrlInNewTab()
                    ->visible(fn () =>
                    Filament::auth()->user()?->hasRole('super_admin')
                ),
                    
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKreditHarians::route('/'),
            'create' => Pages\CreateKreditHarian::route('/create'),
            'view' => Pages\ViewKreditHarian::route('/{record}'),
            'edit' => Pages\EditKreditHarian::route('/{record}/edit'),
            'print' => Pages\PrintKreditHarian::route('/{record}/print'),
        ];
    }
    public static function canEdit(Model $record): bool
    {
        return $record->transaksis()->count() === 0
            && $record->status === 'aktif';
    }

    public static function canDelete(Model $record): bool
    {
        return $record->transaksis()->count() === 0;
    }

}
