<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Support\Contracts\TranslatableContentDriver;


class AbsenMasukWidget extends Widget implements HasActions
{
    use InteractsWithActions;

    protected static string $view = 'filament.widgets.absen-masuk-widget';

    protected static ?int $sort = 2;
    public string $todayStatus;
    public bool $showKeluarModal = false;
    public int $jumlah_setoran = 0;
    public int $penarikan = 0;

    protected int | string | array $columnSpan = [
        'default' => 12,
        'lg' => 6,
    ];
    public function makeFilamentTranslatableContentDriver(): ?TranslatableContentDriver
    {
        return null;
    }


    public function mount(): void
    {
        $this->updateStatus();
    }

    /* ================= STATUS ================= */

    public function updateStatus(): void
    {
        $absensi = Absensi::where('user_id', Auth::id())
            ->whereDate('tanggal', today())
            ->first();

        if (! $absensi) {
            $this->todayStatus = 'not_absen';
        } elseif (! $absensi->jam_keluar) {
            $this->todayStatus = 'masuk_done';
        } else {
            $this->todayStatus = 'completed';
        }
    }

    /* ================= ABSEN MASUK ================= */

    public function absenMasuk(): void
    {
        if (
            Absensi::where('user_id', Auth::id())
                ->whereDate('tanggal', today())
                ->exists()
        ) {
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
            'user_id'   => Auth::id(),
            'tanggal'   => today(),
            'jam_masuk' => $jamMasuk->format('H:i'),
        ]);

        $this->updateStatus();

        Notification::make()
            ->title('Absen Masuk berhasil!')
            ->success()
            ->send();
    }

    /* ================= MODAL ABSEN KELUAR ================= */

    protected function getActions(): array
    {
        return [
            Action::make('absenKeluar')
                ->label('ABSEN PULANG KERJA')
                ->color('danger')
                ->modalHeading('Formulir Absensi Keluar')
                ->modalDescription('Masukkan setoran dan penarikan hari ini')
                ->form([
                    \Filament\Forms\Components\TextInput::make('jumlah_setoran')
                        ->label('Setoran Hari Ini')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('penarikan')
                        ->label('Penarikan Hari Ini')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->jumlah_setoran = $data['jumlah_setoran'];
                    $this->penarikan = $data['penarikan'];

                    $this->absenKeluar();
                })
                ->visible(fn () => $this->todayStatus === 'masuk_done'),
        ];
    }


    private function handleAbsenKeluar(array $data): void
    {
        $absensi = Absensi::where('user_id', Auth::id())
            ->whereDate('tanggal', today())
            ->first();

        if (! $absensi || $absensi->jam_keluar) {
            Notification::make()
                ->title('Tidak ada absen masuk atau sudah keluar!')
                ->warning()
                ->send();
            return;
        }

        $jamMasuk = Carbon::parse($absensi->jam_masuk);
        $jamBatas = today()->setTime(16, 0);

        $jamKeluar = now()->greaterThan($jamBatas)
            ? $jamBatas
            : now();

        $absensi->update([
            'jam_keluar'     => $jamKeluar->format('H:i'),
            'jumlah_jam'     => round($jamKeluar->diffInMinutes($jamMasuk) / 60, 2),
            'jumlah_setoran' => $data['jumlah_setoran'],
            'penarikan'      => $data['penarikan'],
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
