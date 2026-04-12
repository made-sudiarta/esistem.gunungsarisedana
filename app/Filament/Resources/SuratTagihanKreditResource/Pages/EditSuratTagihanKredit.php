<?php

namespace App\Filament\Resources\SuratTagihanKreditResource\Pages;

use App\Filament\Resources\SuratTagihanKreditResource;
use App\Models\KreditBulanan;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSuratTagihanKredit extends EditRecord
{
    protected static string $resource = SuratTagihanKreditResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
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

        $duplikat = $kredit->suratTagihans()
            ->where('jenis_sp', $data['jenis_sp'])
            ->where('id', '!=', $this->record->id)
            ->exists();

        if ($duplikat) {
            Notification::make()
                ->title("{$data['jenis_sp']} untuk kredit ini sudah pernah dibuat.")
                ->danger()
                ->send();

            $this->halt();
        }

        $data['no_pokok'] = $kredit->no_pokok;
        $data['jumlah_tunggakan_bunga'] = $jumlahTunggakan;
        $data['sisa_tunggakan_bunga'] = $sisaTunggakanBunga;
        $data['bunga_per_bulan'] = $bungaPerBulan;
        $data['total_tunggakan_bunga'] = $totalTunggakanBunga;
        $data['sisa_pokok_kredit'] = $sisaPokok;
        $data['tanggal_jatuh_tempo'] = $kredit->tanggal_jatuh_tempo;
        $data['nomor_surat'] = $this->record->nomor_surat;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('cetak')
                ->label('Cetak')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(route('surat-tagihan-kredit.pdf', ['record' => $this->record]))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->hasRole('super_admin')),
        ];
    }
}