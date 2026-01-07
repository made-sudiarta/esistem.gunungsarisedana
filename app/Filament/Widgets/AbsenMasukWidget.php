<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Absensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;

class AbsenMasukWidget extends Widget
{
    protected static string $view = 'filament.widgets.absen-masuk-widget';
    
    protected int | string | array $columnSpan = 1;
    public $size = 'medium';
    
    protected static ?int $sort = 2;
    public $todayStatus;
    public $showKeluarModal = false;
    public $jumlah_setoran = 0;
    public $penarikan = 0;

    public function mount(): void
    {
        $this->updateStatus();
    }

    public function updateStatus(): void
    {
        $today = now()->format('Y-m-d');
        $absensi = Absensi::where('user_id', Auth::id())
            ->where('tanggal', $today)
            ->first();

        if (!$absensi) {
            $this->todayStatus = 'not_absen';
        } elseif (!$absensi->jam_keluar) {
            $this->todayStatus = 'masuk_done';
        } else {
            $this->todayStatus = 'completed';
        }
    }

    // public function absenMasuk(): void
    // {
    //     $today = now()->format('Y-m-d');

    //     $exists = Absensi::where('user_id', Auth::id())
    //         ->where('tanggal', $today)
    //         ->first();

    //     if ($exists) {
    //         Notification::make()
    //             ->title('Kamu sudah absen hari ini!')
    //             ->warning()
    //             ->send();
    //         return;
    //     }

    //     Absensi::create([
    //         'user_id' => Auth::id(),
    //         'tanggal' => $today,
    //         'jam_masuk' => now()->format('H:i'),
    //     ]);

    //     $this->updateStatus();

    //     Notification::make()
    //         ->title('Absen Masuk berhasil tercatat!')
    //         ->success()
    //         ->send();
    // }
    public function absenMasuk(): void
    {
        $today = now()->format('Y-m-d');

        $exists = Absensi::where('user_id', Auth::id())
            ->where('tanggal', $today)
            ->first();

        if ($exists) {
            Notification::make()
                ->title('Kamu sudah absen hari ini!')
                ->warning()
                ->send();
            return;
        }

        $now = now();

        // JAM MASUK DITENTUKAN
        $jamMasuk = $now->lt(Carbon::createFromTime(8, 0))
            ? Carbon::createFromTime(8, 0)
            : $now;

        Absensi::create([
            'user_id'   => Auth::id(),
            'tanggal'   => $today,
            'jam_masuk' => $jamMasuk->format('H:i'),
        ]);

        $this->updateStatus();

        Notification::make()
            ->title('Absen Masuk berhasil tercatat!')
            ->success()
            ->send();
    }


    // public function absenKeluar(): void
    // {
    //     if ($this->jumlah_setoran < 0 || $this->penarikan < 0) {
    //         Notification::make()
    //             ->title('Setoran dan Penarikan harus >= 0')
    //             ->danger()
    //             ->send();
    //         return;
    //     }

    //     $today = now()->format('Y-m-d');
    //     $absensi = Absensi::where('user_id', Auth::id())
    //         ->where('tanggal', $today)
    //         ->first();

    //     if (!$absensi || $absensi->jam_keluar) {
    //         Notification::make()
    //             ->title('Tidak ada absen masuk atau sudah keluar!')
    //             ->warning()
    //             ->send();
    //         return;
    //     }

    //     $jam_masuk = Carbon::parse($absensi->jam_masuk);
    //     $jam_keluar = now();

    //     $absensi->update([
    //         'jam_keluar' => $jam_keluar->format('H:i'),
    //         'jumlah_jam' => round($jam_keluar->diffInMinutes($jam_masuk) / 60, 2),
    //         'jumlah_setoran' => $this->jumlah_setoran,
    //         'penarikan' => $this->penarikan,
    //     ]);

    //     $this->showKeluarModal = false;
    //     $this->jumlah_setoran = 0;
    //     $this->penarikan = 0;

    //     $this->updateStatus();

    //     Notification::make()
    //         ->title('Absen Keluar berhasil!')
    //         ->success()
    //         ->send();
    // }
    public function absenKeluar(): void
    {
        if ($this->jumlah_setoran < 0 || $this->penarikan < 0) {
            Notification::make()
                ->title('Setoran dan Penarikan harus >= 0')
                ->danger()
                ->send();
            return;
        }

        $today = now()->format('Y-m-d');
        $absensi = Absensi::where('user_id', Auth::id())
            ->where('tanggal', $today)
            ->first();

        if (! $absensi || $absensi->jam_keluar) {
            Notification::make()
                ->title('Tidak ada absen masuk atau sudah keluar!')
                ->warning()
                ->send();
            return;
        }

        $now = now();
        $jamBatasPulang = Carbon::createFromTime(16, 0);

        // JAM KELUAR DITENTUKAN
        $jamKeluar = $now->gt($jamBatasPulang)
            ? $jamBatasPulang
            : $now;

        $jamMasuk = Carbon::parse($absensi->jam_masuk);

        $absensi->update([
            'jam_keluar'      => $jamKeluar->format('H:i'),
            'jumlah_jam'      => round($jamKeluar->diffInMinutes($jamMasuk) / 60, 2),
            'jumlah_setoran'  => $this->jumlah_setoran,
            'penarikan'       => $this->penarikan,
        ]);

        $this->showKeluarModal = false;
        $this->jumlah_setoran = 0;
        $this->penarikan = 0;

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
