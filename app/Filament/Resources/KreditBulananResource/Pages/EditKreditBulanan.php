<?php

namespace App\Filament\Resources\KreditBulananResource\Pages;

use App\Filament\Resources\KreditBulananResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKreditBulanan extends EditRecord
{
    protected static string $resource = KreditBulananResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $record = $this->record;

        $plafond = (float) ($data['plafond'] ?? 0);
        $bunga = (float) ($data['bunga_persen'] ?? 0);
        $adm = (float) ($data['biaya_adm_persen'] ?? 0);
        $provisi = (float) ($data['biaya_provisi_persen'] ?? 0);
        $op = (float) ($data['biaya_op_persen'] ?? 0);

        $kyd = (float) ($data['biaya_kyd'] ?? 0);
        $materai = (float) ($data['biaya_materai'] ?? 0);
        $asuransi = (float) ($data['biaya_asuransi'] ?? 0);
        $lain = (float) ($data['biaya_lain'] ?? 0);
        $jangkaWaktu = (int) ($data['jangka_waktu'] ?? 0);

        $nominalPersen = $plafond * ($adm + $provisi + $op) / 100;
        $totalTagihan = $nominalPersen + $materai + $kyd + $asuransi + $lain;
        $angsuranPerBulan = $jangkaWaktu > 0 ? ceil((($plafond*$bunga/100) + ($plafond/$jangkaWaktu))/1000)*1000 : 0;
        $totalTerbayar = (float) $record->transaksis()->sum('nominal_bayar');
        $sisaPokok = max($plafond - $totalTerbayar, 0);

        $data['total_tagihan'] = round($totalTagihan, 2);
        $data['angsuran_per_bulan'] = round($angsuranPerBulan, 2);
        $data['sisa_pokok'] = round($sisaPokok, 2);

        if ($sisaPokok <= 0) {
            $data['status'] = 'lunas';
        }

        return $data;
    }
     protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}