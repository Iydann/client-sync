<?php

namespace App\Filament\Resources\UserContributions\Pages;

use App\Filament\Resources\UserContributions\UserContributionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserContribution extends CreateRecord
{
    protected static string $resource = UserContributionResource::class;
}
