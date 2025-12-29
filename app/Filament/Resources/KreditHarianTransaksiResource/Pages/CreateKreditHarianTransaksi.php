<?php

namespace App\Filament\Resources\KreditHarianTransaksiResource\Pages;

use App\Filament\Resources\KreditHarianTransaksiResource;
use App\Models\KreditHarianTransaksi;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 
use App\Models\KreditHarian;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Actions\CancelAction;



class CreateKreditHarianTransaksi extends CreateRecord
{
    protected static string $resource = KreditHarianTransaksiResource::class;
    protected static ?string $title = 'Tambah Transaksi Pinjaman';
    protected function handleRecordCreation(array $data): Model
    {
        DB::transaction(function () use ($data) {

            foreach ($data['items'] as $item) {

                $kredit = KreditHarian::lockForUpdate()
                    ->findOrFail($item['kredit_harian_id']);

                if ($item['jumlah'] > $kredit->sisa_pokok) {
                    throw new \Exception(
                        "Jumlah melebihi sisa kredit ({$kredit->no_pokok})"
                    );
                }

                KreditHarianTransaksi::create([
                    'kredit_harian_id' => $kredit->id,
                    'tanggal_transaksi' => $item['tanggal_transaksi'],
                    'jumlah' => $item['jumlah'],
                ]);

                // ⬇️ update saldo
                $kredit->decrement('sisa_pokok', $item['jumlah']);
            }
        });

        return KreditHarianTransaksi::latest()->first();
    }
    
    public static function canCreateAnother(): bool
    {
        return false;
    }
    // protected function getFormActions(): array
    // {
    //     return [
    //         CreateAction::make()
    //             ->label('Simpan Transaksi'),

    //         \Filament\Actions\Action::make('cancel')
    //             ->label('Batalkan')
    //             ->color('gray')
    //             ->url($this->getResource()::getUrl('index')),
    //     ];
    // }

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan Transaksi');
    }

    /**
     * GANTI LABEL TOMBOL BATAL
     */
    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Batalkan');
    }

}
