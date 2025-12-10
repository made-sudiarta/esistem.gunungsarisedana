<?php
namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;

class ViewMember extends ViewRecord implements HasForms
{
    protected static string $resource = MemberResource::class;

    protected function getAllTransactions()
    {
        $member = $this->record;

        $pokok = $member->trxSimpananPokoks()
            ->select('tanggal_trx', 'debit', 'kredit', 'keterangan')
            ->get();

        $penyerta = $member->trxSimpananPenyertas()
            ->select('tanggal_trx', 'debit', 'kredit', 'keterangan')
            ->get();

        $wajib = $member->trxSimpananWajibs()
            ->select('tanggal_trx', 'debit', 'kredit', 'keterangan')
            ->get();

        // Gabungkan dan urutkan semua transaksi
        return $pokok->merge($penyerta)->merge($wajib)->sortBy('tanggal_trx')->values();
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil')
                ->label('Ubah Anggota'),
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('print.members', ['member' => $this->record->id]))
                ->openUrlInNewTab(),
            ];
            
    }

    // 🔥 Tambahkan ini untuk override tampilan form di halaman View
    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema());
    }


    protected function mutateFormDataBeforeFill(array $data): array
    {
        $member = $this->record;

        $data['total_debitPokok'] = $member->trxSimpananPokoks()->sum('debit');
        $data['total_kreditPokok'] = $member->trxSimpananPokoks()->sum('kredit');
        $data['saldoPokok'] = $data['total_kreditPokok'] - $data['total_debitPokok'];

        $data['total_debitPenyerta'] = $member->trxSimpananPenyertas()->sum('debit');
        $data['total_kreditPenyerta'] = $member->trxSimpananPenyertas()->sum('kredit');
        $data['saldoPenyerta'] = $data['total_kreditPenyerta'] - $data['total_debitPenyerta'];

        $data['total_debitWajib'] = $member->trxSimpananWajibs()->sum('debit');
        $data['total_kreditWajib'] = $member->trxSimpananWajibs()->sum('kredit');
        $data['saldoWajib'] = $data['total_kreditWajib'] - $data['total_debitWajib'];


        return $data;
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Member / Anggota Koperasi')
                ->description('Informasi Lengkap Anggota')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\DatePicker::make('tanggal_bergabung')
                                ->label('Tanggal Bergabung')
                                ->disabled(),
                            Forms\Components\TextInput::make('nia')
                                ->label('NIA (No. Induk Anggota)')
                                ->formatStateUsing(fn ($state) => str_pad($state, 5, '0', STR_PAD_LEFT))
                                ->disabled(),

                            Forms\Components\TextInput::make('nik')
                                ->label('NIK (No. Induk Kependudukan)')
                                ->disabled(),

                            Forms\Components\TextInput::make('nama_lengkap')
                                ->label('Nama Lengkap')
                                ->columnSpan(2)
                                ->disabled(),

                            Forms\Components\TextInput::make('tempat_lahir')
                                ->label('Tempat Lahir')
                                ->disabled(),

                            Forms\Components\DatePicker::make('tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->disabled(),

                            Forms\Components\Textarea::make('alamat')
                                ->label('Alamat')
                                ->columnSpan(2)
                                ->disabled(),

                            Forms\Components\TextInput::make('no_hp')
                                ->label('No. Handphone')
                                ->disabled(),

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
                        ]),

                        
                ]),
                Forms\Components\Section::make('Simpanan Anggota')
                ->description('Informasi Simpanan Anggota')
                ->schema([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\TextInput::make('saldoPokok')
                                ->label('Simpanan Pokok')
                                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                ->prefix('Rp')
                                ->disabled(),
                            Forms\Components\TextInput::make('saldoPenyerta')
                                ->label('Simpanan Penyerta')
                                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                ->prefix('Rp')
                                ->disabled(),
                            Forms\Components\TextInput::make('saldoWajib')
                                ->label('Simpanan Wajib')
                                ->formatStateUsing(fn ($state) => number_format($state, 0, ',', '.'))
                                ->prefix('Rp')
                                ->disabled(),
                        ]),
                        Forms\Components\Placeholder::make('trx_table')
                        ->label('Riwayat Transaksi Simpanan')
                        ->content(function () {
                            $member = $this->record;

                            $pokok = $member->trxSimpananPokoks()
                                ->select('tanggal_trx', 'debit', 'kredit', 'keterangan')
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'tanggal_trx' => $item->tanggal_trx,
                                        'pokok' => ($item->kredit ?? 0) - ($item->debit ?? 0),
                                        'penyerta' => 0,
                                        'wajib' => 0,
                                        'keterangan' => $item->keterangan,
                                    ];
                                });

                            $penyerta = $member->trxSimpananPenyertas()
                                ->select('tanggal_trx', 'debit', 'kredit', 'keterangan')
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'tanggal_trx' => $item->tanggal_trx,
                                        'pokok' => 0,
                                        'penyerta' => ($item->kredit ?? 0) - ($item->debit ?? 0),
                                        'wajib' => 0,
                                        'keterangan' => $item->keterangan,
                                    ];
                                });

                            $wajib = $member->trxSimpananWajibs()
                                ->select('tanggal_trx', 'debit', 'kredit', 'keterangan')
                                ->get()
                                ->map(function ($item) {
                                    return [
                                        'tanggal_trx' => $item->tanggal_trx,
                                        'pokok' => 0,
                                        'penyerta' => 0,
                                        'wajib' => ($item->kredit ?? 0) - ($item->debit ?? 0),
                                        'keterangan' => $item->keterangan,
                                    ];
                                });

                            $all = collect($pokok)->merge($penyerta)->merge($wajib)->sortBy('tanggal_trx')->values();

                            $html = '<div style="overflow-x:auto;">
            <table style="width:100%; border-collapse: separate; border-spacing: 0; font-size: 14px; min-width:600px; border:1px solid #ddd; border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr>
                        <th style="border:1px solid #ddd; padding:6px; border-top-left-radius: 10px;">Tanggal</th>
                        <th style="border:1px solid #ddd; padding:6px;">Pokok</th>
                        <th style="border:1px solid #ddd; padding:6px;">Penyerta</th>
                        <th style="border:1px solid #ddd; padding:6px;">Wajib</th>
                        <th style="border:1px solid #ddd; padding:6px;">Saldo</th>
                        <th style="border:1px solid #ddd; padding:6px; border-top-right-radius: 10px;">Keterangan</th>
                    </tr>
                </thead>
                <tbody>';

$saldo = 0;
$lastIndex = count($all) - 1;

foreach ($all as $i => $trx) {
    $subtotal = $trx['pokok'] + $trx['penyerta'] + $trx['wajib'];
    $saldo += $subtotal;

    $bgColor = $i % 2 == 0 ? '#f9f9f9' : '#ffffff';

    // Sudut bawah hanya ditambahkan di baris terakhir
    $borderBottomLeft = $i === $lastIndex ? 'border-bottom-left-radius: 10px;' : '';
    $borderBottomRight = $i === $lastIndex ? 'border-bottom-right-radius: 10px;' : '';

    $html .= '<tr style="background-color:' . $bgColor . '; text-align:center;">';
    $html .= '<td style="border:1px solid #ddd; padding:6px;' . $borderBottomLeft . '">' . \Carbon\Carbon::parse($trx['tanggal_trx'])->format('d M Y') . '</td>';
    $html .= '<td style="border:1px solid #ddd; padding:6px;">' . number_format($trx['pokok'], 0, ',', '.') . '</td>';
    $html .= '<td style="border:1px solid #ddd; padding:6px;">' . number_format($trx['penyerta'], 0, ',', '.') . '</td>';
    $html .= '<td style="border:1px solid #ddd; padding:6px;">' . number_format($trx['wajib'], 0, ',', '.') . '</td>';
    $html .= '<td style="border:1px solid #ddd; padding:6px;">' . number_format($saldo, 0, ',', '.') . '</td>';
    $html .= '<td style="border:1px solid #ddd; padding:6px;' . $borderBottomRight . '">' . $trx['keterangan'] . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table></div>';



                            return new HtmlString($html);
                        }),
                    ]),

                Forms\Components\Section::make('Simpanan Sukarela')
                ->description('Informasi Simpanan Sukarela')
                ->schema([
                    Forms\Components\Placeholder::make('sukarelas')
                        ->label('Simpanan Sukarela')
                        ->content(function () {
                            $member = $this->record; // record member saat ini

                            // Ambil data sukarela milik member ini, bisa sesuaikan relasi di model Member
                            $sukarelas = $member->sukarelas()->get();

                            $html = '<div style="overflow-x:auto;">
                                <table style="width:100%; border-collapse: separate; border-spacing: 0; font-size: 14px; min-width:600px; border:1px solid #ddd; border-radius: 10px; overflow: hidden;">
                                    <thead>
                                        <tr>
                                            <th style="border:1px solid #ddd; padding:6px; border-top-left-radius: 10px;">No. Rek.</th>
                                            <th style="border:1px solid #ddd; padding:6px;">Tanggal Terdaftar</th>
                                            <th style="border:1px solid #ddd; padding:6px;">Saldo</th>
                                            <th style="border:1px solid #ddd; padding:6px; border-top-right-radius: 10px;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

                            $lastIndex = count($sukarelas) - 1;

                            foreach ($sukarelas as $i => $sukarela) {
                                $bgColor = $i % 2 == 0 ? '#f9f9f9' : '#ffffff';

                                $borderBottomLeft = $i === $lastIndex ? 'border-bottom-left-radius: 10px;' : '';
                                $borderBottomRight = $i === $lastIndex ? 'border-bottom-right-radius: 10px;' : '';

                                $html .= '<tr style="background-color:' . $bgColor . '; text-align:center;">';
                                $html .= '<td style="border:1px solid #ddd; padding:6px;' . $borderBottomLeft . '">' 
                                        . str_pad($sukarela->no_rek, 5, '0', STR_PAD_LEFT)  // buat leading zero, misal 127 jadi 00127
                                        . '/'
                                        . ($sukarela->groups ? $sukarela->groups->group : '-') // ambil kode group, jika ada
                                        . '</td>';

                                $html .= '<td style="border:1px solid #ddd; padding:6px;">' . \Carbon\Carbon::parse($sukarela->tanggal_terdaftar)->format('d M Y') . '</td>';
                                $html .= '<td style="border:1px solid #ddd; padding:6px;">' . number_format($sukarela->saldo, 0, ',', '.') . '</td>';
                                $html .= '<td style="border:1px solid #ddd; padding:6px;' . $borderBottomRight . '">' . ($sukarela->keterangan ?? '-') . '</td>';
                                $html .= '</tr>';
                            }

                            $html .= '</tbody></table></div>';

                            return new \Illuminate\Support\HtmlString($html);
                        }),
                ]),
        ];
        
    }
}

