<?php

namespace App\Filament\Widgets;

use App\Models\Master;
use Carbon\Carbon;
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
use Livewire\Attributes\On;

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

    public ?float $latitudeKeluar = null;

    public ?float $longitudeKeluar = null;

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

    private function getJamMasukTercatat(): Carbon
    {
        $jamAktual = now();
        $batasJamMasuk = today()->setTime(8, 0, 0);

        if ($jamAktual->lt($batasJamMasuk)) {
            return $batasJamMasuk;
        }

        return $jamAktual;
    }

    private function getJamKeluarTercatat(): Carbon
    {
        $jamAktual = now();

        $batasAwal = today()->setTime(8, 0, 0);
        $batasAkhir = today()->setTime(16, 0, 0);

        if ($jamAktual->lt($batasAwal)) {
            return $batasAwal;
        }

        if ($jamAktual->gt($batasAkhir)) {
            return $batasAkhir;
        }

        return $jamAktual;
    }

    private function hitungJumlahJamKerja(Carbon $jamMasuk, Carbon $jamKeluar): float
    {
        if ($jamKeluar->lessThanOrEqualTo($jamMasuk)) {
            return 0;
        }

        $jumlahMenit = $jamMasuk->diffInMinutes($jamKeluar);

        return round($jumlahMenit / 60, 2);
    }

    private function hitungJarakMeter(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): float {
        $earthRadius = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function validasiLokasiKantor(float $latitude, float $longitude): array
    {
        $officeLat = (float) Master::getValue('absensi', 'kantor_latitude');
        $officeLng = (float) Master::getValue('absensi', 'kantor_longitude');
        $allowedRadius = (int) Master::getValue('absensi', 'radius_meter', 100);

        if (! $officeLat || ! $officeLng) {
            return [
                'valid' => false,
                'jarak' => null,
                'message' => 'Lokasi kantor belum diatur.',
            ];
        }

        $jarak = $this->hitungJarakMeter(
            $officeLat,
            $officeLng,
            $latitude,
            $longitude
        );

        return [
            'valid' => $jarak <= $allowedRadius,
            'jarak' => round($jarak),
            'message' => null,
        ];
    }

    #[On('absen-masuk-dengan-lokasi')]
    public function absenMasukDenganLokasi(float $latitude, float $longitude): void
    {
        $lokasi = $this->validasiLokasiKantor($latitude, $longitude);

        if (! $lokasi['valid']) {
            Notification::make()
                ->title('Lokasi di luar area kantor.')
                ->body(
                    $lokasi['message']
                        ?? 'Jarak Anda sekitar ' . $lokasi['jarak'] . ' meter dari kantor.'
                )
                ->danger()
                ->send();

            return;
        }

        $today = today()->toDateString();
        $userId = Auth::id();

        $jamMasukTercatat = $this->getJamMasukTercatat();
        $jamMasuk = $jamMasukTercatat->format('H:i:s');

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
                    'jam_masuk' => $jamMasuk,
                    'latitude_masuk' => $latitude,
                    'longitude_masuk' => $longitude,
                    'jarak_masuk' => $lokasi['jarak'],
                    'sumber_absen' => 'widget_kolektor',
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('absensis')->insert([
                'user_id' => $userId,
                'tanggal' => $today,
                'jam_masuk' => $jamMasuk,
                'jam_keluar' => null,
                'jumlah_jam' => 0,
                'jumlah_setoran' => 0,
                'penarikan' => 0,
                'latitude_masuk' => $latitude,
                'longitude_masuk' => $longitude,
                'jarak_masuk' => $lokasi['jarak'],
                'latitude_keluar' => null,
                'longitude_keluar' => null,
                'jarak_keluar' => null,
                'sumber_absen' => 'widget_kolektor',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Notification::make()
            ->title('Absen masuk berhasil.')
            ->body('Jam masuk tercatat: ' . $jamMasuk . '. Jarak dari kantor: ' . $lokasi['jarak'] . ' meter.')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    #[On('set-lokasi-keluar')]
    public function setLokasiKeluar(float $latitude, float $longitude): void
    {
        $this->latitudeKeluar = $latitude;
        $this->longitudeKeluar = $longitude;

        $this->mountAction('absenKeluar');
    }

    public function absenMasukAction(): Action
    {
        return Action::make('absenMasuk')
            ->label('Absen Masuk')
            ->icon('heroicon-m-arrow-right-on-rectangle')
            ->color('primary')
            ->disabled(fn (): bool => filled($this->getAbsensiHariIni()?->jam_masuk));
    }

    public function absenKeluarAction(): Action
    {
        return Action::make('absenKeluar')
            ->label('Absen Keluar')
            ->icon('heroicon-m-arrow-left-on-rectangle')
            ->color('warning')
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
                if (! $this->latitudeKeluar || ! $this->longitudeKeluar) {
                    Notification::make()
                        ->title('Lokasi belum terbaca.')
                        ->body('Silakan izinkan akses lokasi lalu coba lagi.')
                        ->danger()
                        ->send();

                    return;
                }

                $lokasi = $this->validasiLokasiKantor(
                    $this->latitudeKeluar,
                    $this->longitudeKeluar
                );

                if (! $lokasi['valid']) {
                    Notification::make()
                        ->title('Lokasi di luar area kantor.')
                        ->body(
                            $lokasi['message']
                                ?? 'Jarak Anda sekitar ' . $lokasi['jarak'] . ' meter dari kantor.'
                        )
                        ->danger()
                        ->send();

                    return;
                }

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
                $jamKeluar = $this->getJamKeluarTercatat();

                $jumlahJam = $this->hitungJumlahJamKerja($jamMasuk, $jamKeluar);

                DB::table('absensis')
                    ->where('id', $absensi->id)
                    ->update([
                        'jam_keluar' => $jamKeluar->format('H:i:s'),
                        'jumlah_jam' => $jumlahJam,
                        'jumlah_setoran' => $data['jumlah_setoran'] ?? 0,
                        'penarikan' => $data['penarikan'] ?? 0,
                        'latitude_keluar' => $this->latitudeKeluar,
                        'longitude_keluar' => $this->longitudeKeluar,
                        'jarak_keluar' => $lokasi['jarak'],
                        'sumber_absen' => 'widget_kolektor',
                        'updated_at' => now(),
                    ]);

                $this->latitudeKeluar = null;
                $this->longitudeKeluar = null;

                Notification::make()
                    ->title('Absen keluar berhasil disimpan.')
                    ->body('Jam keluar tercatat: ' . $jamKeluar->format('H:i:s') . '. Total jam kerja: ' . $jumlahJam . ' jam. Jarak dari kantor: ' . $lokasi['jarak'] . ' meter.')
                    ->success()
                    ->send();

                $this->dispatch('$refresh');
            });
    }
}