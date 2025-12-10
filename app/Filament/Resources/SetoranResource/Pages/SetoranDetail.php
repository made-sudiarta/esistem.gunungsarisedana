<?php

namespace App\Filament\Resources\SetoranResource\Pages;

use App\Filament\Resources\SetoranResource;
use Filament\Resources\Pages\Page;       
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class SetoranDetail extends Page
{
    protected static string $resource = SetoranResource::class;

    protected static string $view = 'filament.resources.setoran-resource.pages.setoran-detail';

    protected static ?string $title = 'Setoran';
    use InteractsWithRecord;
    public int $jumlahSukarela = 0;
    public int $jumlahBeasiswa = 0;
    public int $jumlahAcrh = 0;
    public int $jumlahPokok = 0;
    public int $jumlahPenyerta = 0;
    public int $jumlahWajib = 0;
    public int $total = 0;
    
    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->jumlahSukarela = $this->record->setoranSukarelas()->sum('jumlah');
        $this->jumlahBeasiswa = 0;
        $this->jumlahAcrh = 0;
        $this->jumlahPokok = 0;
        $this->jumlahPenyerta = 0;
        $this->jumlahWajib = 0;
        $this->total = $this->jumlahSukarela + $this->jumlahBeasiswa + $this->jumlahAcrh + $this->jumlahPokok + $this->jumlahPenyerta + $this->jumlahWajib;

    }
    
}
