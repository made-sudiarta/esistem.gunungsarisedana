<?php

namespace App\Filament\Resources\SimpananBerjangkaResource\Pages;

use App\Filament\Resources\SimpananBerjangkaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
use Filament\Facades\Filament;
use Filament\Forms\Components\Placeholder;

class ListSimpananBerjangkas extends ListRecords
{
    public string $filterMode = 'all';


    protected static string $resource = SimpananBerjangkaResource::class;

    
    // public function mount(): void
    // {
    //     parent::mount();

    //     // Ambil parameter dari URL
    //     $status = request()->get('status');

    //     // Set filter mode berdasarkan Stat Card
    //     $this->filterMode = match($status) {
    //         'jatuh_tempo' => 'jatuh_tempo',
    //         'tenggat_bunga' => 'bunga',
    //         default => 'all'
    //     };

    //     // refresh data
    //     $this->refreshRecords();
    // }
    public function mount(): void
    {
        parent::mount();

        // Ambil parameter dari URL
        $status = request()->get('status');

        // Set filter mode berdasarkan Stat Card
        $this->filterMode = match ($status) {
            'jatuh_tempo'   => 'jatuh_tempo',
            'tenggat_bunga' => 'bunga',
            default         => 'all',
        };
    }



    // public function refreshRecords()
    // {
    //     $today = Carbon::today();

    //     $this->records = SimpananBerjangka::get()->filter(function ($item) use ($today) {

    //         // FILTER 1: JATUH TEMPO BULAN & TAHUN SAMA
    //         if ($this->filterMode === 'jatuh_tempo') {
    //             $jatuh = Carbon::parse($item->tanggal_masuk)->addMonths($item->jangka_waktu);
    //             return $jatuh->month === $today->month && $jatuh->year === $today->year;
    //         }

    //         // FILTER 2: BUNGA HARUS DICETAK (HARI SAMA)
    //         if ($this->filterMode === 'bunga') {
    //             $jatuh = Carbon::parse($item->tanggal_masuk)->addMonths($item->jangka_waktu);
    //             return $jatuh->day === $today->day;
    //         }

    //         // FILTER 3: SEMUA DATA
    //         return true;
    //     });
    // }
    // protected function getTableQuery(): ?Builder
    // {
    //     // Jika records belum di-refresh
    //     if (!isset($this->records)) {
    //         $this->refreshRecords();
    //     }

    //     // Ambil semua ID hasil filter
    //     $ids = $this->records->pluck('id');

    //     // Kembalikan query untuk tabel
    //     return SimpananBerjangka::query()
    //         ->whereIn('id', $ids);


    //     // FILTER LOGIN 
    //     $query = parent::getTableQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //     ]);

    //     $user = Filament::auth()->user();

    //     // Super Admin → lihat semua
    //     if ($user->hasRole('super_admin')) {
    //         return $query;
    //     }

    //     // Kolektor → hanya data group miliknya
    //     return $query->whereHas('group', function (Builder $q) use ($user) {
    //         $q->where('user_id', $user->id);
    //     });
    // }

    protected function getTableQuery(): Builder
{
    $query = parent::getTableQuery()
        ->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);

    $user = Filament::auth()->user();
    $today = Carbon::today();

    // 🔐 FILTER ROLE
    if (! $user->hasRole('super_admin')) {
        $query->whereHas('group', function (Builder $q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    // 📌 FILTER MODE (STAT CARD)
    if ($this->filterMode === 'jatuh_tempo') {
        $query->whereRaw("
            MONTH(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
            AND YEAR(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
        ", [$today->month, $today->year]);
    }

    if ($this->filterMode === 'bunga') {
        $query->whereRaw("
            DAY(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
        ", [$today->day]);
    }

    return $query;
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
                })
            ->visible(fn () =>
                Filament::auth()->user()?->hasRole('super_admin')
            ),
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
                ->openUrlInNewTab()
                ->visible(fn () =>
                    Filament::auth()->user()?->hasRole('super_admin')
                ),
            Actions\Action::make('cetakStruk')
                ->label('Struk Hari ini')
                ->icon('heroicon-o-printer')
                ->url(fn ($record) => route('simpanan-berjangka.print-struk'))
                ->openUrlInNewTab()
                ->visible(fn () =>
                    Filament::auth()->user()?->hasRole('super_admin')
                ),
        ];
    }
    protected function getTableRecordUrlUsing(): ?\Closure
    {
        return fn ($record) => route('cetak-struk', ['id' => $record->id]);
    }

}
