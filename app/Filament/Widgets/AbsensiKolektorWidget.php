<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AbsensiKolektorWidget extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.absensi-kolektor-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('Kolektor') ?? false;
    }

    public function getAbsensiHariIni(): ?object
    {
        return DB::table('absensis')
            ->whereNull('deleted_at')
            ->where('user_id', Auth::id())
            ->where('tanggal', today()->toDateString())
            ->first();
    }

    public function absenMasukAction(): Action
    {
        return Action::make('absenMasuk')
            ->label('Absen Masuk')
            ->icon('heroicon-m-arrow-right-on-rectangle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Absen Masuk')
            ->modalDescription('Apakah Anda yakin ingin melakukan absen masuk hari ini?')
            ->modalSubmitActionLabel('Ya, Absen Masuk')
            ->disabled(fn (): bool => filled($this->getAbsensiHariIni()?->jam_masuk))
            ->action(function (): void {
                $today = today()->toDateString();
                $now = now()->format('H:i:s');
                $userId = Auth::id();

                $absensi = DB::table('absensis')
                    ->whereNull('deleted_at')
                    ->where('user_id', $userId)
                    ->where('tanggal', $today)
                    ->first();

                if ($absensi && $absensi->jam_masuk) {
                    Notification::make()
                        ->title('Anda sudah absen masuk hari ini.')
                        ->warning()
                        ->send();

                    return;
                }

                if ($absensi) {
                    DB::table('absensis')
                        ->where('id', $absensi->id)
                        ->update([
                            'jam_masuk' => $now,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('absensis')->insert([
                        'user_id' => $userId,
                        'tanggal' => $today,
                        'jam_masuk' => $now,
                        'jam_keluar' => null,
                        'jumlah_setoran' => 0,
                        'penarikan' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                Notification::make()
                    ->title('Absen masuk berhasil.')
                    ->success()
                    ->send();
            });
    }

    public function absenKeluarAction(): Action
    {
        return Action::make('absenKeluar')
            ->label('Absen Keluar')
            ->icon('heroicon-m-arrow-left-on-rectangle')
            ->color('danger')
            ->modalHeading('Absen Keluar')
            ->modalSubmitActionLabel('Simpan Absen Keluar')
            ->disabled(fn (): bool => blank($this->getAbsensiHariIni()?->jam_masuk) || filled($this->getAbsensiHariIni()?->jam_keluar))
            ->form([
                TextInput::make('jumlah_setoran')
                    ->label('Jumlah Setoran Hari Ini')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->default(0),

                TextInput::make('penarikan')
                    ->label('Jumlah Penarikan Hari Ini')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->default(0),
            ])
            ->action(function (array $data): void {
                $today = today()->toDateString();
                $userId = Auth::id();

                $absensi = DB::table('absensis')
                    ->whereNull('deleted_at')
                    ->where('user_id', $userId)
                    ->where('tanggal', $today)
                    ->first();

                if (! $absensi || ! $absensi->jam_masuk) {
                    Notification::make()
                        ->title('Anda belum absen masuk hari ini.')
                        ->warning()
                        ->send();

                    return;
                }

                if ($absensi->jam_keluar) {
                    Notification::make()
                        ->title('Anda sudah absen keluar hari ini.')
                        ->warning()
                        ->send();

                    return;
                }

                $jamMasuk = Carbon::parse($absensi->tanggal . ' ' . $absensi->jam_masuk);
                $jamKeluar = now();

                $jumlahMenit = $jamMasuk->diffInMinutes($jamKeluar);
                $jumlahJam = round($jumlahMenit / 60, 2);

                DB::table('absensis')
                    ->where('id', $absensi->id)
                    ->update([
                        'jam_keluar' => $jamKeluar->format('H:i:s'),
                        'jumlah_jam' => $jumlahJam,
                        'jumlah_setoran' => $data['jumlah_setoran'] ?? 0,
                        'penarikan' => $data['penarikan'] ?? 0,
                        'updated_at' => now(),
                    ]);

                Notification::make()
                    ->title('Absen keluar berhasil disimpan.')
                    ->body('Total jam kerja: ' . $jumlahJam . ' jam')
                    ->success()
                    ->send();
            });
    }
}