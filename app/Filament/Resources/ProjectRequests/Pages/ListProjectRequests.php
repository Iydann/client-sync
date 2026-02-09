<?php

namespace App\Filament\Resources\ProjectRequests\Pages;

use App\Filament\Resources\ProjectRequests\ProjectRequestResource;
use App\Filament\Traits\HasGlobalYearFilter;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectRequests extends ListRecords
{
    protected static string $resource = ProjectRequestResource::class;

    use HasGlobalYearFilter;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
