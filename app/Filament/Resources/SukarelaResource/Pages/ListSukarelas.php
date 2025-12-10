<?php

namespace App\Filament\Resources\SukarelaResource\Pages;

use App\Filament\Resources\SukarelaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Group;
use App\Models\Sukarela;


class ListSukarelas extends ListRecords
{
    protected static string $resource = SukarelaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Semua')
                ->badge(Sukarela::count()),
        ];

        foreach (Group::all() as $group) {
            $tabs[$group->id] = Tab::make($group->group)
                ->badge($group->sukarelas()->count())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('group_id', $group->id));
        }

        return $tabs;
    }


}
