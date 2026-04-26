<?php

namespace App\Filament\Widgets;

use App\Models\Member;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

class StatistikAnggotaWidget extends BaseWidget
{
    public static function canView(): bool
    {
        return Auth::user()?->hasRole('super_admin') ?? false;
    }
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    protected function getStats(): array
    {
        $anggotaBiasa = $this->countByJenis('Anggota Biasa');
        $anggotaTambahan = $this->countByJenis('Anggota Tambahan');
        $anggotaPendiri = $this->countByJenis('Anggota Pendiri');
        $nonAnggota = $this->countByJenis('Non Anggota');

        $simpananBiasa = $this->totalSimpananPokokByJenis('Anggota Biasa');

        $simpananTambahan =
            $this->totalSimpananPokokByJenis('Anggota Tambahan') +
            $this->totalSimpananPenyertaByJenis('Anggota Tambahan') +
            $this->totalSimpananWajibByJenis('Anggota Tambahan');

        $simpananPendiri =
            $this->totalSimpananPokokByJenis('Anggota Pendiri') +
            $this->totalSimpananPenyertaByJenis('Anggota Pendiri') +
            $this->totalSimpananWajibByJenis('Anggota Pendiri');

        return [
            Stat::make('Anggota Biasa', number_format($anggotaBiasa, 0, ',', '.'))
                ->description($this->descriptionTotalSimpanan($simpananBiasa))
                ->chart($this->getChartData('Anggota Biasa'))
                ->color('info')
                ->extraAttributes([
                    'class' => 'stat-anggota stat-anggota-info',
                ]),

            Stat::make('Anggota Tambahan', number_format($anggotaTambahan, 0, ',', '.'))
                ->description($this->descriptionTotalSimpanan($simpananTambahan))
                ->chart($this->getChartData('Anggota Tambahan'))
                ->color('warning')
                ->extraAttributes([
                    'class' => 'stat-anggota stat-anggota-warning',
                ]),

            Stat::make('Anggota Pendiri', number_format($anggotaPendiri, 0, ',', '.'))
                ->description($this->descriptionTotalSimpanan($simpananPendiri))
                ->chart($this->getChartData('Anggota Pendiri'))
                ->color('success')
                ->extraAttributes([
                    'class' => 'stat-anggota stat-anggota-success',
                ]),

            Stat::make('Non Anggota', number_format($nonAnggota, 0, ',', '.'))
                ->description($this->descriptionTotalSimpanan(0))
                ->chart($this->getChartData('Non Anggota'))
                ->color('danger')
                ->extraAttributes([
                    'class' => 'stat-anggota stat-anggota-danger',
                ]),
        ];
    }

    private function countByJenis(string $jenis): int
    {
        return Member::whereHas('jenis', function ($query) use ($jenis) {
            $query->where('jenis', $jenis);
        })->count();
    }

    private function getMemberIdsByJenis(string $jenis): array
    {
        return Member::whereHas('jenis', function ($query) use ($jenis) {
            $query->where('jenis', $jenis);
        })->pluck('id')->toArray();
    }

    private function totalSimpananPokokByJenis(string $jenis): int|float
    {
        $memberIds = $this->getMemberIdsByJenis($jenis);

        if (empty($memberIds)) {
            return 0;
        }

        return DB::table('trx_simpanan_pokoks')
            ->whereIn('member_id', $memberIds)
            ->sum('kredit');
    }

    private function totalSimpananPenyertaByJenis(string $jenis): int|float
    {
        $memberIds = $this->getMemberIdsByJenis($jenis);

        if (empty($memberIds)) {
            return 0;
        }

        return DB::table('trx_simpanan_penyertas')
            ->whereIn('member_id', $memberIds)
            ->sum('kredit');
    }

    private function totalSimpananWajibByJenis(string $jenis): int|float
    {
        $memberIds = $this->getMemberIdsByJenis($jenis);

        if (empty($memberIds)) {
            return 0;
        }

        return DB::table('trx_simpanan_wajibs')
            ->whereIn('member_id', $memberIds)
            ->sum('kredit');
    }

    private function getChartData(string $jenis): array
    {
        return collect(range(5, 0))
            ->map(function (int $month) use ($jenis) {
                $date = now()->subMonths($month);

                return Member::whereHas('jenis', function ($query) use ($jenis) {
                        $query->where('jenis', $jenis);
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();
            })
            ->toArray();
    }

    private function formatRupiah(int|float $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }
    
    private function descriptionWithBigIcon(int|float $nominal, string $colorClass): HtmlString
    {
        return new HtmlString('
            <div class="relative min-h-[58px] pr-16">
                <div class="flex flex-col gap-1">
                    <span>Total Simpanan</span>
                    <span class="font-semibold">' . $this->formatRupiah($nominal) . '</span>
                </div>

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="absolute right-1 top-[-40px] h-16 w-16 opacity-2 ' . $colorClass . '"
                >
                    <path d="M2.25 6.75A2.25 2.25 0 0 1 4.5 4.5h15a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75Zm3.75 1.5a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5H6Zm0 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5H6Zm8.25 3a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" />
                </svg>
            </div>
        ');
    }
   private function descriptionTotalSimpanan(int|float $nominal): HtmlString
    {
        return new HtmlString('
            <div class="flex flex-col gap-1">
                <span>Total Simpanan</span>
                <span class="font-semibold">' . $this->formatRupiah($nominal) . '</span>
            </div>
        ');
    }
    private function statCardAttributes(string $color): array
    {
        $svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='{$color}'%3E%3Cpath d='M2.25 6.75A2.25 2.25 0 0 1 4.5 4.5h15a2.25 2.25 0 0 1 2.25 2.25v10.5A2.25 2.25 0 0 1 19.5 19.5h-15a2.25 2.25 0 0 1-2.25-2.25V6.75Zm3.75 1.5a.75.75 0 0 0 0 1.5h12a.75.75 0 0 0 0-1.5H6Zm0 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5H6Zm8.25 3a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z'/%3E%3C/svg%3E";

        return [
            'style' => "
                background-image: url(\"{$svg}\");
                background-repeat: no-repeat;
                background-position: right 1.5rem top 1.5rem;
                background-size: 64px 64px;
            ",
        ];
    }
}