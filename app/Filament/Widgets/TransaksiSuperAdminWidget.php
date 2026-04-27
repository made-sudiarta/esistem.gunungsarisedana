<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class TransaksiSuperAdminWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    protected function getStats(): array
    {
        $pinjamanBulananBunga = $this->sumHariIni('transaksi_kredit_bulanans', 'bunga');
        $pinjamanBulananPokok = $this->sumHariIni('transaksi_kredit_bulanans', 'pokok');
        $pinjamanBulananDenda = $this->sumHariIni('transaksi_kredit_bulanans', 'denda');

        $pinjamanBulananTotal =
            $pinjamanBulananBunga +
            $pinjamanBulananPokok +
            $pinjamanBulananDenda;

        $pinjamanHarianPokok = $this->sumHariIni('kredit_harian_transaksis', 'jumlah');

        $penyertaSetoran = $this->sumHariIni('trx_simpanan_penyertas', 'kredit');
        $penyertaPenarikan = $this->sumHariIni('trx_simpanan_penyertas', 'debit');

        $pokokSetoran = $this->sumHariIni('trx_simpanan_pokoks', 'kredit');
        $pokokPenarikan = $this->sumHariIni('trx_simpanan_pokoks', 'debit');

        $wajibSetoran = $this->sumHariIni('trx_simpanan_wajibs', 'kredit');
        $wajibPenarikan = $this->sumHariIni('trx_simpanan_wajibs', 'debit');

        $absensiSetoran = DB::table('absensis')
            ->whereNull('deleted_at')
            ->whereDate('tanggal', today()->toDateString())
            ->sum('jumlah_setoran');

        $absensiPenarikan = DB::table('absensis')
            ->whereNull('deleted_at')
            ->whereDate('tanggal', today()->toDateString())
            ->sum('penarikan');

        $absensiTotal = $absensiSetoran - $absensiPenarikan;

        return [
            Stat::make('Simpanan Penyerta', $this->formatRupiah($penyertaSetoran - $penyertaPenarikan))
                ->description($this->descriptionSetoranPenarikan($penyertaSetoran, $penyertaPenarikan))
                ->chart($this->getChartDataSetoranPenarikan('trx_simpanan_penyertas'))
                ->color('primary')
                ->extraAttributes($this->statWalletAttributes('#f59e0b')),

            Stat::make('Simpanan Pokok', $this->formatRupiah($pokokSetoran - $pokokPenarikan))
                ->description($this->descriptionSetoranPenarikan($pokokSetoran, $pokokPenarikan))
                ->chart($this->getChartDataSetoranPenarikan('trx_simpanan_pokoks'))
                ->color('primary')
                ->extraAttributes($this->statWalletAttributes('#84cc16')),

            Stat::make('Simpanan Wajib', $this->formatRupiah($wajibSetoran - $wajibPenarikan))
                ->description($this->descriptionSetoranPenarikan($wajibSetoran, $wajibPenarikan))
                ->chart($this->getChartDataSetoranPenarikan('trx_simpanan_wajibs'))
                ->color('primary')
                ->extraAttributes($this->statWalletAttributes('#ef4444')),

            Stat::make('Setoran Kolektor', $this->formatRupiah($absensiTotal))
                ->description($this->descriptionSetoranPenarikan($absensiSetoran, $absensiPenarikan))
                ->chart($this->getChartDataAbsensiKaryawan())
                ->color('info')
                ->extraAttributes($this->statWalletAttributes('#22c55e')),

            Stat::make('Pinjaman Bulanan', $this->formatRupiah($pinjamanBulananTotal))
                ->description($this->descriptionPinjamanBulanan(
                    bunga: $pinjamanBulananBunga,
                    pokok: $pinjamanBulananPokok,
                    denda: $pinjamanBulananDenda,
                ))
                ->chart($this->getChartDataPinjamanBulanan())
                ->color('warning')
                ->extraAttributes($this->statMoneyAttributes('#60a5fa')),

            Stat::make('Pinjaman Harian', $this->formatRupiah($pinjamanHarianPokok))
                ->description($this->descriptionPinjamanHarian($pinjamanHarianPokok))
                ->chart($this->getChartDataPinjamanHarian())
                ->color('warning')
                ->extraAttributes($this->statMoneyAttributes('#4ade80')),
        ];
    }

    private function sumHariIni(string $table, string $column): int|float
    {
        if (! DB::getSchemaBuilder()->hasTable($table)) {
            return 0;
        }

        if (! DB::getSchemaBuilder()->hasColumn($table, $column)) {
            return 0;
        }

        $tanggalColumn = $this->getTanggalColumn($table);

        $query = DB::table($table)
            ->whereDate($tanggalColumn, today()->toDateString());

        if (DB::getSchemaBuilder()->hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query->sum($column);
    }

    private function sumByDate(string $table, string $column, string $date): int|float
    {
        if (! DB::getSchemaBuilder()->hasTable($table)) {
            return 0;
        }

        if (! DB::getSchemaBuilder()->hasColumn($table, $column)) {
            return 0;
        }

        $tanggalColumn = $this->getTanggalColumn($table);

        $query = DB::table($table)
            ->whereDate($tanggalColumn, $date);

        if (DB::getSchemaBuilder()->hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        return $query->sum($column);
    }

    private function getTanggalColumn(string $table): string
    {
        return match ($table) {
            'transaksi_kredit_bulanans' => 'tanggal_transaksi',
            'kredit_harian_transaksis' => 'tanggal_transaksi',

            'trx_simpanan_penyertas',
            'trx_simpanan_pokoks',
            'trx_simpanan_wajibs' => 'tanggal_trx',

            default => 'created_at',
        };
    }

    private function getChartDataSetoranPenarikan(string $table): array
    {
        return collect(range(6, 0))
            ->map(function (int $day) use ($table) {
                $date = now()->subDays($day)->toDateString();

                $setoran = $this->sumByDate($table, 'kredit', $date);
                $penarikan = $this->sumByDate($table, 'debit', $date);

                return $setoran - $penarikan;
            })
            ->toArray();
    }

    private function getChartDataAbsensiKaryawan(): array
    {
        return collect(range(6, 0))
            ->map(function (int $day) {
                $date = now()->subDays($day)->toDateString();

                $setoran = DB::table('absensis')
                    ->whereNull('deleted_at')
                    ->whereDate('tanggal', $date)
                    ->sum('jumlah_setoran');

                $penarikan = DB::table('absensis')
                    ->whereNull('deleted_at')
                    ->whereDate('tanggal', $date)
                    ->sum('penarikan');

                return $setoran - $penarikan;
            })
            ->toArray();
    }

    private function getChartDataPinjamanBulanan(): array
    {
        return collect(range(6, 0))
            ->map(function (int $day) {
                $date = now()->subDays($day)->toDateString();

                return
                    $this->sumByDate('transaksi_kredit_bulanans', 'bunga', $date) +
                    $this->sumByDate('transaksi_kredit_bulanans', 'pokok', $date) +
                    $this->sumByDate('transaksi_kredit_bulanans', 'denda', $date);
            })
            ->toArray();
    }

    private function getChartDataPinjamanHarian(): array
    {
        return collect(range(6, 0))
            ->map(function (int $day) {
                $date = now()->subDays($day)->toDateString();

                return $this->sumByDate('kredit_harian_transaksis', 'jumlah', $date);
            })
            ->toArray();
    }

    private function formatRupiah(int|float $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }

    private function descriptionSetoranPenarikan(int|float $setoran, int|float $penarikan): HtmlString
    {
        return new HtmlString('
            <div class="flex flex-col gap-1 pr-16">
                <div class="flex items-center gap-5">
                    <span>Setoran &nbsp;</span>
                    <span class="font-semibold">' . $this->formatRupiah($setoran) . '</span>
                </div>

                <div class="flex items-center gap-5">
                    <span>Penarikan &nbsp;</span>
                    <span class="font-semibold">' . $this->formatRupiah($penarikan) . '</span>
                </div>
            </div>
        ');
    }

    private function descriptionPinjamanBulanan(
        int|float $bunga,
        int|float $pokok,
        int|float $denda,
    ): HtmlString {
        return new HtmlString('
            <div class="flex flex-col gap-1 pr-16">
                <div class="flex items-center gap-5">
                    <span>Bunga &nbsp;</span>
                    <span class="font-semibold">' . $this->formatRupiah($bunga) . '</span>
                </div>

                <div class="flex items-center gap-5">
                    <span>Pokok &nbsp;</span>
                    <span class="font-semibold">' . $this->formatRupiah($pokok) . '</span>
                </div>

                <div class="flex items-center gap-5">
                    <span>Denda &nbsp;</span>
                    <span class="font-semibold">' . $this->formatRupiah($denda) . '</span>
                </div>
            </div>
        ');
    }

    private function descriptionPinjamanHarian(int|float $pokok): HtmlString
    {
        return new HtmlString('
            <div class="flex flex-col gap-1 pr-16">
                <div class="flex items-center gap-5">
                    <span>Pokok &nbsp;</span>
                    <span class="font-semibold">' . $this->formatRupiah($pokok) . '</span>
                </div>
            </div>
        ');
    }

    private function statWalletAttributes(string $color): array
    {
        $svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='{$color}'%3E%3Cpath d='M2.25 6.75A2.25 2.25 0 0 1 4.5 4.5h15a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75Zm3.75 1.5a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5H6Zm0 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5H6Zm8.25 3a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z'/%3E%3C/svg%3E";

        return [
            'class' => 'stat-transaksi',
            'style' => "
                background-image: url(\"{$svg}\");
                background-repeat: no-repeat;
                background-position: right 2rem top 2rem;
                background-size: 64px 64px;
            ",
        ];
    }

    private function statMoneyAttributes(string $color): array
    {
        $svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='{$color}'%3E%3Cpath d='M12 7.5a4.5 4.5 0 1 0 0 9 4.5 4.5 0 0 0 0-9ZM1.5 6.75A2.25 2.25 0 0 1 3.75 4.5h16.5a2.25 2.25 0 0 1 2.25 2.25v10.5a2.25 2.25 0 0 1-2.25 2.25H3.75a2.25 2.25 0 0 1-2.25-2.25V6.75Zm2.25-.75a.75.75 0 0 0-.75.75v1.5A3.75 3.75 0 0 0 6.75 4.5h-3Zm16.5 0h-3a3.75 3.75 0 0 0 3.75 3.75v-1.5a.75.75 0 0 0-.75-.75Zm.75 9.75A3.75 3.75 0 0 0 17.25 19.5h3a.75.75 0 0 0 .75-.75v-3Zm-17.25 3.75h3A3.75 3.75 0 0 0 3 15.75v3a.75.75 0 0 0 .75.75Z'/%3E%3C/svg%3E";

        return [
            'class' => 'stat-transaksi',
            'style' => "
                background-image: url(\"{$svg}\");
                background-repeat: no-repeat;
                background-position: right 2rem top 2rem;
                background-size: 64px 64px;
            ",
        ];
    }
}