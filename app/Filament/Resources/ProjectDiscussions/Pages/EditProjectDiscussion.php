<?php

namespace App\Filament\Resources\ProjectDiscussions\Pages;

use App\Filament\Resources\ProjectDiscussions\ProjectDiscussionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectDiscussion extends EditRecord
{
    protected static string $resource = ProjectDiscussionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
