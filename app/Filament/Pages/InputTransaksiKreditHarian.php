<?php

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\KreditHarian;
use App\Models\KreditHarianTransaksi;
use Illuminate\Support\Facades\DB;

class InputTransaksiKreditHarian extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationLabel = 'Transaksi Kredit Harian';
    protected static ?string $navigationGroup = 'Kredit Harian';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;



    public ?array $data = [];

    protected float $saldoAwal = 0;

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kredit_harian_id')
                    ->label('Nomor Kredit')
                    ->options(
                        KreditHarian::pluck('no_pokok', 'id')
                    )
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->setSaldoAwal($state);
                        $this->hitungUlangSisa();
                    }),

                Forms\Components\Placeholder::make('saldo_awal')
                    ->label('Sisa Pokok Saat Ini')
                    ->content(fn () => 'Rp ' . number_format($this->saldoAwal, 0, ',', '.')),

                Forms\Components\Repeater::make('transaksis')
                    ->label('Daftar Transaksi')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_transaksi')
                            ->default(now())
                            ->required(),

                        Forms\Components\TextInput::make('jumlah')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->hitungUlangSisa()),

                        Forms\Components\TextInput::make('sisa_pokok_preview')
                            ->label('Sisa Pokok')
                            ->disabled()
                            ->prefix('Rp'),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Tambah Transaksi')
                    ->columns(3)
                    ->reactive(),
            ])
            ->statePath('data');
    }

    protected function setSaldoAwal($kreditId): void
    {
        $kredit = KreditHarian::find($kreditId);

        if (! $kredit) {
            $this->saldoAwal = 0;
            return;
        }

        $this->saldoAwal =
            $kredit->transaksis()
                ->latest('tanggal_transaksi')
                ->value('sisa_pokok')
            ?? $kredit->plafond;
    }

    protected function hitungUlangSisa(): void
    {
        if (empty($this->data['transaksis'])) {
            return;
        }

        $sisa = $this->saldoAwal;

        foreach ($this->data['transaksis'] as $index => $trx) {
            $jumlah = floatval($trx['jumlah'] ?? 0);

            $sisa -= $jumlah;

            $this->data['transaksis'][$index]['sisa_pokok_preview'] =
                number_format(max($sisa, 0), 0, ',', '.');
        }
    }

    public function simpan()
    {
        DB::transaction(function () {
            $kredit = KreditHarian::findOrFail($this->data['kredit_harian_id']);

            $sisa =
                $kredit->transaksis()
                    ->latest('tanggal_transaksi')
                    ->value('sisa_pokok')
                ?? $kredit->plafond;

            foreach ($this->data['transaksis'] as $trx) {
                $sisa -= $trx['jumlah'];

                KreditHarianTransaksi::create([
                    'kredit_harian_id' => $kredit->id,
                    'tanggal_transaksi' => $trx['tanggal_transaksi'],
                    'jumlah' => $trx['jumlah'],
                    'sisa_pokok' => max($sisa, 0),
                ]);
            }
        });

        $this->notify('success', 'Transaksi berhasil disimpan');
        $this->form->fill();
    }
}
