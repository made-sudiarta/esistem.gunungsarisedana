<?php

namespace App\Filament\Resources\TransaksiKreditBulananResource\Pages;

use App\Filament\Resources\TransaksiKreditBulananResource;
use App\Models\TransaksiKreditBulanan;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksiKreditBulanan extends CreateRecord
{
    protected static string $resource = TransaksiKreditBulananResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $firstRecord = null;

        foreach ($data['items'] ?? [] as $item) {
            $record = TransaksiKreditBulanan::create([
                'kredit_bulanan_id' => $item['kredit_bulanan_id'],
                'tanggal_transaksi' => $data['tanggal_transaksi'],
                'saldo_awal' => TransaksiKreditBulananResource::toNumber($item['saldo_awal'] ?? 0),
                'pokok' => TransaksiKreditBulananResource::toNumber($item['pokok'] ?? 0),
                'bunga' => TransaksiKreditBulananResource::toNumber($item['bunga'] ?? 0),
                'denda' => TransaksiKreditBulananResource::toNumber($item['denda'] ?? 0),
                'nominal_bayar' => TransaksiKreditBulananResource::toNumber($item['nominal_bayar'] ?? 0),
                'sisa_saldo' => TransaksiKreditBulananResource::toNumber($item['sisa_saldo'] ?? 0),
                'keterangan' => $item['keterangan'] ?? null,
                'user_id' => auth()->id(),
            ]);

            if (! $firstRecord) {
                $firstRecord = $record;
            }
        }

        return $firstRecord ?? new TransaksiKreditBulanan();
    }
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }
    public static function canCreateAnother(): bool
    {
        return false;
    }
    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batalkan');
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}