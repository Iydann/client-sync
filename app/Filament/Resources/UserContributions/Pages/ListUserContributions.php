<?php

namespace App\Filament\Resources\UserContributions\Pages;

use App\Filament\Resources\UserContributions\UserContributionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserContributions extends ListRecords
{
    protected static string $resource = UserContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
