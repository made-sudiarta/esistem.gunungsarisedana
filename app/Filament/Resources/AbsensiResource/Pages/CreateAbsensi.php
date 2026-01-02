<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use App\Filament\Resources\AbsensiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use App\Models\Absensi;

class CreateAbsensi extends CreateRecord
{
    protected static string $resource = AbsensiResource::class;
    protected static ?string $title = 'Absensi Baru';

    public function mount(): void
    {
        parent::mount();

        // Super admin bebas
        if (auth()->user()->hasRole('super_admin')) {
            return;
        }

        $sudahAbsen = Absensi::where('user_id', auth()->id())
            ->whereDate('tanggal', Carbon::today())
            ->exists();

        if ($sudahAbsen) {
            Notification::make()
                ->title('Absensi Gagal')
                ->body('Anda sudah melakukan absensi hari ini.')
                ->danger()
                ->persistent()
                ->send();

            // Redirect balik ke table
            redirect(AbsensiResource::getUrl('index'));
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     if (! auth()->user()->hasRole('super_admin')) {
    //         $data['user_id'] = auth()->id();
    //     }

    //     return $data;
    // }
    // protected function getRedirectUrl(): string
    // {
    //     return static::$resource::getUrl('index');
    // }
    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan Absensi');
    }
    public static function canCreateAnother(): bool
    {
        return false;
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
