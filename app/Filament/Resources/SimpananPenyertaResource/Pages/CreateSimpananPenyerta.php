<?php

namespace App\Filament\Resources\SimpananPenyertaResource\Pages;

use App\Filament\Resources\SimpananPenyertaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSimpananPenyerta extends CreateRecord
{
    protected static string $resource = SimpananPenyertaResource::class;
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $firstRecord = null;

        foreach ($data['transactions'] as $trx) {
            $record = static::getModel()::create([
                'tanggal_trx' => $data['tanggal_trx'],
                'member_id'   => $trx['member_id'],
                'kredit'      => $trx['kredit'] ?? 0,
                'debit'       => $trx['debit'] ?? 0,
                'keterangan'  => $trx['keterangan'] ?? null,
            ]);

            $firstRecord ??= $record;
        }

        return $firstRecord;
    }
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
