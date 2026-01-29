<?php

namespace App\Filament\Resources\Milestones\Pages;

use App\Filament\Resources\Milestones\MilestoneResource;
use App\Filament\Resources\Projects\ProjectResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewMilestone extends ViewRecord
{
    protected static string $resource = MilestoneResource::class;
    public function getBackUrl(): string
    {
        $milestone = $this->getRecord();
        
        return ProjectResource::getUrl('view', ['record' => $milestone->project_id]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back_to_project')
                ->label('Back to Project')
                ->icon('heroicon-m-arrow-left')
                ->color('gray')
                ->url($this->getBackUrl()),

            Actions\EditAction::make(),
        ];
    }
}