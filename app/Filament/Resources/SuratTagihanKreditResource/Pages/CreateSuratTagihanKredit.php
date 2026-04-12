<?php

namespace App\Filament\Resources\SuratTagihanKreditResource\Pages;

use App\Filament\Resources\SuratTagihanKreditResource;
use App\Models\KreditBulanan;
use App\Models\SuratTagihanKredit;
use App\Models\SuratTagihanNomorCounter;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateSuratTagihanKredit extends CreateRecord
{
    protected static string $resource = SuratTagihanKreditResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['nomor_surat']);

        $kredit = KreditBulanan::findOrFail($data['kredit_bulanan_id']);

        $jumlahTunggakan = (int) $kredit->jumlah_tunggakan;
        $sisaTunggakanBunga = (float) ($kredit->sisa_tunggakan_bunga ?? 0);
        $bungaPerBulan = (float) $kredit->getBungaPerBulanTagihan();
        $sisaPokok = (float) $kredit->getSisaSaldo();
        $totalTunggakanBunga = ($jumlahTunggakan * $bungaPerBulan) + $sisaTunggakanBunga;

        if ($kredit->status === 'lunas') {
            Notification::make()
                ->title('Kredit ini sudah lunas.')
                ->danger()
                ->send();

            $this->halt();
        }

        if ($jumlahTunggakan <= 0 && $sisaTunggakanBunga <= 0) {
            Notification::make()
                ->title('Kredit ini tidak memiliki tunggakan bunga.')
                ->danger()
                ->send();

            $this->halt();
        }

        $existingJenis = $kredit->suratTagihans()
            ->pluck('jenis_sp')
            ->toArray();

        if (in_array($data['jenis_sp'], $existingJenis)) {
            Notification::make()
                ->title("{$data['jenis_sp']} untuk kredit ini sudah pernah dibuat.")
                ->danger()
                ->send();

            $this->halt();
        }

        if ($data['jenis_sp'] === 'SP1' && $jumlahTunggakan < 1 && $sisaTunggakanBunga <= 0) {
            Notification::make()
                ->title('SP1 hanya bisa dibuat jika ada tunggakan.')
                ->danger()
                ->send();

            $this->halt();
        }

        if ($data['jenis_sp'] === 'SP2') {
            if (! in_array('SP1', $existingJenis)) {
                Notification::make()
                    ->title('SP1 harus dibuat terlebih dahulu.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            if ($jumlahTunggakan < 2 && $sisaTunggakanBunga <= 0) {
                Notification::make()
                    ->title('SP2 hanya bisa dibuat jika tunggakan minimal 2 bulan atau masih ada sisa tunggakan bunga.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        if ($data['jenis_sp'] === 'SP3') {
            if (! in_array('SP2', $existingJenis)) {
                Notification::make()
                    ->title('SP2 harus dibuat terlebih dahulu.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            if ($jumlahTunggakan < 3 && $sisaTunggakanBunga <= 0) {
                Notification::make()
                    ->title('SP3 hanya bisa dibuat jika tunggakan minimal 3 bulan atau masih ada sisa tunggakan bunga.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        $data['no_pokok'] = $kredit->no_pokok;
        $data['jumlah_tunggakan_bunga'] = $jumlahTunggakan;
        $data['sisa_tunggakan_bunga'] = $sisaTunggakanBunga;
        $data['bunga_per_bulan'] = $bungaPerBulan;
        $data['total_tunggakan_bunga'] = $totalTunggakanBunga;
        $data['sisa_pokok_kredit'] = $sisaPokok;
        $data['tanggal_jatuh_tempo'] = $kredit->tanggal_jatuh_tempo;
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $tanggalSurat = isset($data['tanggal_surat'])
                ? Carbon::parse($data['tanggal_surat'])
                : now();

            $bulan = (int) $tanggalSurat->format('n');
            $tahun = (int) $tanggalSurat->format('Y');

            $counter = SuratTagihanNomorCounter::query()
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = SuratTagihanNomorCounter::create([
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'last_number' => 0,
                ]);

                $counter = SuratTagihanNomorCounter::query()
                    ->whereKey($counter->id)
                    ->lockForUpdate()
                    ->first();
            }

            $existingMax = SuratTagihanKredit::query()
                ->whereYear('tanggal_surat', $tahun)
                ->whereMonth('tanggal_surat', $bulan)
                ->selectRaw('MAX(CAST(SUBSTRING_INDEX(nomor_surat, "/", 1) AS UNSIGNED)) as max_no')
                ->lockForUpdate()
                ->value('max_no');

            $lastNumber = max(
                (int) ($counter->last_number ?? 0),
                (int) ($existingMax ?? 0)
            );

            $nextNumber = $lastNumber + 1;

            $romans = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
                5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
                9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
            ];

            $nomorSurat = sprintf(
                '%03d/KGS/ACR/%s/%d',
                $nextNumber,
                $romans[$bulan],
                $tahun
            );

            $counter->update([
                'last_number' => $nextNumber,
            ]);

            $data['nomor_surat'] = $nomorSurat;

            return SuratTagihanKredit::create($data);
        });
    }
}