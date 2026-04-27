<?php

namespace App\Filament\Widgets;

use App\Models\Master;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class AbsensiStaffWidget extends Widget
{
    protected static string $view = 'filament.widgets.absensi-staff-widget';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = [
        'default' => 1,
        'md' => 2,
        'lg' => 2,
        'xl' => 2,
    ];

    public static function canView(): bool
    {
        return Auth::check() && ! Auth::user()?->hasAnyRole(['Kolektor', 'super_admin']);
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

    #[On('staff-absen-masuk-dengan-lokasi')]
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
                    'sumber_absen' => 'widget_staff',
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
                'sumber_absen' => 'widget_staff',
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

    #[On('staff-absen-keluar-dengan-lokasi')]
    public function absenKeluarDenganLokasi(float $latitude, float $longitude): void
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
                'jumlah_setoran' => 0,
                'penarikan' => 0,
                'latitude_keluar' => $latitude,
                'longitude_keluar' => $longitude,
                'jarak_keluar' => $lokasi['jarak'],
                'sumber_absen' => 'widget_staff',
                'updated_at' => now(),
            ]);

        Notification::make()
            ->title('Absen keluar berhasil.')
            ->body('Jam keluar tercatat: ' . $jamKeluar->format('H:i:s') . '. Total jam kerja: ' . $jumlahJam . ' jam. Jarak dari kantor: ' . $lokasi['jarak'] . ' meter.')
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }
}