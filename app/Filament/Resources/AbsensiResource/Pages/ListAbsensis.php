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
        
                            Select::make('user_id')
                                ->label('Karyawan')
                                ->options(fn () => User::query()->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->visible(fn () => auth()->user()->hasRole('super_admin'))
                                ->required(fn () => auth()->user()->hasRole('super_admin')),
                        ])
                        ->action(function (array $data) {
                            $mulai   = Carbon::parse($data['tanggal_mulai'])->startOfDay();
                            $selesai = Carbon::parse($data['tanggal_selesai'])->endOfDay();
        
                            $query = Absensi::query()
                                ->whereBetween('tanggal', [$mulai, $selesai]);
        
                            // kalau bukan super_admin, pakai user login
                            if (! auth()->user()->hasRole('super_admin')) {
                                $query->where('user_id', auth()->id());
                                $userName = auth()->user()->name;
                            } else {
                                $query->where('user_id', $data['user_id']);
                                $userName = \App\Models\User::find($data['user_id'])?->name ?? '-';
                            }
        
                            $totalJam       = (float) $query->sum('jumlah_jam');
                            $totalSetoran   = (float) $query->sum('jumlah_setoran');
                            $totalPenarikan = (float) $query->sum('penarikan');
                            $totalBersih    = $totalSetoran - $totalPenarikan;
        
                            // ambil detail baris kalau mau ditampilkan di printout
                            $rows = $query->orderBy('tanggal')->get();
        
                            // === Opsi 1: PDF (DomPDF) ===
                            $pdf = \PDF::loadView('absensi.rekap', [
                                'userName'        => $userName,
                                'mulai'           => $mulai,
                                'selesai'         => $selesai,
                                'rows'            => $rows,
                                'totalJam'        => $totalJam,
                                'totalSetoran'    => $totalSetoran,
                                'totalPenarikan'  => $totalPenarikan,
                                'totalBersih'     => $totalBersih,
                            ])->setPaper('A4', 'portrait');
        
                            $namaFile = 'rekap-absensi-' . $mulai->format('Ymd') . '-' . $selesai->format('Ymd') . '.pdf';
        
                            return response()->streamDownload(fn () => print($pdf->output()), $namaFile);
                        }),
        ];
    }
}
