<?php

namespace App\Filament\Resources\SetoranSukarelaResource\Pages;

use App\Filament\Resources\SetoranSukarelaResource;
use App\Filament\Resources\SetoranResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetoranSukarela extends CreateRecord
{
    protected static string $resource = SetoranSukarelaResource::class;

    public ?int $setoranId = null;

    public function mount(): void
    {
        parent::mount();

        $this->setoranId = request()->get('setoran_id');

        if (!$this->setoranId) {
            abort(404, 'Setoran ID tidak ditemukan.');
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['setoran_id'] = $this->setoranId;
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return SetoranResource::getUrl('detail', ['record' => $this->setoranId]);
    }
}
