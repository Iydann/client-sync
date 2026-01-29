<?php

namespace App\Filament\Resources\Milestones\Pages;

use App\Filament\Resources\Milestones\MilestoneResource;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMilestone extends EditRecord
{
    protected static string $resource = MilestoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Tombol Back (pojok kiri atas form)
    public function getBackUrl(): string
    {
        return ProjectResource::getUrl('view', ['record' => $this->getRecord()->project_id]);
    }

    // Redirect setelah tombol "Save" ditekan
    protected function getRedirectUrl(): string
    {
        return ProjectResource::getUrl('view', ['record' => $this->getRecord()->project_id]);
    }
}