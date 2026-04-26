<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Carbon\Carbon;
use App\Models\Absensi;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class ListAbsensis extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Absensi Baru')
                ->icon('heroicon-o-plus')
                ->visible(fn () => auth()->user()->hasRole('super_admin')),
            Actions\Action::make('rekapPrint')
                ->label('Rekap & Print')
                ->icon('heroicon-o-printer')
                ->form([
                    DatePicker::make('tanggal_mulai')
                        ->label('Dari Tanggal')
                        ->required()
                        ->default(now()->startOfMonth()),

                    DatePicker::make('tanggal_selesai')
                        ->label('Sampai Tanggal')
                        ->required()
                        ->default(now()),

                    Select::make('user_ids')
                        ->label('Karyawan')
                        ->multiple()
                        ->options(fn () => User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->preload()
                        ->visible(fn () => auth()->user()->hasRole('super_admin'))
                        ->required(fn () => auth()->user()->hasRole('super_admin')),
                ])
                ->action(function (array $data) {

                    $mulai   = Carbon::parse($data['tanggal_mulai'])->startOfDay();
                    $selesai = Carbon::parse($data['tanggal_selesai'])->endOfDay();

                    // non-superadmin: pakai user login saja (1 halaman)
                    if (! auth()->user()->hasRole('super_admin')) {
                        $userIds = [auth()->id()];
                    } else {
                        $userIds = $data['user_ids'] ?? [];
                    }

                    $users = User::query()
                        ->whereIn('id', $userIds)
                        ->orderBy('name')
                        ->get();

                    $rekap = $users->map(function ($user) use ($mulai, $selesai) {
                        $q = Absensi::query()
                            ->where('user_id', $user->id)
                            ->whereBetween('tanggal', [$mulai, $selesai]);

                        $rows = (clone $q)->orderBy('tanggal')->get();

                        $totalJam       = (float) (clone $q)->sum('jumlah_jam');
                        $totalSetoran   = (float) (clone $q)->sum('jumlah_setoran');
                        $totalPenarikan = (float) (clone $q)->sum('penarikan');

                        return [
                            'userName'       => $user->name,
                            'rows'           => $rows,
                            'totalJam'       => $totalJam,
                            'totalSetoran'   => $totalSetoran,
                            'totalPenarikan' => $totalPenarikan,
                            'totalBersih'    => $totalSetoran - $totalPenarikan,
                        ];
                    })->toArray();

                    $pdf = \PDF::loadView('absensi.rekap', [
                        'mulai'   => $mulai,
                        'selesai' => $selesai,
                        'rekap'   => $rekap,
                    ])->setPaper('A4', 'portrait');

                    $namaFile = 'rekap-absensi-' . $mulai->format('Ymd') . '-' . $selesai->format('Ymd') . '.pdf';

                    return response()->streamDownload(fn () => print($pdf->output()), $namaFile);
                }),
         ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\AbsensiResource\Widgets\AbsensiHarianStats::class,
        ];
    }
}
