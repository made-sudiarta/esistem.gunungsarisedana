<?php
namespace App\Livewire\Filament\Resources\SetoranResource\Pages\Components;

use App\Models\SetoranSukarela;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\TableComponent;

class SetoranSukarelaTable extends TableComponent
{
    public \App\Models\Setoran $setoran;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SetoranSukarela::query()->where('setoran_id', $this->setoran->id)
            )
            ->columns([
                Tables\Columns\TextColumn::make('sukarela.no_rek_with_group')
                    ->label('No.'),
                Tables\Columns\TextColumn::make('sukarela.members.nama_lengkap')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->formatStateUsing(fn ($state) => 'Rp. ' . number_format($state, 0, ',', '.'))
                    ->label('Jumlah (IDR)'),
                Tables\Columns\TextColumn::make('sukarela.keterangan')
                    ->label('Keterangan'),
            ]);
    }
}

