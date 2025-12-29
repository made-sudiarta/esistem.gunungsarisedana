<?php

namespace App\Filament\Resources\SimpananBerjangkaResource\Pages;

use App\Filament\Resources\SimpananBerjangkaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SimpananBerjangkaImport;
use Carbon\Carbon;
use App\Models\SimpananBerjangka;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Placeholder;

class ListSimpananBerjangkas extends ListRecords
{
    public string $filterMode = 'all';


    protected static string $resource = SimpananBerjangkaResource::class;

    public function mount(): void
    {
        parent::mount();

        // Ambil parameter dari URL
        $status = request()->get('status');

        // Set filter mode berdasarkan Stat Card
        $this->filterMode = match($status) {
            'jatuh_tempo' => 'jatuh_tempo',
            'tenggat_bunga' => 'bunga',
            default => 'all'
        };

        // refresh data
        $this->refreshRecords();
    }


    public function refreshRecords()
    {
        $today = Carbon::today();

        $this->records = SimpananBerjangka::get()->filter(function ($item) use ($today) {

            // FILTER 1: JATUH TEMPO BULAN & TAHUN SAMA
            if ($this->filterMode === 'jatuh_tempo') {
                $jatuh = Carbon::parse($item->tanggal_masuk)->addMonths($item->jangka_waktu);
                return $jatuh->month === $today->month && $jatuh->year === $today->year;
            }

            // FILTER 2: BUNGA HARUS DICETAK (HARI SAMA)
            if ($this->filterMode === 'bunga') {
                $jatuh = Carbon::parse($item->tanggal_masuk)->addMonths($item->jangka_waktu);
                return $jatuh->day === $today->day;
            }

            // FILTER 3: SEMUA DATA
            return true;
        });
    }
    protected function getTableQuery(): ?Builder
    {
        // Jika records belum di-refresh
        if (!isset($this->records)) {
            $this->refreshRecords();
        }

        // Ambil semua ID hasil filter
        $ids = $this->records->pluck('id');

        // Kembalikan query untuk tabel
        return SimpananBerjangka::query()
            ->whereIn('id', $ids);
    }

    public function getTitle(): string
    {
        $status = request()->get('status');

        return match ($status) {
            'jatuh_tempo'   => 'Bilyet Jatuh Tempo Bulan Ini',
            'tenggat_bunga' => 'Cetak Bunga Hari Ini',
            default         => 'Simpanan Berjangka',
        };
    }





    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\SimpananBerjangkaResource\Widgets\SimpananBerjangkaOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Add Data'),

            Actions\Action::make('upload')
                ->label('Upload')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->modalHeading('Upload Data Simpanan Berjangka')
                ->modalSubheading('Unggah file Excel sesuai format template yang disediakan.')
                ->form([
                    Placeholder::make('download_template')
                        ->content(new HtmlString(
                            'Klik <a href="'.asset('templates/template-simpanan-berjangka.xlsx').'" target="_blank" class="text-blue-600 underline">di sini</a> untuk mendownload template Excel.'
                        )),
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('File Excel (.xlsx)')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->directory('simpanan-berjangka-import')
                        ->disk('public')
                        ->preserveFilenames()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $path = $data['file'];
                    $absolutePath = Storage::disk('public')->path($path);

                    Excel::import(new \App\Imports\SimpananBerjangkaImport, $absolutePath);

                    Notification::make()
                        ->title('Import berhasil!')
                        ->success()
                        ->send();
                }),

            // Actions\Action::make('upload')
            //     ->label('Upload')
            //     ->icon('heroicon-o-arrow-up-tray')
            //     ->color('info')
            //     ->modalHeading('Upload Data Simpanan Berjangka')
            //     ->modalSubheading('Unggah file Excel sesuai format template yang disediakan.')
            //     ->form([
            //         \Filament\Forms\Components\FileUpload::make('file')
            //             ->label('File Excel (.xlsx)')
            //             ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
            //             ->directory('simpanan-berjangka-import') // <-- WAJIB
            //             ->disk('public') // <-- WAJIB
            //             ->preserveFilenames() // opsional
            //             ->required(),
            //     ])
            //     ->action(function (array $data) {
            //         $path = $data['file']; // contoh: simpanan-berjangka-import/nama.xlsx

            //         $absolutePath = Storage::disk('public')->path($path);

            //         Excel::import(new \App\Imports\SimpananBerjangkaImport, $absolutePath);

            //         Notification::make()
            //             ->title('Import berhasil!')
            //             ->success()
            //             ->send();
            //     }),

            // Actions\Action::make('download-template')
            //     ->label('Template')
            //     ->icon('heroicon-o-document-arrow-down')
            //     ->color('success')
            //     ->url(asset('templates/template-simpanan-berjangka.xlsx'))
            //     ->openUrlInNewTab(),

            // Actions\Action::make('print')
            //     ->label('Print Data')
            //     ->icon('heroicon-o-printer')
            //     ->color('success')
            //     ->url(function () {

            //         // Ambil status dari URL (jatuh_tempo / tenggat_bunga / all)
            //         $status = request()->get('status', 'all');

            //         // Ambil filter table jika ada
            //         $filters = request()->get('tableFilters', []);

            //         // Kirim status + filters ke halaman cetak
            //         $query = http_build_query([
            //             'status' => $status,
            //             'filters' => $filters,
            //         ]);

            //         return route('print.simpanan-berjangka') . '?' . $query;
            //     })
            //     ->openUrlInNewTab(),


            Actions\Action::make('printData')
            ->label('Print Data')
            ->icon('heroicon-o-printer')
            ->color('success')
            ->url(function ($livewire) {
                // Ambil filter aktif
                $filters = $livewire->tableFilters ?? [];

                $query = http_build_query(['filters' => $filters]);

                return route('print.simpanan-berjangka.index') . '?' . $query;
            })
            ->openUrlInNewTab(),





            Actions\Action::make('cetakStruk')
                ->label('Struk Hari ini')
                ->icon('heroicon-o-printer')
                ->url(fn ($record) => route('simpanan-berjangka.print-struk'))
                ->openUrlInNewTab(),

            // Actions\Action::make('cetakPilihStruk')
            //     ->label('Print Struk')
            //     ->icon('heroicon-o-printer')
            //     ->form([
            //         DatePicker::make('tanggal_dari')->label('Dari Tanggal')->required(),
            //         DatePicker::make('tanggal_sampai')->label('Sampai Tanggal')->required(),
            //     ])
            //     ->modalSubmitActionLabel('Cetak')
            //     ->action(function (array $data, $livewire) {
            //         $url = route('cetak-pilih-struk', [
            //             'tanggal_dari' => $data['tanggal_dari'],
            //             'tanggal_sampai' => $data['tanggal_sampai'],
            //         ]);

            //         // 🔥 Ini cara yang benar di Filament 3
            //         $livewire->dispatch('open-new-tab', url: $url);
            //     }),

                

        ];
    }
    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('cetak-struk', ['id' => $record->id]);
    }

}
