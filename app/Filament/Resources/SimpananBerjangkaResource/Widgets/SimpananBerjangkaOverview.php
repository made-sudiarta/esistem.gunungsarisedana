<?php

namespace App\Filament\Resources\SimpananBerjangkaResource\Widgets;

use App\Models\SimpananBerjangka;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

class SimpananBerjangkaOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();

        // 1. Bilyet Jatuh Tempo (tanggal_masuk + jangka_waktu bulan <= hari ini)
        $jatuhTempo = SimpananBerjangka::get()->filter(function ($item) use ($today) {
            $jatuh = Carbon::parse($item->tanggal_masuk)->addMonths((int) $item->jangka_waktu);
            // return $jatuh->lte($today);
            return $jatuh->month === $today->month && $jatuh->year === $today->year;
        })->count();

        // $tenggatBunga = SimpananBerjangka::get()->filter(function ($item) use ($today) {
        //     $jatuh = Carbon::parse($item->tanggal_masuk)
        //         ->addMonths((int) $item->jangka_waktu);

        //     // Cek tanggal (day) sama, abaikan bulan & tahun
        //     return $jatuh->day === $today->day;
        // })->sum('nominal');

        $tenggatBunga = SimpananBerjangka::get()
            ->filter(function ($item) use ($today) {
                $jatuh = Carbon::parse($item->tanggal_masuk)
                    ->addMonths((int) $item->jangka_waktu);

                // cek hari saja
                return $jatuh->day === $today->day;
            })
            ->sum(function ($item) {
                // hitung bunga per bulan
                return $item->nominal * ($item->bunga_persen / 100) / 12;
            });



        // 3. Total Bilyet Aktif
        $total = SimpananBerjangka::count();
        $nominal = SimpananBerjangka::sum('nominal');

        return [
            Stat::make('Bilyet Jatuh Tempo', $jatuhTempo)
                ->description('Klik untuk melihat data jatuh tempo')
                ->url('/admin/simpanan-berjangkas?status=jatuh_tempo')
                ->color('danger')
                ->icon('heroicon-o-clock'),

            Stat::make('Cetak Bunga Hari Ini', 'Rp. '.number_format($tenggatBunga))
                ->description('Klik untuk melihat bunga simpanan')
                ->url('/admin/simpanan-berjangkas?status=tenggat_bunga')
                ->color('warning')
                ->icon('heroicon-o-banknotes'),
                

            Stat::make('Jumlah Simpanan Berjangka', 'Rp. '.number_format($nominal))
                ->description('Klik untuk lihat semua data')
                ->url('/admin/simpanan-berjangkas')
                ->color('success')
                ->icon('heroicon-o-document-text'),
        ];
    }
}
