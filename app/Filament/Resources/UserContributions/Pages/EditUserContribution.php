<?php

namespace App\Filament\Resources\UserContributions\Pages;

use App\Filament\Resources\UserContributions\UserContributionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUserContribution extends EditRecord
{
    protected static string $resource = UserContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
