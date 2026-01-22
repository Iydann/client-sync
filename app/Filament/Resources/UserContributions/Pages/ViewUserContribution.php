<?php

namespace App\Filament\Resources\UserContributions\Pages;

use App\Filament\Resources\UserContributions\UserContributionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUserContribution extends ViewRecord
{
    protected static string $resource = UserContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
