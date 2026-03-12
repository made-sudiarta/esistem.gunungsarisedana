<?php

namespace App\Filament\Resources\SimpananWajibResource\Pages;

use App\Filament\Resources\SimpananWajibResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\TrxSimpananWajib;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateSimpananWajib extends CreateRecord
{
    protected static string $resource = SimpananWajibResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $firstRecord = null;

            foreach ($data['transactions'] as $trx) {
                $record = TrxSimpananWajib::create([
                    'tanggal_trx' => $data['tanggal_trx'],
                    'member_id'   => $trx['member_id'],
                    'kredit'      => $trx['kredit'] ?? 0,
                    'debit'       => $trx['debit'] ?? 0,
                    'keterangan'  => $trx['keterangan'] ?? null,
                ]);

                $firstRecord ??= $record;
            }

            return $firstRecord;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
   
}
