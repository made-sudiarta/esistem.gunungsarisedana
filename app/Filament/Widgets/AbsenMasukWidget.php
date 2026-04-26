<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AbsenMasukWidget extends Widget
{
    protected static string $view = 'filament.widgets.absen-masuk-widget';

    protected static ?int $sort = 2;

    public string $todayStatus = 'not_absen';

    public bool $showKeluarModal = false;

    public ?int $jumlah_setoran = null;

    public ?int $penarikan = null;

    protected int | string | array $columnSpan = [
    'default' => 1,
    'md' => 1,
    'lg' => 1,
    'xl' => 1,

];

    public function mount(): void
    {
        $this->updateStatus();
    }

    public function updateStatus(): void
    {
        $absensi = Absensi::where('user_id', Auth::id())
            ->whereDate('tanggal', today())
            ->first();

        if (! $absensi) {
            $this->todayStatus = 'not_absen';
            return;
        }

        if (! $absensi->jam_keluar) {
            $this->todayStatus = 'masuk_done';
            return;
        }

        $this->todayStatus = 'completed';
    }

    public function absenMasuk(): void
    {
        $sudahAbsen = Absensi::where('user_id', Auth::id())
            ->whereDate('tanggal', today())
            ->exists();

        if ($sudahAbsen) {
            Notification::make()
                ->title('Kamu sudah absen hari ini!')
                ->warning()
                ->send();

            return;
        }

        $now = now();

        $jamMasuk = $now->lt(today()->setTime(8, 0))
            ? today()->setTime(8, 0)
            : $now;

        Absensi::create([
            'user_id' => Auth::id(),
            'tanggal' => today(),
            'jam_masuk' => $jamMasuk->format('H:i'),
        ]);

        $this->updateStatus();

        Notification::make()
            ->title('Absen Masuk berhasil!')
            ->success()
            ->send();
    }

    public function absenKeluar(): void
{
    $this->validate([
        'jumlah_setoran' => ['required', 'numeric', 'min:0'],
        'penarikan' => ['required', 'numeric', 'min:0'],
    ]);

    $absensi = Absensi::where('user_id', Auth::id())
        ->whereDate('tanggal', today())
        ->first();

    if (! $absensi || $absensi->jam_keluar) {
        Notification::make()
            ->title('Tidak ada absen masuk atau kamu sudah absen keluar!')
            ->warning()
            ->send();

        return;
    }

    $jamMasuk = Carbon::parse(today()->format('Y-m-d') . ' ' . $absensi->jam_masuk);

    $jamBatas = today()->setTime(16, 0);

    $jamKeluar = now()->greaterThan($jamBatas)
        ? $jamBatas
        : now();

    $absensi->update([
        'jam_keluar' => $jamKeluar->format('H:i'),
        'jumlah_jam' => round($jamMasuk->diffInMinutes($jamKeluar) / 60, 2),
        'jumlah_setoran' => $this->jumlah_setoran,
        'penarikan' => $this->penarikan,
    ]);

    $this->showKeluarModal = false;

    $this->reset([
        'jumlah_setoran',
        'penarikan',
    ]);

    $this->updateStatus();

    Notification::make()
        ->title('Absen Keluar berhasil!')
        ->success()
        ->send();
}

    public function render(): View
    {
        return view('filament.widgets.absen-masuk-widget');
    }
}