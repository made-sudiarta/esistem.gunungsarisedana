<?php

namespace App\Filament\Resources\MemberResource\Pages;

use App\Filament\Resources\MemberResource;
use Filament\Resources\Pages\Page;
use App\Models\Member;

class IdCardMember extends Page
{
    protected static string $resource = MemberResource::class;

    protected static string $view = 'filament.resources.member-resource.pages.id-card-member';

    public $record;

    public function mount($record): void
    {
        $this->record = Member::findOrFail($record);
    }
}
